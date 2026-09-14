# Contao 6 Vorbereitung: `createBackendUserInstance()`/`createFrontendUserInstance()` deprecaten

Status: approved
Datum: 2026-09-14

## Kontext

Achter Einzelpunkt der Contao-6-Migrationsreihe (siehe
`2026-09-14-twig-template-compat-design.md`,
`2026-09-14-request-scope-matcher-deprecation-design.md`,
`2026-09-14-fragment-controller-modernization-design.md`,
`2026-09-14-insert-tag-deprecation-design.md`,
`2026-09-14-dca-wizard-listener-deprecation-design.md`,
`2026-09-14-template-options-listener-finder-design.md` und
`2026-09-14-generate-alias-listener-slug-design.md` für die vorherigen Punkte und die
generelle Arbeitsweise).

Ausgangspunkt war die Korrektur von Punkt 5
(`2026-09-14-dca-wizard-listener-deprecation-design.md`): Dort wurde
`Data\Updater\DatabaseRowUpdater::hasUserAccess()` von der deprecateten
`Contao\BackendUser::hasAccess()` auf Symfonys Voter-basiertes Rechtesystem umgestellt.
Im Zuge dessen wurde geprüft, ob es weitere `BackendUser`-Zugriffsstellen außerhalb
bereits deprecateten Codes gibt. Gefunden:
`DependencyInjection\ContaoServicesFactory::createBackendUserInstance()`/
`createFrontendUserInstance()`, angeboten als private DI-Services
`netzmacht.contao_toolkit.contao.backend_user`/`...frontend_user`.

## Analyse

Zunächst verifiziert (gegen den tatsächlichen `6.0`-Branch von `contao/contao`, nicht nur
die installierte 5.7.13): Die Klassen `Contao\BackendUser`, `Contao\FrontendUser` und
`Contao\User` sind in Contao 6 selbst **nicht** deprecated — sie bleiben die kanonische
Symfony-`UserInterface`-Repräsentation für Backend-/Frontend-Nutzer.
`User::getInstance()` (der Singleton-Zugriff) ist ebenfalls nicht deprecated. Deprecated
ist ausschließlich `BackendUser::hasAccess()` selbst (bereits in Punkt 5 behandelt) — im
5.7.13-Docblock stand "to be removed in Contao 6", im tatsächlichen `6.0`-Branch wurde das
auf "to be removed in Contao 7" verschoben; die Methode existiert in Contao 6 also
weiterhin, bleibt aber deprecated.

`createBackendUserInstance()`/`createFrontendUserInstance()` selbst sind davon zwar nicht
direkt betroffen, folgen aber einem anderen Muster als der Rest von
`ContaoServicesFactory`: Alle übrigen Methoden (`createBackendAdapter()`,
`createSystemAdapter()`, …) nutzen Contaos modernen `Adapter`-Mechanismus
(`ContaoFramework::getAdapter()`). Die beiden User-Methoden rufen stattdessen
`ContaoFramework::createInstance(BackendUser::class)` auf — verifiziert in
`ContaoFramework::createInstance()` (Contao Core): Da `BackendUser`/`FrontendUser` eine
statische `getInstance()`-Methode besitzen, wird intern exakt dorthin verzweigt. Das ist
also der klassische Contao-Singleton-Zugriff (`BackendUser::getInstance()`), der den
*aktuell authentifizierten* Nutzer liefert — inhaltlich näher an
`Symfony\Bundle\SecurityBundle\Security::getUser()` als an den übrigen,
zustandslosen Framework-Klassen-Adaptern dieser Factory.

Toolkit-intern ungenutzt: Weder die beiden Factory-Methoden noch die Service-Ids werden
irgendwo im eigenen `src/`-Code referenziert (nur ihre eigene Definition + der
zugehörige Spec-Test) — reine öffentliche API für Konsumenten.

## Entscheidung

`createBackendUserInstance()`/`createFrontendUserInstance()` sowie die Services
`netzmacht.contao_toolkit.contao.backend_user`/`...frontend_user` werden deprecated.
Nutzerentscheidung zum Ersatz, abhängig vom tatsächlichen Bedarf:

- Geht es um eine **Berechtigungsprüfung**, muss auf Symfonys Rechtesystem umgestiegen
  werden: `Security::isGranted(...)` mit den passenden
  `Contao\CoreBundle\Security\ContaoCorePermissions::*`-Konstanten (analog zur bereits in
  Punkt 5 korrigierten `DatabaseRowUpdater::hasUserAccess()`-Implementierung).
- Wird tatsächlich das **konkrete Nutzerobjekt** benötigt (z. B. `$user->username`,
  eigene Properties), führt der Weg über Symfonys Security-Helper:
  `Symfony\Bundle\SecurityBundle\Security::getUser()` (optional kombiniert mit einem
  `instanceof BackendUser`/`FrontendUser`-Check) — exakt das Muster, das
  `DatabaseRowUpdater` bereits nutzt.

Da beide Methoden aktiv von Konsumenten aufgerufen bzw. deren Service-Ids aktiv
referenziert werden müssen (kein Pflicht-Bestandteil einer unveränderten Basisklasse),
gilt nach dem in `2026-09-14-twig-template-compat-design.md` festgelegten Prinzip (siehe
[[deprecation-mechanics]]) eine **Runtime-Warnung**
(`trigger_deprecation('netzmacht/contao-toolkit', '4.1', ...)`), ausgelöst jeweils zu
Beginn der Methode.

Der Rest von `ContaoServicesFactory` (alle `Adapter`-basierten `create*Adapter()`-Methoden)
bleibt unverändert — dort besteht kein Bezug zu `BackendUser`/`FrontendUser` und kein
Contao-seitiger Deprecation-Anlass.

## Änderungen Version 4.1.0 (Kompatibilitätsschicht)

- `ContaoServicesFactory::createBackendUserInstance()`/`createFrontendUserInstance()`:
  jeweils `trigger_deprecation()`-Aufruf zu Methodenbeginn (Verweis auf
  `Security::isGranted()` für Rechteprüfungen bzw. `Security::getUser()` für den
  konkreten Nutzerzugriff), Methoden-Docblock erhält `@deprecated`.
- `services.yml`: Kommentar-Hinweis `@deprecated` über den Service-Definitionen
  `netzmacht.contao_toolkit.contao.backend_user`/`...frontend_user`.
- Dokumentation (sofern vorhanden) erhält entsprechende Deprecation-Hinweise.
- `CHANGELOG.md`: Eintrag unter `[4.1.0]` — Deprecation der beiden Factory-Methoden/
  Services, Migrationsempfehlung (Symfony-Rechtesystem bzw. `Security::getUser()`).

## Änderungen Version 5.0.0 (Zielbild, Contao 6)

- Entfernt: `ContaoServicesFactory::createBackendUserInstance()`,
  `createFrontendUserInstance()`.
- Entfernt aus `services.yml`: `netzmacht.contao_toolkit.contao.backend_user`,
  `netzmacht.contao_toolkit.contao.frontend_user`.
- Unverändert: alle übrigen `ContaoServicesFactory`-Methoden (Adapter-basiert).

## Bewusst nicht Teil dieses Themas

- Keine neue Toolkit-Abstraktion als Ersatz — Konsumenten nutzen Symfonys
  Security-Komponente direkt, analog zum bereits etablierten Muster in
  `DatabaseRowUpdater`.

## Testing

- Bestehende Spec-Fälle `it_creates_backend_user_instance`/`it_creates_frontend_user_instance`
  (`spec/DependencyInjection/ContaoServicesFactorySpec.php`) bleiben funktional
  unverändert (Methoden funktionieren weiterhin, nur deprecated).
- Neuer Spec-Fall je Methode: Aufruf löst die Deprecation-Warnung aus.
