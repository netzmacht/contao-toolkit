# Contao 6 Vorbereitung: `TemplateOptionsListener` um moderne Fragment-Template-Identifier erweitern

Status: approved
Datum: 2026-09-14

## Kontext

Sechster Einzelpunkt der Contao-6-Migrationsreihe (siehe
`2026-09-14-twig-template-compat-design.md`,
`2026-09-14-request-scope-matcher-deprecation-design.md`,
`2026-09-14-fragment-controller-modernization-design.md`,
`2026-09-14-insert-tag-deprecation-design.md` und
`2026-09-14-dca-wizard-listener-deprecation-design.md` für die vorherigen Punkte und die
generelle Arbeitsweise).

`Netzmacht\Contao\Toolkit\Dca\Listener\Options\TemplateOptionsListener` ist ein
`options_callback` für `customTpl`-artige Auswahlfelder: Er ruft
`Contao\Controller::getTemplateGroup($config['prefix'])` auf und liefert (abzüglich
eines konfigurierbaren `exclude`) die Liste passender Templates.

Dieser Punkt greift eine in `2026-09-14-twig-template-compat-design.md` unter "Bewusst
nicht Teil dieses Themas" zurückgestellte Frage auf: Wie wird `customTpl` für moderne
(Twig-)Fragment-Templates aufgelöst? Sie betrifft direkt auch die neuen
`Controller\Fragment\*`-Basisklassen aus
`2026-09-14-fragment-controller-modernization-design.md`, deren `customTpl`-Resolving
über Contaos modernen Identifier-Stil (`content_element/text` statt `ce_text`) läuft.

## Analyse

`Controller::getTemplateGroup()` ist **nicht deprecated** — im `6.0`-Branch von
`contao/contao` (direkt geprüft, nicht nur an der installierten 5.7.13) existiert die
Methode nahezu unverändert weiter. Einziger Unterschied: Der Merge mit dem alten
`TemplateLoader::getPrefixedFiles()` (Legacy-PHP-Template-Loader) entfällt in 6.0, die
Methode arbeitet dort ausschließlich über `contao.twig.filesystem_loader`. Für klassische
Prefixe (`ce_`, `mod_`, `form_`, …) bleibt `TemplateOptionsListener` also unverändert
funktionsfähig.

Für moderne, namespaced Fragment-Template-Identifier (`content_element/text`) versagt die
Methode jedoch explizit:

```php
if (str_contains($strPrefix, '/') || str_contains($strDefaultTemplate, '/')) {
    throw new \InvalidArgumentException(
        'Using getTemplateGroup() with modern fragment templates is not supported. '
        . 'Use the "contao.twig.finder_factory" service instead.'
    );
}
```

Contao bietet dafür `Contao\CoreBundle\Twig\Finder\FinderFactory` (Service-ID
`contao.twig.finder_factory`, autowireable über den Klassennamen). Die erzeugte
`Finder`-Instanz kennt eine dedizierte `withVariants()`-Methode, die exakt das
"Sibling-Template"-Konzept der klassischen `getTemplateGroup()`-Prefix-Suche abbildet:
gefiltert nach einem exakten Basis-Identifier (`content_element/text`) werden zusätzlich
alle Varianten-Templates (`content_element/text/custom1`, `.../special`, …) eingeschlossen
— das Twig-native Pendant zu `ce_text`, `ce_text_custom1`, `ce_text_special` im klassischen
System. `asIdentifierList()` liefert eine `list<string>` — dieselbe Rückgabeform wie
`getTemplateGroup()`, sodass der bestehende `exclude`-Mechanismus (`array_diff`)
unverändert weiterverwendet werden kann. Contaos `Finder::asTemplateOptions()` (mit
Quellen-Labels, Theme-Support, übersetzten Custom-Labels) wäre eine mächtigere
Alternative, würde aber das UI-Format des `options_callback` ändern — bewusst nicht
übernommen, um das bestehende Verhalten für Konsumenten nicht sichtbar zu verändern.

`FinderFactory` ist unter der neuen Mindestanforderung `contao/core-bundle: ^5.7`
garantiert vorhanden, keine bedingte Abhängigkeit nötig.

## Entscheidung

`TemplateOptionsListener` wird **erweitert, nicht deprecated**. Er unterscheidet anhand
des konfigurierten `prefix` zwischen klassischem und modernem Identifier-Stil:

```php
public function onOptionsCallback(DataContainer $dataContainer): array
{
    $config    = $this->getConfig($dataContainer);
    $templates = str_contains($config['prefix'], '/')
        ? $this->finderFactory->create()->identifier($config['prefix'])->withVariants()->asIdentifierList()
        : Controller::getTemplateGroup($config['prefix']);

    if (empty($config['exclude'])) {
        return $templates;
    }

    return array_values(array_map('\strval', array_diff($templates, $config['exclude'])));
}
```

Der Konstruktor erhält einen neuen Pflicht-Parameter
`Contao\CoreBundle\Twig\Finder\FinderFactory $finderFactory`. Das ist eine
Signaturänderung, aber unkritisch: `TemplateOptionsListener` ist `final` und ausschließlich
als DI-verdrahteter Service gedacht (Referenzierung im DCA über Service-ID, kein manuelles
`new TemplateOptionsListener(...)` durch Konsumenten vorgesehen) — analog zur bereits in
4.1 etablierten Praxis, neue Abhängigkeiten bei reinen Service-Klassen ohne
Deprecation-Zyklus zu ergänzen.

## Änderungen Version 4.1.0 (Kompatibilitätsschicht)

- `TemplateOptionsListener`: neuer Konstruktor-Parameter `FinderFactory $finderFactory`,
  `onOptionsCallback()` verzweigt anhand `str_contains($config['prefix'], '/')` zwischen
  `Controller::getTemplateGroup()` (klassisch) und `Finder::identifier(...)
  ->withVariants()->asIdentifierList()` (modern).
- Dokumentation: Hinweis, dass `template_options.prefix` jetzt auch moderne
  Fragment-Template-Identifier (`content_element/text`) unterstützt.
- `CHANGELOG.md`: Eintrag unter `[4.1.0]` — `TemplateOptionsListener` unterstützt jetzt
  moderne Twig-Fragment-Template-Identifier.

## Änderungen Version 5.0.0 (Zielbild, Contao 6)

Keine weiteren Änderungen — die in 4.1 eingeführte Logik ist bereits das Zielbild, da
`Controller::getTemplateGroup()` für klassische Prefixe auch in Contao 6 weiterbesteht
und beide Code-Pfade dauerhaft nebeneinander gültig bleiben (kein Ersetzungs-, sondern
ein Ergänzungsfall).

## Bewusst nicht Teil dieses Themas

- `Finder::asTemplateOptions()` (reichhaltigeres Label-/Theme-Format) wird nicht
  übernommen, um das bestehende UI-Verhalten nicht zu verändern. Kann bei Bedarf als
  eigener, späterer Punkt diskutiert werden.
- `GenerateAliasListener` (`src/Dca/Listener/Save/`) wird als eigener, nachfolgender
  Punkt der Migrationsreihe besprochen.

## Testing

- Neuer Spec-Fall: `prefix` ohne `/` nutzt weiterhin `Controller::getTemplateGroup()`
  (bestehendes Verhalten unverändert).
- Neuer Spec-Fall: `prefix` mit `/` nutzt `FinderFactory`/`Finder::withVariants()`,
  liefert Basis-Identifier plus Varianten als `list<string>`.
- Bestehender `exclude`-Spec-Fall bleibt für beide Pfade gültig.
