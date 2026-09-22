# Contao 6 Vorbereitung: `RenderBackendViewTrait`/`ModuleRenderBackendViewTrait` deprecaten

Status: approved
Datum: 2026-09-14

## Kontext

Zehnter Einzelpunkt der Contao-6-Migrationsreihe (siehe
`2026-09-14-twig-template-compat-design.md`,
`2026-09-14-request-scope-matcher-deprecation-design.md`,
`2026-09-14-fragment-controller-modernization-design.md`,
`2026-09-14-insert-tag-deprecation-design.md`,
`2026-09-14-dca-wizard-listener-deprecation-design.md`,
`2026-09-14-template-options-listener-finder-design.md`,
`2026-09-14-generate-alias-listener-slug-design.md`,
`2026-09-14-backend-frontend-user-factory-deprecation-design.md` und
`2026-09-14-response-tagger-deprecation-design.md` für die vorherigen Punkte und die
generelle Arbeitsweise).

Löst den in `2026-09-14-fragment-controller-modernization-design.md` unter "Bewusst nicht
Teil dieses Themas" zurückgestellten Punkt auf: Ob/wie `RenderBackendViewTrait`
(Backend-Vorschau-Rendering für Content-Elemente) in der neuen `Controller\Fragment\*`-
Klassengeneration eine Entsprechung braucht.

## Analyse

Zwei Traits rendern beim Anzeigen im Backend-Editor einen "###Wildcard###"-Platzhalter
statt des echten Frontend-Markups: `Controller\ContentElement\RenderBackendViewTrait`
(`renderContentBackendView()`) und `Controller\FrontendModule\ModuleRenderBackendViewTrait`
(`renderModuleBackendView()`). Beide berechnen dieselben Daten (übersetzter Name, Edit-Link
via Router, Datensatz-ID) und rendern damit das Contao-eigene Template
`be_wildcard.html5`/`@Contao/be_wildcard.html.twig`.

Geprüft gegen die tatsächlichen nativen Basisklassen (die unsere neuen
`Controller\Fragment\*`-Klassen aus Punkt 3 bereits erweitern) — das Bild ist für
Frontend-Module und Content-Elemente unterschiedlich:

**Frontend-Module: vollständig durch Contao Core abgedeckt.**
`Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController::__invoke()`
(unverändert von unserer neuen Klasse geerbt) prüft `isBackendScope($request)` und rendert
bei Backend-Kontext automatisch `getBackendWildcard($model)` — **bevor** unser
`getResponse()`-Hook überhaupt aufgerufen wird. `getBackendWildcard()` rendert
`@Contao/backend/module_wildcard.html.twig` mit denselben Daten (Name, ID, Edit-Link,
Request-Token). Für die neue `Controller\Fragment\AbstractFrontendModuleController` ist das
Wildcard-Verhalten also bereits vollautomatisch vorhanden, ganz ohne Toolkit-Code.

**Content-Elemente: nicht automatisch abgedeckt, aber Bausteine vorhanden.**
`AbstractContentElementController::__invoke()` hat keinen entsprechenden Kurzschluss.
Stattdessen übergibt `addDefaultDataToTemplate()` ein `as_editor_view`-Flag an die
Template-Daten, das einzelne moderne Twig-Templates selektiv nutzen (z. B.
`content_element/login.html.twig`, das im Editor-View nur das Passkey-JS unterdrückt, sonst
aber normal rendert) — bewusst anders als bei Modulen, da Content-Elemente inline im
Seitenfluss liegen und ein hartes Wildcard-Replace das Backend-Layout zerreißen würde.

Contao Core nutzt das generische `@Contao/be_wildcard.html.twig` aber weiterhin aktiv für
Elemente mit strukturellem Charakter bzw. Seiteneffekten — verifiziert:
`ContentSliderStart`/`ContentAccordionStart`/`FormFieldsetStart`/`Form` rendern es weiterhin
selbst im Backend-Kontext. Der Bedarf für Content-Elemente ist also real und in Contao 6
weiterhin gültig, nur nicht automatisiert. Die nötigen Bausteine dafür sind auf unserer
neuen `Controller\Fragment\AbstractContentElementController` bereits vorhanden:
`isBackendScope($request)` (geerbt von `AbstractFragmentController`) und `$this->render()`
(geerbt von Symfonys `AbstractController`, über `Contao\CoreBundle\Controller\AbstractController`).

## Entscheidung

Beide alten Traits werden deprecated — doc-only, kein `trigger_deprecation()` (gleiche
Begründung wie bei Punkt 9/`ResponseTagger`: dominanter Nutzungspfad ist die bereits
doc-only-deprecatete alte `ContentElement`/`FrontendModule`/`Hybrid`-Controller-Hierarchie
aus Punkt 3, ein separater Runtime-Trigger würde nur zusätzlich zur bereits vorhandenen
Fragment-Controller-Warnung feuern).

- `ModuleRenderBackendViewTrait`: **kein Ersatz nötig** in der neuen Klassengeneration —
  das native `getBackendWildcard()` deckt den Anwendungsfall bereits vollautomatisch ab.
- `RenderBackendViewTrait`: Ersatz ist ein neuer, dünner Opt-in-Trait
  `Controller\Fragment\RenderBackendWildcardTrait`, der ausschließlich die nativen
  Bausteine nutzt (keine eigene Template-Logik mehr):

  ```php
  namespace Netzmacht\Contao\Toolkit\Controller\Fragment;

  use Contao\ContentModel;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\RouterInterface;
  use Symfony\Contracts\Translation\TranslatorInterface;

  trait RenderBackendWildcardTrait
  {
      protected function renderBackendWildcard(ContentModel $model, Request $request): Response
      {
          $name = $this->container->get('translator')->trans(
              sprintf('CTE.%s.0', $this->getType()),
              [],
              'contao_tl_content',
          );

          $href = $this->container->get('router')->generate(
              'contao_backend',
              ['do' => $request->query->get('do'), 'table' => 'tl_content', 'act' => 'edit', 'id' => $model->id],
          );

          return $this->render('@Contao/be_wildcard.html.twig', [
              'wildcard' => sprintf('###%s###', $name),
              'id'       => $model->id,
              'link'     => $name,
              'href'     => $href,
          ]);
      }

      abstract protected function getType(): string;

      public static function getSubscribedServices(): array
      {
          return [...parent::getSubscribedServices(), 'translator' => TranslatorInterface::class];
      }
  }
  ```

  `router` ist bereits über Symfonys eigene `AbstractController::getSubscribedServices()`
  vorabonniert, nur `translator` muss ergänzt werden. Anders als beim alten Trait wird der
  `do`-Parameter direkt aus dem übergebenen `Request` gelesen (`$request->query->get('do')`)
  statt über einen eigenen `Contao\Input`-Adapter — der neue Trait hat damit keine
  Abhängigkeit mehr auf Legacy-Contao-Klassen.

  Der Trait ist bewusst **nicht** automatisch in `Controller\Fragment\AbstractContentElementController`
  eingebunden (anders als bei Modulen gibt es keinen universellen Automatismus in Contao
  selbst) — Konsumenten, deren Content-Element den harten Platzhalter braucht, binden ihn
  selbst ein und rufen ihn aus ihrem `preGenerate()`-Hook heraus auf, wenn
  `$this->isBackendScope($request)` zutrifft.

  **Hinweis für Konsumenten, die mehrere Traits mit eigenem `getSubscribedServices()`
  kombinieren** (z. B. zusammen mit der ebenfalls in Punkt 3 geplanten neuen
  `IsHiddenTrait`-Variante, die `TokenChecker` per Service-Subscriber bezieht): Da PHP
  Trait-Methoden bei gleichnamigen Methoden nicht automatisch zusammenführt, muss die
  konsumierende Klasse in diesem Fall selbst ein zusammenführendes
  `getSubscribedServices()` schreiben. Dokumentationshinweis, kein Code-Problem des
  Toolkits selbst.

## Änderungen Version 4.1.0 (Kompatibilitätsschicht)

- `Controller\ContentElement\RenderBackendViewTrait`,
  `Controller\FrontendModule\ModuleRenderBackendViewTrait`: Trait-Docblock erhält
  `@deprecated` (kein `trigger_deprecation()`).
- Neu: `Controller\Fragment\RenderBackendWildcardTrait`.
- Dokumentation: neue Seite für `RenderBackendWildcardTrait` (inkl. Beispiel-Aufruf aus
  `preGenerate()`), bestehende Dokumentation der beiden alten Traits als deprecated
  markiert; Hinweis, dass Frontend-Module das Wildcard-Verhalten künftig automatisch von
  Contao Core erhalten (kein Trait mehr nötig).
- `CHANGELOG.md`: Eintrag unter `[4.1.0]`.

## Änderungen Version 5.0.0 (Zielbild, Contao 6)

- Entfernt (bestätigt endgültig, löst den Vorbehalt "sofern nicht mehr referenziert" aus
  `2026-09-14-fragment-controller-modernization-design.md` auf):
  `Controller\ContentElement\RenderBackendViewTrait`,
  `Controller\FrontendModule\ModuleRenderBackendViewTrait`.
- Unverändert: `Controller\Fragment\RenderBackendWildcardTrait`.

## Bewusst nicht Teil dieses Themas

- Kein automatisches Einbinden von `RenderBackendWildcardTrait` in
  `Controller\Fragment\AbstractContentElementController` — bewusste Konsumenten-Opt-in-
  Entscheidung, analog zu Contaos eigenem `as_editor_view`-Konzept, das ebenfalls pro
  Template/Controller entschieden wird statt global erzwungen zu sein.

## Testing

- Neuer Spec-Fall: `RenderBackendWildcardTrait::renderBackendWildcard()` rendert
  `@Contao/be_wildcard.html.twig` mit den erwarteten Daten (übersetzter Name, ID,
  Edit-Link inkl. `do`-Parameter aus dem Request).
- Bestehende Specs für die beiden alten Traits bleiben unverändert (weiterhin
  funktionsfähiger, nur deprecateter Code).
