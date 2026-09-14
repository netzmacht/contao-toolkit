# Contao 6 Vorbereitung: Fragment-Controller-Basisklassen modernisieren

Status: approved
Datum: 2026-09-14

## Kontext

Dritter Einzelpunkt der Contao-6-Migrationsreihe (siehe
`2026-09-14-twig-template-compat-design.md` und
`2026-09-14-request-scope-matcher-deprecation-design.md` für die vorherigen Punkte und
die generelle Arbeitsweise).

Die toolkit-eigenen Fragment-Controller-Basisklassen (`Controller\AbstractFragmentController`
und die Subklassen `Controller\ContentElement\AbstractContentElementController`,
`Controller\FrontendModule\AbstractFrontendModuleController`,
`Controller\Hybrid\AbstractHybridController`) wurden laut Nutzer aus drei Gründen gebaut:

1. Kapselung der Contao-Template-Rendering-Logik.
2. Verzicht auf Symfonys Service-Subscriber-Ansatz zugunsten sichtbarer
   Konstruktor-Dependencies.
3. Besserer, hook-basierter Datenfluss bei der Template-Daten-Aufbereitung.

## Analyse

Geprüft anhand des installierten `contao/core-bundle` (5.7.13, unsere Mindestanforderung
seit `2026-09-14-twig-template-compat-design.md`):

- `Contao\CoreBundle\Controller\AbstractFragmentController` nutzt bereits
  `ServiceSubscriberInterface`/`getSubscribedServices()` statt sichtbarer
  Konstruktor-Dependencies (Grund 2 ist damit von Contao Core selbst übernommen; Nutzer
  akzeptiert diesen Ansatz inzwischen).
- Contao hat mit `createTemplate()`/`Contao\CoreBundle\Twig\FragmentTemplate` bereits
  eine eigene "moderne Fragmente"-Datenfluss-Logik: `addDefaultDataToTemplate()` baut
  einen strukturierten Kontext (`type`, `template`, `data`, `section`, `properties`,
  `element_html_id`, `element_css_classes`, `headline`) auf. `createTemplate()` löst
  `customTpl` bereits selbst auf, inkl. Legacy/Twig-Dispatch und dem offiziellen
  `@Contao/...`-Twig-Namespace für überschreibbare Templates (Grund 1 ist damit
  ebenfalls größtenteils von Contao Core übernommen).
- Der einzige von Contao vorgesehene Erweiterungspunkt für Konsumenten ist
  `abstract protected function getResponse(FragmentTemplate $template, Model $model, Request $request): Response`.
- `Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController` und
  `...\FrontendModule\AbstractFrontendModuleController` rufen in ihrem bereits fertigen
  `__invoke()` automatisch `$this->tagResponse($model)` auf (geerbt von
  `Contao\CoreBundle\Controller\AbstractController::tagResponse()`) — macht
  `Netzmacht\Contao\Toolkit\Response\ResponseTagger` für Fragment-Controller
  überflüssig. `ResponseTagger` ist laut eigenem Docblock ohnehin nur "backward
  compatibility layer for Contao < 4.6". **Das wird nicht in diesem Punkt gelöst**,
  sondern als eigener, künftiger Punkt vorgemerkt.
- Für die "Hybrid"-Kombination (Content-Element + Frontend-Modul aus einer Klasse) gibt
  es kein Contao-Core-Pendant. Nutzerentscheidung: Dieses Konzept wird nicht in die neue
  Klassengeneration übernommen (Contao bewegt sich laut Nutzer perspektivisch auf
  Content-Elemente als primäres Konzept zu). Content-Element- und
  Frontend-Modul-Basisklasse werden aber beide (getrennt) neu gebaut.
- `IsHiddenTrait::isHidden()` (Invisible-/Start-/Stop-/Preview-Modus-Prüfung) hat kein
  Contao-Core-Pendant und bleibt notwendig.
- `preGenerate()`/`postGenerate()`-Hooks der alten Klasse bleiben ebenfalls funktional
  notwendig: `preGenerate()` für Fälle, in denen statt des normalen Renderns eine andere
  Response nötig ist (Redirect, Datei-Download); `postGenerate()` um z. B.
  Cache-Control-Direktiven auf der fertigen Response zu setzen.

## Entscheidung

Neue, schlanke Basisklassen im Namespace `Netzmacht\Contao\Toolkit\Controller\Fragment\`,
die direkt auf Contaos eigenen Kernklassen aufsetzen, statt deren Template- und
DI-Logik erneut nachzubauen:

- `Controller\Fragment\AbstractContentElementController extends Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController`
- `Controller\Fragment\AbstractFrontendModuleController extends Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController`

Kein `AbstractHybridController`-Pendant in der neuen Generation.

Beide neuen Klassen implementieren `getResponse()` `final` und legen einen dünnen,
vertrauten Hook frei, der `isHidden()`, `preGenerate()`, `prepareTemplateData()` und
`postGenerate()` in dieser Reihenfolge kombiniert:

```php
final protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
{
    if ($this->isHidden($model, $request)) {
        return new Response();
    }

    $response = $this->preGenerate($template, $model, $request);
    if ($response !== null) {
        return $response;
    }

    $template->setData($this->prepareTemplateData($template->getData(), $request, $model));
    $response = $template->getResponse();

    return $this->postGenerate($response, $template, $model, $request) ?? $response;
}

protected function preGenerate(FragmentTemplate $template, ContentModel $model, Request $request): Response|null
{
    return null;
}

protected function prepareTemplateData(array $data, Request $request, ContentModel $model): array
{
    return $data;
}

protected function postGenerate(Response $response, FragmentTemplate $template, ContentModel $model, Request $request): Response|null
{
    return null;
}
```

(analog für `AbstractFrontendModuleController` mit `ModuleModel` statt `ContentModel`)

**Bewusste Signatur-Anpassungen gegenüber der alten Klasse:**
- `preGenerate()` erhält `$template` statt einzelner `$section`/`$classes`-Parameter
  (über `$template->getData()['section']` etc. erreichbar, da Contao diese Daten vor
  `getResponse()` bereits in `addDefaultDataToTemplate()` gesetzt hat).
- `postGenerate()` erhält das fertige `Response`-Objekt (inkl. der von Contao bereits
  gesetzten Cache-Tagging-Header) statt eines rohen HTML-`string $buffer` +
  separatem `$data`-Array.

Kein eigener Konstruktor, kein `TemplateRenderer` mehr injiziert — beide Klassen nutzen
ausschließlich Contaos ererbten Service-Subscriber-Mechanismus (`getSubscribedServices()`
/ `$this->container->get(...)`) für zusätzliche Abhängigkeiten (z. B. `TokenChecker` für
`isHidden()`).

`IsHiddenTrait` wird für den neuen Namespace angepasst: Contao liefert bereits
`isBackendScope()` (kein eigener `isBackendRequest()`-Abstract mehr nötig), `TokenChecker`
wird per Service-Subscriber statt Konstruktor-Property bezogen.

**Wichtige Eigenschaft dieses Designs:** Der Code der neuen Klassen ist für 4.1.0 und
5.0.0 identisch. In 5.0 ändert sich an diesen Klassen nichts weiter — es werden nur die
alten, deprecateten Klassen entfernt. Ein Konsumenten-Bundle, das seine Controller in
4.1 auf die neue Generation umstellt, ist damit automatisch bereits 5.0-kompatibel.

## Änderungen Version 4.1.0 (Kompatibilitätsschicht)

- Neue Klassen unter `src/Controller/Fragment/`:
  `AbstractContentElementController`, `AbstractFrontendModuleController`, sowie die
  angepasste `IsHiddenTrait`-Variante für diesen Namespace.
- Alte Klassen (`Controller\AbstractFragmentController`,
  `Controller\ContentElement\AbstractContentElementController`,
  `Controller\FrontendModule\AbstractFrontendModuleController`,
  `Controller\Hybrid\AbstractHybridController` und deren Traits) bleiben unverändert
  bestehen und funktionsfähig. Docblock-`@deprecated`-Hinweis (doc-only, kein
  `trigger_deprecation()`-Aufruf — Prinzip aus
  `2026-09-14-twig-template-compat-design.md`: diese Klassen werden weiterhin von
  bestehenden Konsumenten-Controllern per Vererbung instanziiert, ein Runtime-Trigger
  würde bei jedem Request jedes migrierten wie nicht-migrierten Projekts feuern).
  Verweis im Docblock auf die neuen `Controller\Fragment\...`-Klassen als Ersatz.
- `docs/`: neue Dokumentationsseite für die neuen Fragment-Controller-Basisklassen,
  bestehende Dokumentation der alten Klassen als deprecated markiert.
- `CHANGELOG.md`: Eintrag unter `[4.1.0]` mit Hinweis auf die neuen Basisklassen und
  Migrationsempfehlung.

## Änderungen Version 5.0.0 (Zielbild, Contao 6)

- Entfernt: `src/Controller/AbstractFragmentController.php`,
  `src/Controller/ContentElement/` (alte Klasse + `IsHiddenTrait`, `RenderBackendViewTrait`
  sofern nicht mehr referenziert), `src/Controller/FrontendModule/AbstractFrontendModuleController.php`
  (alt) + `ModuleRenderBackendViewTrait` (sofern nicht mehr referenziert),
  `src/Controller/Hybrid/` (komplett).
- `src/Controller/Fragment/*` bleibt unverändert bestehen.

## Bewusst nicht Teil dieses Themas

- `ResponseTagger`/`FosCacheResponseTagger`/`NoOpResponseTagger`: durch Contaos
  natives, automatisches `tagResponse($model)` in den neuen Basisklassen bereits jetzt
  erkennbar redundant. Als eigener Punkt der Migrationsreihe geklärt und deprecated
  (doc-only) zugunsten von `Contao\CoreBundle\Cache\CacheTagManager`, siehe
  `2026-09-14-response-tagger-deprecation-design.md`.
- Ob/wie `RenderBackendViewTrait` (Backend-Vorschau-Rendering für ausgeblendete
  Frontend-Module/Content-Elemente im Editor) in der neuen Klassengeneration
  eine Entsprechung braucht, wird bei Bedarf als Nachtrag zu diesem Punkt ergänzt,
  sobald ein konkreter Anwendungsfall ansteht.

## Testing

- Neue Spec-Tests für `Controller\Fragment\AbstractContentElementController` und
  `AbstractFrontendModuleController`: `isHidden()`-Kurzschluss, `preGenerate()`-Kurzschluss,
  `prepareTemplateData()`-Hook wird angewendet, `postGenerate()` kann die Response
  ersetzen oder unverändert lassen.
- Bestehende Specs für die alten Klassen bleiben unverändert (weiterhin funktionsfähiger
  Code, nur deprecated).
