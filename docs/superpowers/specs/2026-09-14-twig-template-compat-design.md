# Contao 6 Vorbereitung: View/Template-Komponente auf Twig-only

Status: approved
Datum: 2026-09-14

## Kontext

netzmacht/contao-toolkit wird auf Contao 6 vorbereitet. Version **4.1.0** dient dabei
als Kompatibilitätsschicht: Verhalten, das für die künftige breaking Version **5.0.0**
geplant ist, wird — soweit sinnvoll möglich — bereits jetzt zum neuen Default, während
alles, was in 5.0 entfällt, in 4.1 als `@deprecated` markiert wird.

Dies ist der erste von mehreren Einzelpunkten, die nacheinander besprochen und
spezifiziert werden. Größter Einzelpunkt laut Nutzer: Contao bewegt sich mit Contao 6
zu einem reinen Twig-Rendering (Legacy-PHP-Template-Engine wird laut Contao-6-Changelog
entfernt: "Legacy template system removed", Erweiterung von `Module`/`ContentElement`/
`Hybrid` deprecated). Die toolkit-eigene `View\Template`-Komponente baut aktuell
vollständig auf der Legacy-PHP-Template-Engine auf und muss umgebaut werden.

## Globale Rahmenentscheidung (gilt für das gesamte 4.1-Release, nicht nur diesen Punkt)

**Contao-4.13-Unterstützung wird in 4.1.0 vollständig fallen gelassen.**
`composer.json` wird von `contao/core-bundle: ^4.13 || ^5.3` auf
`contao/core-bundle: ^5.7` geändert (global, nicht nur für die Template-Komponente).

Begründung: 4.1 existiert, um auf Contao 6 vorzubereiten. Dualer Support für Contao 4
und 5/6 würde in vielen Punkten unnötige Verzweigungen erzwingen. `^5.7` wurde explizit
gewählt (nicht `^5.5`), als konkrete Mindestversion für den Rest der 4.1-Arbeiten.

Für alle künftigen Punkte dieser Migration gilt automatisch: Contao-4-spezifische
Code-Pfade dürfen entfernt/bereinigt werden, nicht erst in 5.0.

## Analyse des Ist-Zustands

- `Netzmacht\Contao\Toolkit\View\Template` (Interface): `parse()`, `get()`, `set()`,
  `helper()`, `getData()`, `setData()` — Wrapper-API um Contaos Legacy-Template-Objekte.
- `View\Template\TemplateFactory` (Interface) + `ToolkitTemplateFactory` (Implementierung):
  erzeugt `FrontendTemplate`/`BackendTemplate`.
- `View\Template\FrontendTemplate` / `BackendTemplate`: erben von
  `Contao\FrontendTemplate` / `Contao\BackendTemplate` (Legacy-PHP-Template-Engine),
  nutzen `TemplateTrait`.
- `View\Template\TemplateTrait`: liefert `get()`/`set()`/`helper()`/`insert()`.
- `View\Template\Event\GetTemplateHelpersEvent` + `Subscriber\GetTemplateHelpersListener`:
  event-basierte Registrierung von Template-Helpern (`assets`, `translator`), die per
  `$this->helper('name')` in Legacy-Templates abgerufen werden.
- `View\Template\TemplateRenderer` (Interface) + `DelegatingTemplateRenderer`: einziger
  öffentlich genutzte Rendering-Einstieg. Routet anhand des Namens: `*.twig` → Twig,
  sonst → `be:`/`fe:`/`toolkit:`-Präfix-Namen → Legacy-Contao-Templates via
  `TemplateFactory`.
- `DependencyInjection\Compiler\TemplateRendererPass`: verdrahtet den Twig-Service nur,
  wenn er im Container existiert (aktuell "optional" gedacht — in der Praxis liefert
  `contao/core-bundle` Twig aber immer mit).
- `Controller\AbstractFragmentController::render()`: präfixt Namen ohne `.twig`-Endung
  und ohne expliziten Scope automatisch mit `fe:` (impliziter Legacy-Fallback).
- Eigene `.html5`-Templates im Paket (einzige, die das Toolkit selbst ausliefert):
  `src/Resources/contao/templates/be_wizard_picker.html5`,
  `be_wizard_color_picker.html5`, `be_wizard_popup.html5` — genutzt von
  `Dca\Listener\Wizard\AbstractPickerListener`, `ColorPickerListener`,
  `PopupWizardListener`, jeweils ausschließlich über `TemplateRenderer::render()`
  (kein direktes Instanziieren der Template-Klassen durch Konsumenten-Code gefunden).
- `docs/view/templates.rst` dokumentiert die aktuelle API.

## Entscheidung

Twig wird ab 4.1.0 der produktiv genutzte Default-Rendering-Pfad für die drei
toolkit-eigenen Templates. Die Legacy-Template-Objekt-Abstraktion
(`Template`/`TemplateFactory`/`FrontendTemplate`/`BackendTemplate`/`TemplateTrait` sowie
der Helper-Event-Mechanismus) wird als Ganzes deprecated — nicht durch eine neue,
parallele Toolkit-Abstraktion ersetzt. Für Twig-Templates ist kein `helper()`-Ersatz
nötig: Twig-native Mittel (Contaos eigene Twig-Functions/Filter, z. B. `trans()`,
`backend_icon()`) übernehmen diese Rolle.

Icon-Rendering in den neuen Twig-Templates nutzt Contaos native Twig-Function
`backend_icon(src, alt, attrs)` (Wrapper um `Image::getHtml()`, seit Contao 5.5
vorhanden, Standard-Idiom in Contao Cores eigenen Backend-Wizard-Twig-Templates wie
`row_wizard.html.twig`, `option_wizard.html.twig`). Da die Mindestanforderung ohnehin
auf `^5.7` angehoben wird, ist keine eigene Fallback-Function nötig.

## Änderungen Version 4.1.0 (Kompatibilitätsschicht)

### composer.json
- `contao/core-bundle: ^4.13 || ^5.3` → `^5.7` (globale Änderung, siehe oben).

### Neue Twig-Templates
Unter `src/Resources/views/backend/` (Symfony-Bundle-Legacy-Struktur, passend zur
bestehenden `src/Resources/`-Konvention des Pakets; Namespace `@NetzmachtContaoToolkit`):
- `wizard_picker.html.twig`
- `wizard_color_picker.html.twig`
- `wizard_popup.html.twig`

Icon-Markup wird direkt im Twig-Template über `backend_icon(icon, title,
attrs().set('style', 'cursor:pointer'))` erzeugt (kein Vorrendern im Listener).

### Default-Umstellung
`$template`-Property in `AbstractPickerListener`, `ColorPickerListener`,
`PopupWizardListener` zeigt künftig auf die neuen `@NetzmachtContaoToolkit/backend/*.html.twig`-Namen.
Die bisherigen `.html5`-Dateien bleiben unverändert im Paket bestehen (für explizite
Fremdreferenzen von außen), werden aber nicht mehr als Default verwendet.

### Deprecation-Markierungen
Neue Abhängigkeit: `symfony/deprecation-contracts` (für `trigger_deprecation()`).

Als `@deprecated` markiert, Entfernung in 5.0.0 angekündigt
(`trigger_deprecation('netzmacht/contao-toolkit', '4.1', '...')`):
- Interface `View\Template` und alle Implementierungen (`FrontendTemplate`,
  `BackendTemplate`, `TemplateTrait`)
- Interface `View\Template\TemplateFactory` + `ToolkitTemplateFactory`
- `View\Template\Event\GetTemplateHelpersEvent` + `Subscriber\GetTemplateHelpersListener`
- `View\Template\Exception\HelperNotFound`
- Legacy-Zweig in `DelegatingTemplateRenderer` (`renderContaoTemplate()` /
  `extractScopeAndTemplateName()`) — Warnung wird ausgelöst, wenn dieser Zweig
  tatsächlich einen Namen verarbeitet (nicht beim Instanziieren der Klasse)
- Impliziter `fe:`-Auto-Prefix in `AbstractFragmentController::render()` — Warnung, wenn
  ein Name ohne `.twig`-Endung übergeben wird

`DependencyInjection\Compiler\TemplateRendererPass` bleibt unverändert bestehen (Twig
ist ab `^5.7` faktisch immer vorhanden, die Pass-Logik wird aber erst in 5.0 entfernt,
um in 4.1 keine unnötige zusätzliche Verhaltensänderung einzuführen).

### Dokumentation
`docs/view/templates.rst`: Twig wird als empfohlener Weg dokumentiert, der
Legacy-Abschnitt entsprechend als deprecated gekennzeichnet.

### CHANGELOG.md
Eintrag unter `[Unreleased]` bzw. `[4.1.0]` mit: composer-Anhebung (Breaking im Sinne
der Host-Kompatibilität, aber keine öffentliche API-Änderung), neue Twig-Templates,
Deprecation-Hinweise mit Verweis auf die betroffenen Klassen/Interfaces.

## Änderungen Version 5.0.0 (Zielbild, Contao 6)

Entfernt:
- `View/Template.php` (Interface)
- `View/Template/TemplateFactory.php`, `ToolkitTemplateFactory.php`
- `View/Template/FrontendTemplate.php`, `BackendTemplate.php`, `TemplateTrait.php`
- `View/Template/Exception/HelperNotFound.php`
- `View/Template/Event/GetTemplateHelpersEvent.php`,
  `Subscriber/GetTemplateHelpersListener.php`
- `DependencyInjection/Compiler/TemplateRendererPass.php`
- `src/Resources/contao/templates/*.html5` (alle drei Dateien)

Geändert:
- `DelegatingTemplateRenderer`: verliert den `TemplateFactory`-Konstruktor-Parameter,
  nimmt `Twig\Environment` non-nullable entgegen (Autowiring), `render()` reicht nur
  noch an Twig durch. Klassenname/-umbenennung ist eine offene, unkritische
  Detailfrage für die 5.0-Umsetzung selbst.
- `AbstractFragmentController::render()` / `getTemplateName()`: Auto-Prefixing-Logik
  entfällt vollständig, Template-Namen sind reine Twig-Referenzen.
- `src/Resources/config/services.yml`: `template_factory`-Service-Definition entfällt.

## Bewusst nicht Teil dieses Themas

Wie `customTpl` bei Fragment-Controllern in Contao 6 aufgelöst wird (Legacy
`.html5`-Basename vs. Twig-Identifier) hängt an Contao Cores eigener Migration der
`customTpl`-Auswahl (`Controller::getTemplateGroup()`) und wird als eigener,
zukünftiger Punkt separat besprochen.

## Testing

- Bestehende Specs (`spec/`) für `DelegatingTemplateRenderer`, `AbstractFragmentController`,
  die drei Wizard-Listener werden auf die neuen Default-Template-Namen angepasst.
- Neue Spec-Fälle: Deprecation-Warnung wird ausgelöst, wenn der Legacy-Zweig in
  `DelegatingTemplateRenderer` bzw. der `fe:`-Auto-Prefix in
  `AbstractFragmentController::render()` greift.
- Manuelle/funktionale Verifikation: Wizard-Icons (Picker, ColorPicker, Popup) im
  Contao-Backend rendern nach der Umstellung optisch identisch zum bisherigen Zustand.
