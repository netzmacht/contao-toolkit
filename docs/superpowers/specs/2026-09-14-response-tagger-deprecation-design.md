# Contao 6 Vorbereitung: `ResponseTagger`-Kapselung deprecaten

Status: approved
Datum: 2026-09-14

## Kontext

Neunter Einzelpunkt der Contao-6-Migrationsreihe (siehe
`2026-09-14-twig-template-compat-design.md`,
`2026-09-14-request-scope-matcher-deprecation-design.md`,
`2026-09-14-fragment-controller-modernization-design.md`,
`2026-09-14-insert-tag-deprecation-design.md`,
`2026-09-14-dca-wizard-listener-deprecation-design.md`,
`2026-09-14-template-options-listener-finder-design.md`,
`2026-09-14-generate-alias-listener-slug-design.md` und
`2026-09-14-backend-frontend-user-factory-deprecation-design.md` für die vorherigen
Punkte und die generelle Arbeitsweise).

Dieser Punkt greift die in `2026-09-14-fragment-controller-modernization-design.md` unter
"Bewusst nicht Teil dieses Themas" zurückgestellte Frage auf: `Response\ResponseTagger`
(inkl. `FosCacheResponseTagger`, `NoOpResponseTagger`, der Compiler-Pass
`FosCacheResponseTaggerPass`) wurde laut eigenem Interface-Docblock "as a backward
compatibility layer for Contao < 4.6" eingeführt. Ziel war laut Nutzer, dass der Aufrufer
nicht wissen muss, ob HTTP-Cache-Tagging (FOS HttpCache) überhaupt aktiviert ist.

## Analyse

Die bestehende Kapselung: `ResponseTagger`-Interface (`addTags(array $tags): void`),
`FosCacheResponseTagger` (wrapt `FOS\HttpCache\ResponseTagger`),
`NoOpResponseTagger` (Fallback, macht nichts). `FosCacheResponseTaggerPass` prüft beim
Container-Build, ob der Service `fos_http_cache.http.symfony_response_tagger` existiert,
und ersetzt dann die Definition von `netzmacht.contao_toolkit.response_tagger`
entsprechend — Default ist `NoOpResponseTagger`. `friendsofsymfony/http-cache` steht im
Toolkit nur in `require-dev`/`conflict`, nie als Laufzeit-Pflichtabhängigkeit.

Contao Core bietet inzwischen ein natives, funktional überlegenes Äquivalent:
`Contao\CoreBundle\Cache\CacheTagManager` (Service `contao.cache.tag_manager`,
autowireable über `Contao\CoreBundle\Cache\CacheTagManager: '@contao.cache.tag_manager'`).
Geprüft — identisch vorhanden sowohl in der installierten 5.7.13 als auch im `6.0`-Branch
von `contao/contao`:

```php
public function __construct(
    private readonly EntityManagerInterface $entityManager,
    private readonly EventDispatcherInterface $eventDispatcher,
    private readonly ResponseTagger|null $responseTagger = null,   // FOS' eigenes Interface
    private readonly CacheInvalidator|null $cacheInvalidator = null,
) {}

public function tagWith(array|object|string|null $target): void
{
    if (!$this->responseTagger) {
        return;
    }
    $this->responseTagger->addTags($this->getTagsFor($target));
}
```

Jede `tagWith*()`-Methode beginnt mit demselben `if (!$this->responseTagger) { return; }`
— exakt das vom Nutzer geforderte Verhalten ("Caller muss nicht wissen, ob Caching
aktiviert ist"), nativ und ohne eigenen Compiler-Pass. Zusätzlich bietet
`CacheTagManager` deutlich mehr, als unser `ResponseTagger` je abgedeckt hat:
automatische Tag-Ableitung aus Model-/Entity-Klassen, -Instanzen und -Collections
(`getTagsFor()`, `tagWithModelInstance()`, `tagWithEntityClass()`, …) sowie
Tag-Invalidierung (`invalidateTagsFor()`/`invalidateTags()`, über
`FOS\HttpCache\CacheInvalidator` und ein `InvalidateCacheTagsEvent` entkoppelt) — etwas,
das unser `ResponseTagger` mit seiner reinen `addTags()`-Methode gar nicht anbietet.

Bereits in `2026-09-14-fragment-controller-modernization-design.md` festgestellt:
`Contao\CoreBundle\Controller\AbstractController::tagResponse()` (Basis der neuen
`Controller\Fragment\*`-Klassen) delegiert direkt an `contao.cache.tag_manager`.

**Interner Nutzungskontext:** `ResponseTagger` wird toolkit-intern ausschließlich als
Pflicht-Konstruktor-Parameter der bereits (doc-only) deprecateten alten
Fragment-Controller-Basisklassen (`AbstractFragmentController`,
`ContentElement\AbstractContentElementController`,
`FrontendModule\AbstractFrontendModuleController`, `Hybrid\AbstractHybridController`)
verwendet. Gleichzeitig ist `ResponseTagger` aber auch eigenständige, öffentliche API
(Interface + Service-Alias `netzmacht.contao_toolkit.response_tagger`), die Konsumenten
unabhängig von den Fragment-Controllern direkt injizieren könnten.

## Entscheidung

`ResponseTagger`, `FosCacheResponseTagger`, `NoOpResponseTagger`,
`FosCacheResponseTaggerPass` sowie `Exception\InvalidHttpResponseTagException` (nur
innerhalb dieser Klassen verwendet) werden als Ganzes deprecated — kein Ersatz als
Toolkit-Abstraktion, Konsumenten injizieren künftig direkt
`Contao\CoreBundle\Cache\CacheTagManager`.

**Mechanik: doc-only, kein `trigger_deprecation()`.** Der dominante Nutzungspfad läuft
über die bereits doc-only-deprecateten alten Fragment-Controller-Basisklassen (siehe
`2026-09-14-fragment-controller-modernization-design.md`, dort mit derselben Begründung:
mandatorischer Konstruktor-Parameter einer unveränderten Basisklasse, die weiterhin von
jedem noch nicht migrierten Konsumenten-Controller bei jedem Request instanziiert wird).
Ein Runtime-Trigger direkt auf `ResponseTagger`/seinen Implementierungen würde bei jedem
Request jedes nicht migrierten Projekts zusätzlich zur bereits vorhandenen
Fragment-Controller-Warnung feuern, ohne zusätzlichen Erkenntniswert — Konsumenten, die
von den alten Fragment-Controllern auf `Controller\Fragment\*` umsteigen, verlieren
`ResponseTagger` als Abhängigkeit ohnehin automatisch mit.

## Änderungen Version 4.1.0 (Kompatibilitätsschicht)

- `ResponseTagger`, `FosCacheResponseTagger`, `NoOpResponseTagger`,
  `FosCacheResponseTaggerPass`, `InvalidHttpResponseTagException`: Klassen-/
  Interface-Docblock erhält `@deprecated` (Verweis auf
  `Contao\CoreBundle\Cache\CacheTagManager` als Ersatz), kein `trigger_deprecation()`.
- Dokumentation (sofern vorhanden) erhält entsprechende Deprecation-Hinweise inkl.
  Migrationsbeispiel (`$cacheTagManager->tagWith($model)` statt
  `$responseTagger->addTags([...])`).
- `CHANGELOG.md`: Eintrag unter `[4.1.0]` — Deprecation der `ResponseTagger`-Kapselung,
  Migrationsempfehlung auf `Contao\CoreBundle\Cache\CacheTagManager`.

## Änderungen Version 5.0.0 (Zielbild, Contao 6)

- Entfernt: `src/Response/` komplett (`ResponseTagger.php`, `FosCacheResponseTagger.php`,
  `NoOpResponseTagger.php`), `src/DependencyInjection/Compiler/FosCacheResponseTaggerPass.php`,
  `src/Exception/InvalidHttpResponseTagException.php`.
- Entfernt aus `services.yml`: `netzmacht.contao_toolkit.response_tagger`.
- Entfernt aus `NetzmachtContaoToolkitBundle.php`: Registrierung von
  `FosCacheResponseTaggerPass`.
- Entfernt aus `composer.json`: `require-dev`/`conflict`-Einträge für
  `friendsofsymfony/http-cache` (sofern nach Entfernung dieses Punkts nicht mehr anderweitig
  für Tests benötigt).

## Bewusst nicht Teil dieses Themas

- Keine neue Toolkit-Abstraktion als Ersatz — Konsumenten nutzen
  `Contao\CoreBundle\Cache\CacheTagManager` direkt, analog zum bereits etablierten Muster
  bei `DatabaseRowUpdater`/`ContaoServicesFactory` (Punkte 5, 8).

## Testing

- Keine neuen Spec-Fälle nötig (doc-only-Deprecation, kein neues Laufzeitverhalten).
- Bestehende Specs für `FosCacheResponseTagger`/`NoOpResponseTagger` bleiben unverändert
  (weiterhin funktionsfähiger, nur deprecateter Code).
