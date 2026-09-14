# Contao 6 Vorbereitung: `RequestScopeMatcher` als obsolet markieren

Status: approved
Datum: 2026-09-14

## Kontext

Zweiter Einzelpunkt der Contao-6-Migrationsreihe (siehe
`2026-09-14-twig-template-compat-design.md` für den ersten Punkt und die generelle
Arbeitsweise). Nutzerhypothese: `Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher`
wurde eingeführt, weil Contao früher zwingend ein `Request`-Objekt für die
Scope-Ermittlung benötigte. Das sei inzwischen nicht mehr nötig, die Klasse damit
obsolet.

## Analyse

Geprüft anhand des installierten `contao/core-bundle` (5.7.13, entspricht unserer in
`2026-09-14-twig-template-compat-design.md` festgelegten neuen Mindestanforderung):

`Contao\CoreBundle\Routing\ScopeMatcher::isFrontendRequest()` /
`isBackendRequest()` / `isContaoRequest()` akzeptieren bereits nativ
`Request|null $request = null` und fallen intern selbst auf
`RequestStack::getCurrentRequest()` zurück. Damit ist die Nutzerhypothese bestätigt:
`RequestScopeMatcher` dupliziert für diese drei Methoden nur noch Funktionalität, die
Contao Core selbst bereits bietet.

Eine vierte Methode, `isInstallRequest()` (prüft `_route === 'contao_install'`), hat
keine Entsprechung in Contaos `ScopeMatcher`. Grep über `vendor/contao/core-bundle`
findet keine `contao_install`-Route mehr — bestätigt die zusätzliche Nutzerangabe, dass
Contao diese Route bereits in Contao 5 entfernt hat. Unter unserer Mindestanforderung
(`^5.7`) ist die Methode damit bereits jetzt faktisch tote Code (liefert immer `false`).
Einziger Verwender im gesamten Paket: `SetOperationDataAttributeListener::onLoadDataContainer()`.

**Wichtiger Befund zur Tragweite:** `RequestScopeMatcher` ist kein rein internes Detail,
sondern ein **Pflicht-Konstruktor-Parameter** der öffentlichen Basisklassen
`AbstractFragmentController`, `AbstractContentElementController`,
`AbstractHybridController` und `AbstractFrontendModuleController` — der primäre
Erweiterungspunkt des Pakets für Konsumenten-eigene Fragment-Controller. Diese
Konstruktor-Signaturen in 4.1 zu ändern wäre breaking. Der konkrete Umgang mit diesen
vier Basisklassen als Ganzes (nicht nur bzgl. `RequestScopeMatcher`) wird als eigener,
nachfolgender Punkt der Migrationsreihe separat besprochen; dieser Spec legt nur die
`RequestScopeMatcher`-spezifischen Teile fest.

## Entscheidung

`RequestScopeMatcher` wird als gesamte Klasse deprecated, `isInstallRequest()` wird als
bereits tote Funktionalität direkt entfernt (keine Deprecation-Phase nötig, da unter der
unterstützten Mindestversion nie mehr funktional). Für die Deprecation-Mechanik gilt das
in `2026-09-14-twig-template-compat-design.md` festgehaltene, projektweite Prinzip
(siehe dortiger Abschnitt "Deprecation-Markierungen"): reine Doc-Annotation ohne
Runtime-Trigger, solange die Klasse weiterhin als Pflichtabhängigkeit unveränderter
öffentlicher Basisklassen verdrahtet ist.

## Änderungen Version 4.1.0 (Kompatibilitätsschicht)

- `RequestScopeMatcher`: Klassen-Docblock erhält `@deprecated`-Hinweis (Verweis auf
  `Contao\CoreBundle\Routing\ScopeMatcher` als Ersatz für `isFrontendRequest()` /
  `isBackendRequest()` / `isContaoRequest()`). Kein `trigger_deprecation()`-Aufruf, da
  weiterhin Pflicht-Konstruktor-Parameter der vier Basisklassen (werden bei jedem
  Request instanziiert).
- `isInstallRequest()`-Methode wird ersatzlos aus `RequestScopeMatcher` entfernt.
- `SetOperationDataAttributeListener::onLoadDataContainer()`: Bedingung vereinfacht sich
  von `! $this->scopeMatcher->isContaoRequest() || $this->scopeMatcher->isInstallRequest()`
  zu `! $this->scopeMatcher->isContaoRequest()`.
- Konstruktor-Signaturen von `AbstractFragmentController`,
  `AbstractContentElementController`, `AbstractHybridController`,
  `AbstractFrontendModuleController` bleiben unverändert (weiterhin `RequestScopeMatcher`).
- `docs/`: sofern `RequestScopeMatcher` dokumentiert ist, Hinweis auf Deprecation
  ergänzen.
- `CHANGELOG.md`: Eintrag unter `[4.1.0]` — Deprecation von `RequestScopeMatcher`
  (Ersatz: `Contao\CoreBundle\Routing\ScopeMatcher`), Entfernung von
  `isInstallRequest()`.

## Änderungen Version 5.0.0 (Zielbild, Contao 6)

- `RequestScopeMatcher` wird entfernt (`src/Routing/RequestScopeMatcher.php` +
  `spec/Routing/RequestScopeMatcherSpec.php`).
- Service-Definition `netzmacht.contao_toolkit.routing.scope_matcher` in
  `src/Resources/config/services.yml` wird entfernt.
- Konstruktor-Signaturen der vier Basisklassen wechseln von `RequestScopeMatcher` auf
  `Contao\CoreBundle\Routing\ScopeMatcher` — genaue Ausgestaltung Teil des separaten,
  noch zu besprechenden Punkts zu den Fragment-Controller-Basisklassen.

## Testing

- `spec/Routing/RequestScopeMatcherSpec.php`: Testfall für `isInstallRequest()`
  entfernen.
- `spec/Dca/Listener/SetOperationDataAttributeListenerSpec.php` (falls vorhanden) bzw.
  neuer Spec-Fall: vereinfachte Bedingung ohne `isInstallRequest()`.
