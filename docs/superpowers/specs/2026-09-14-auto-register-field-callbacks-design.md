# Contao 6 Vorbereitung: Toolkit-DCA-Callbacks automatisch registrieren

Status: approved
Datum: 2026-09-14

## Kontext

Elfter Einzelpunkt der Contao-6-Migrationsreihe (siehe
`2026-09-14-twig-template-compat-design.md`,
`2026-09-14-request-scope-matcher-deprecation-design.md`,
`2026-09-14-fragment-controller-modernization-design.md`,
`2026-09-14-insert-tag-deprecation-design.md`,
`2026-09-14-dca-wizard-listener-deprecation-design.md`,
`2026-09-14-template-options-listener-finder-design.md`,
`2026-09-14-generate-alias-listener-slug-design.md`,
`2026-09-14-backend-frontend-user-factory-deprecation-design.md`,
`2026-09-14-response-tagger-deprecation-design.md` und
`2026-09-14-render-backend-view-trait-deprecation-design.md` für die vorherigen Punkte und
die generelle Arbeitsweise).

Aktuell muss ein Konsument jeden Toolkit-DCA-Callback manuell zweimal konfigurieren:
einmal inhaltlich über den `toolkit.<key>`-Unterschlüssel eines Felds (z. B.
`toolkit.template_options.prefix`) und ein zweites Mal strukturell über den passenden
Contao-Callback-Slot (z. B. `'options_callback' => [TemplateOptionsListener::class,
'onOptionsCallback']`). Ziel dieses Punkts: Ist der `toolkit.<key>`-Unterschlüssel eines
Felds gesetzt, soll der passende Callback automatisch registriert werden — aber
ausdrücklich nur für die Listener, die nach Abschluss dieser Migrationsreihe erhalten
bleiben (Punkte 1–10), nicht für deprecateten Code.

## Analyse

Alle verbleibenden Toolkit-DCA-Callbacks lesen ihre Konfiguration bereits einheitlich aus
`fields.<field>.toolkit.<key>` (`toolkit` als Sibling-Key von `eval`, keine
`eval.toolkit.*`-Verschachtelung):

| Config-Key | DCA-Slot | Ziel-Service (Methode) |
|---|---|---|
| `toolkit.template_options` | `options_callback` | `TemplateOptionsListener::onOptionsCallback` |
| `toolkit.alias_generator` | `save_callback` | `SlugAliasListener::onSaveCallback` (neu, Punkt 7) |
| `toolkit.popup_wizard` | `wizard` | `PopupWizardListener::onWizardCallback` |

Geprüft gegen Contaos tatsächliche Callback-Dispatch-Logik — die drei Slot-Typen
verhalten sich unterschiedlich:

- `options_callback` (`Widget.php:1305`): wird als **einzelnes** `[class, method]`-Paar
  behandelt (`is_array($arrData['options_callback'])`, kein Loop über mehrere Einträge).
  Ein zweiter Eintrag ist nicht vorgesehen — eine Auto-Registrierung darf einen bereits
  manuell gesetzten `options_callback` **nicht** überschreiben.
- `save_callback` und `wizard` (`DataContainer.php:638`, analog für `save_callback` in
  `DatabaseRowUpdater`/Contao Core): beides sind **Arrays**, mehrere Einträge werden der
  Reihe nach ausgeführt/verkettet. Ein Toolkit-Callback kann hier sicher ergänzt werden,
  ohne bestehende manuelle Einträge zu verdrängen.

Als Hook-Mechanismus existiert bereits ein direktes Vorbild:
`Dca\Listener\SetOperationDataAttributeListener` — hängt an `loadDataContainer`, liest
einen `toolkit`-Unterschlüssel (dort unter `list.operations.<name>.toolkit`) und mutiert
die Definition darauf basierend. Dasselbe Muster, nur auf `fields.*.toolkit.*`
angewendet, deckt diesen Punkt ab. `Dca\Definition::modify(path, handler)` bietet exakt
die nötige Read-Modify-Write-Operation für den Array-Fall.

## Entscheidung

**Mechanik: Tagged-Service-Konvention**, nicht eine hart codierte Zuordnungstabelle in
einer einzelnen Klasse — analog zum bereits etablierten Formatter-Tag-Muster
(`netzmacht.contao_toolkit.dca.formatter`, eingesammelt von `CreateFormatterSubscriber`).

Neuer Service-Tag `netzmacht.contao_toolkit.dca.auto_callback` mit den Attributen `key`
(der `toolkit.<key>`-Unterschlüssel), `slot` (Contao-DCA-Callback-Slot) und `method`. Die
drei verbleibenden Listener deklarieren sich selbst:

```yaml
Netzmacht\Contao\Toolkit\Dca\Listener\Options\TemplateOptionsListener:
  tags:
    - { name: 'netzmacht.contao_toolkit.dca.auto_callback', key: 'template_options', slot: 'options_callback', method: 'onOptionsCallback' }

Netzmacht\Contao\Toolkit\Dca\Listener\Save\SlugAliasListener:
  tags:
    - { name: 'netzmacht.contao_toolkit.dca.auto_callback', key: 'alias_generator', slot: 'save_callback', method: 'onSaveCallback' }

Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\PopupWizardListener:
  tags:
    - { name: 'netzmacht.contao_toolkit.dca.auto_callback', key: 'popup_wizard', slot: 'wizard', method: 'onWizardCallback' }
```

Ein neuer Compiler-Pass `DependencyInjection\Compiler\RegisterFieldCallbacksPass`
(analog zu `FosCacheResponseTaggerPass`) sammelt die getaggten Services per
`$container->findTaggedServiceIds(...)` zu einer Config-Map
(`[key => ['slot' => ..., 'service' => $serviceId, 'method' => ...]]`) und injiziert sie
als Konstruktor-Argument in einen neuen Listener `Dca\Listener\RegisterFieldCallbacksListener`.

`RegisterFieldCallbacksListener` hängt — wie `SetOperationDataAttributeListener` — an
`loadDataContainer` (mit derselben `RequestScopeMatcher`-Guard-Bedingung), iteriert über
`fields.*.toolkit.*` und registriert je gefundenem bekannten Key den passenden Callback:

```php
private const LIST_SLOTS = ['save_callback', 'wizard'];

private function registerCallback(Definition $definition, string $field, array $callback): void
{
    $path  = ['fields', $field, $callback['slot']];
    $entry = [$callback['service'], $callback['method']];

    if (in_array($callback['slot'], self::LIST_SLOTS, true)) {
        $definition->modify($path, static function (mixed $current) use ($entry): array {
            $current = (array) $current;

            if (in_array($entry, $current, true)) {
                return $current;
            }

            return [...$current, $entry];
        });

        return;
    }

    // Einzel-Slot (z. B. options_callback): nie einen bereits gesetzten Callback ersetzen.
    if ($definition->has($path)) {
        return;
    }

    $definition->set($path, $entry);
}
```

Die Service-Id im Callback-Eintrag ist die FQCN des jeweiligen Listeners (bereits heute
die tatsächliche Service-Id in `listeners.yml`) — verifiziert gegen Contaos
`System::importStatic()`: Klassennamen-Strings werden dort über
`$container->has($strClass) && $container->get($strClass)` aufgelöst, solange der
Service `public: true` ist (bereits der Fall für alle drei Listener).

**Bewusst nur für die drei oben genannten, verbleibenden Listener** — deprecatete
Listener (`GenerateAliasListener`, `StateButtonCallbackListener`, `ColorPickerListener`,
`FilePickerListener`, `PagePickerListener`) bekommen **keinen** `auto_callback`-Tag und
damit keine Auto-Registrierung; ihre bestehende, rein manuelle Registrierung bleibt
unverändert bestehen (kein zusätzlicher Anreiz, deprecateten Code weiter zu nutzen).

## Änderungen Version 4.1.0 (Kompatibilitätsschicht)

- Neuer Service-Tag `netzmacht.contao_toolkit.dca.auto_callback`.
- Neu: `DependencyInjection\Compiler\RegisterFieldCallbacksPass`,
  `Dca\Listener\RegisterFieldCallbacksListener`.
- `services.yml`/`listeners.yml`: `TemplateOptionsListener`, `SlugAliasListener` (Punkt 7),
  `PopupWizardListener` erhalten den neuen Tag.
- `NetzmachtContaoToolkitBundle.php`: Registrierung des neuen Compiler-Passes.
- Dokumentation: neue Seite, die die `toolkit.<key>`-Konvention und die
  Auto-Registrierung erklärt; bestehende Beispiele mit manueller
  `options_callback`/`save_callback`/`wizard`-Registrierung werden als weiterhin
  funktionsfähig, aber nicht mehr nötig markiert (kein Deprecation-Hinweis — die manuelle
  Registrierung bleibt ein gültiger, expliziter Override-Mechanismus, siehe unten).
- `CHANGELOG.md`: Eintrag unter `[4.1.0]`.

## Änderungen Version 5.0.0 (Zielbild, Contao 6)

- Keine strukturelle Änderung — dieselbe Tagged-Service-Konvention bleibt gültig. Beim
  Entfernen der deprecateten Listener (Punkte 5, 7) entfallen automatisch auch etwaige
  (nicht vorgesehene) Tags auf ihnen; die drei verbleibenden Listener behalten ihre Tags
  unverändert.

## Bewusst nicht Teil dieses Themas

- Keine Auto-Registrierung für `AbstractPickerListener`/`AbstractFieldPickerListener` —
  das sind generische Basisklassen für projektspezifische Subklassen ohne festen
  Service-Namen/feste Methode, passen nicht in das Tag-Schema (siehe
  `2026-09-14-dca-wizard-listener-deprecation-design.md`).
- Kein Konflikt-Hinweis/keine Exception, wenn sowohl `toolkit.template_options` als auch
  ein manueller `options_callback` gleichzeitig gesetzt sind — der manuelle Callback
  gewinnt stillschweigend (siehe Entscheidung). Kann bei Bedarf als eigener, späterer
  Punkt ergänzt werden (z. B. ein `trigger_deprecation()`- oder Log-Hinweis bei
  erkanntem Konflikt).

## Testing

- Neuer Spec-Fall: `RegisterFieldCallbacksListener` registriert `options_callback` für
  ein Feld mit `toolkit.template_options`, sofern noch kein `options_callback` gesetzt
  ist.
- Neuer Spec-Fall: bereits vorhandener `options_callback` wird nicht überschrieben.
- Neuer Spec-Fall: `save_callback`/`wizard` werden ergänzt (bestehende Einträge bleiben
  erhalten), mehrfacher `loadDataContainer`-Aufruf führt nicht zu doppelten Einträgen.
- Neuer Spec-Fall: `RegisterFieldCallbacksPass` baut die Config-Map korrekt aus den
  getaggten Services.
