# Contao 6 Vorbereitung: Veraltete DCA-Listener deprecaten (StateButton, ColorPicker, File-/PagePicker)

Status: approved
Datum: 2026-09-14

## Kontext

Fünfter Einzelpunkt der Contao-6-Migrationsreihe (siehe
`2026-09-14-twig-template-compat-design.md`,
`2026-09-14-request-scope-matcher-deprecation-design.md`,
`2026-09-14-fragment-controller-modernization-design.md` und
`2026-09-14-insert-tag-deprecation-design.md` für die vorherigen Punkte und die
generelle Arbeitsweise).

Geprüft wurden die vier konkreten Listener-Implementierungen in
`src/Dca/Listener/Button/` und `src/Dca/Listener/Wizard/`:
`StateButtonCallbackListener`, `ColorPickerListener`, `FilePickerListener`,
`PagePickerListener`. Alle vier sind rein opt-in nutzbare `button_callback`-/
`wizard`-Callbacks, die ein Konsument explizit in seiner eigenen DCA-Konfiguration
referenziert (kein Toolkit-Service ist in `src/Resources/config/services.yml`
auto-registriert oder als Pflichtabhängigkeit verdrahtet).

## Analyse

### `StateButtonCallbackListener` (bereits vom Nutzer bestätigt)

Baut manuell einen Status-Toggle-Button (Icon-Swap, Redirect-basiertes Umschalten über
`Updater::update()`, Rechteprüfung über `Updater::hasUserAccess()`). Contao Core bietet
seit geraumer Zeit eine vollständige native Entsprechung:
`Contao\CoreBundle\DataContainer\DataContainerOperationsBuilder::handleToggle()`,
ausgelöst über `'toggle' => true` (Feld-Eval) bzw. `list.operations.<op>.toggle`
(Operation-Config). Die native Variante deckt Icon-Swap (gleiche `_`-Suffix-Konvention),
Inverse-Logik (`reverseToggle`) und Rechteprüfung
(`ContaoCorePermissions::USER_CAN_EDIT_FIELD_OF_TABLE`) ab — funktional per AJAX
(`AjaxRequest.toggleField`) statt per Redirect, also sogar UX-seitig überlegen.

**Migrationshinweis:** Der native Toggle läuft nicht über den toolkit-eigenen
`Updater`-Service. Konsumenten, die sich auf `Updater::update()`-Events/-Hooks beim
Statuswechsel verlassen, müssen das beim Umstieg prüfen.

### `ColorPickerListener`

Rendert manuell einen Farbwähler-Button samt Template und JS-Hook. Contao Core bietet
seit der Umstellung auf Stimulus-Controller ein natives Feld-Eval
`eval => ['colorpicker' => true]` (`DataContainer.php:509`,
`data-contao--color-picker-target`) — kein Wizard-Callback, kein Template mehr nötig.

### `FilePickerListener` / `PagePickerListener`

Rendern manuell einen Picker-Button neben einem beliebigen (i. d. R. Text-)Feld, der ein
Popup-Auswahlfenster öffnet und den gewählten Wert in das Feld einträgt. Wichtige
Abgrenzung (siehe Diskussion): `inputType => 'fileTree'`/`'pageTree'` sind **keine**
Entsprechung — das sind eigenständige Widgets, die das gesamte Feld inkl. Speicherung
als FK-Relation übernehmen, nicht das "Picker-Button neben bestehendem Feld"-Muster.

Die tatsächliche native Entsprechung ist das deklarative Feld-Eval
`eval => ['dcaPicker' => [...]]`, das automatisch — unabhängig vom `inputType` —
`Backend::getDcaPickerWizard($extras, $table, $field, $inputName)` aufruft
(`DataContainer.php:634`, im Rahmen von `DataContainer::row()`, der Standard-Feld-
Rendering-Pipeline von `DC_Table`/`DC_Folder`). `Backend::getDcaPickerWizard()` ist eine
öffentliche, nicht deprecatete Methode und deckt exakt das ab, was
`FilePickerListener`/`PagePickerListener::generate()` manuell nachbauen (URL über
`contao.picker.builder`, inkl. `supportsContext()`-Guard; Icon über `Image::getHtml()`).
Zusätzlich rendert `getDcaPickerWizard()` bereits mit dem modernen Stimulus-Controller
(`data-controller="contao--modal-selector"`), während die Toolkit-eigene
`be_wizard_picker.html5` noch mit der alten MooTools-API
(`Backend.openModalSelector(...)`) arbeitet.

**Migrationshinweis:** `dcaPicker` erwartet vermutlich `source` (Tabelle+Zeilen-ID) in
den `extras`, analog zur bisherigen manuellen Befüllung — nicht abschließend gegen jeden
Anwendungsfall verifiziert, aber irrelevant für die Deprecation-Entscheidung selbst.

### `PopupWizardListener` (bleibt unverändert — kein Teil dieser Deprecation)

Löst ein anderes Muster: Button, der einen verknüpften Datensatz in einem
Popup-iFrame zum Bearbeiten öffnet (`Backend.openModalIframe(...)`), kein
Auswahl-Picker. Dafür gibt es **kein** natives Contao-Äquivalent — weder ein Feld-Eval
wie `dcaPicker`/`colorpicker` noch einen geteilten Helper wie
`Backend::getDcaPickerWizard()`. Contao Core löst dasselbe Muster in eigenen
DCA-Klassen weiterhin manuell und dupliziert es dabei sogar (`tl_content::editForm()`
und `::editModule()`, `contao/core-bundle/contao/dca/tl_content.php`, praktisch
identischer Code in beiden Methoden).

**Gegen den `6.0`-Branch von `contao/contao` verifiziert** (nicht nur gegen die
installierte 5.7.13): `contao-components/mootools` ist in Contao 6 weiterhin eine harte
Composer-Abhängigkeit (`composer.json` von Root und `core-bundle`,
`^1.6.0.1`); `tl_content::editForm()`/`::editModule()` sind gegenüber 5.7 unverändert
(identisches `onclick="Backend.openModalIframe(...)"`-Markup); `core.js` definiert
`openModalIframe()` weiterhin vollständig MooTools-basiert (`SimpleModal`,
`window.getSize()`, `document.body.setStyle(...)`). Contao migriert selektiv
(`dcaPicker` läuft in 6.0 bereits über `modal-selector-controller.js`/Stimulus), der
iFrame-Popup-Pfad gehört aber noch nicht zu den migrierten Teilen. `PopupWizardListener`
funktioniert in Contao 6 daher unverändert weiter.

## Entscheidung

`StateButtonCallbackListener`, `ColorPickerListener`, `FilePickerListener`,
`PagePickerListener` werden als eigenständige Klassen deprecated — keine parallele
Toolkit-Abstraktion als Ersatz (konsistent mit dem bisherigen Vorgehen). Da alle vier
rein aktiv durch Konsumenten-DCA-Konfiguration gewählt werden (kein Pflicht-Bestandteil
einer unveränderten Basisklasse), gilt nach dem in
`2026-09-14-twig-template-compat-design.md` festgelegten Prinzip (siehe
[[deprecation-mechanics]]) eine **Runtime-Warnung**
(`trigger_deprecation('netzmacht/contao-toolkit', '4.1', ...)`), ausgelöst im
Konstruktor der jeweiligen Klasse.

Die gemeinsamen Basisklassen `AbstractPickerListener` und `AbstractFieldPickerListener`
bleiben unverändert und unmarkiert — sie sind generische Infrastruktur für Konsumenten,
die einen eigenen, wirklich individuellen Picker ohne Contao-Pendant bauen wollen.
`PopupWizardListener` und `AbstractWizardListener` bleiben ebenfalls vollständig
unverändert (siehe Analyse).

## Änderungen Version 4.1.0 (Kompatibilitätsschicht)

- `StateButtonCallbackListener`: Konstruktor erhält `trigger_deprecation()`-Aufruf
  (Verweis auf das native `toggle`-Feld-Eval als Ersatz), Klassen-Docblock erhält
  `@deprecated`.
- `ColorPickerListener`: Konstruktor (geerbt über `AbstractWizardListener`) bzw. eigener
  Konstruktor erhält `trigger_deprecation()`-Aufruf (Verweis auf
  `eval => ['colorpicker' => true]`), Klassen-Docblock erhält `@deprecated`.
- `FilePickerListener`, `PagePickerListener`: jeweils eigener Konstruktor erhält
  `trigger_deprecation()`-Aufruf (Verweis auf `eval => ['dcaPicker' => [...]]` und
  `Contao\Backend::getDcaPickerWizard()`), Klassen-Docblock erhält `@deprecated`.
- `CHANGELOG.md`: Eintrag unter `[4.1.0]` — Deprecation der vier Listener,
  Migrationsempfehlung je Klasse (natives `toggle`-Eval, `colorpicker`-Eval,
  `dcaPicker`-Eval).
- Dokumentation (sofern vorhanden) erhält entsprechende Deprecation-Hinweise.

## Änderungen Version 5.0.0 (Zielbild, Contao 6)

- Entfernt: `src/Dca/Listener/Button/StateButtonCallbackListener.php`,
  `src/Dca/Listener/Wizard/ColorPickerListener.php`,
  `src/Dca/Listener/Wizard/FilePickerListener.php`,
  `src/Dca/Listener/Wizard/PagePickerListener.php` sowie zugehörige Specs.
- Unverändert: `AbstractPickerListener`, `AbstractFieldPickerListener`,
  `AbstractWizardListener`, `PopupWizardListener`.

## Bewusst nicht Teil dieses Themas

- `PopupWizardListener`: bleibt vollständig unverändert (siehe Analyse) — kein
  Migrationsbedarf, da kein natives Contao-Äquivalent existiert.
- `TemplateOptionsListener` (`src/Dca/Listener/Options/`) und `GenerateAliasListener`
  (`src/Dca/Listener/Save/`) werden als eigene, nachfolgende Punkte der
  Migrationsreihe besprochen.

## Testing

- Neue Spec-Fälle je deprecateter Klasse: Instanziierung löst die Deprecation-Warnung
  aus, bestehendes Verhalten (Rendering, Toggle-Logik) bleibt unverändert
  funktionsfähig.
- Bestehende Specs für die vier Klassen bleiben ansonsten unverändert.
