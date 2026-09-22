# Contao 6 Vorbereitung: `InsertTag`-Komponente deprecaten

Status: approved
Datum: 2026-09-14

## Kontext

Vierter Einzelpunkt der Contao-6-Migrationsreihe (siehe
`2026-09-14-twig-template-compat-design.md`,
`2026-09-14-request-scope-matcher-deprecation-design.md` und
`2026-09-14-fragment-controller-modernization-design.md` für die vorherigen Punkte und
die generelle Arbeitsweise).

`Netzmacht\Contao\Toolkit\InsertTag\*` wurde ursprünglich gebaut, um eigenen Insert-Tags
einen zuverlässigen Zugriff auf ihre Parameter zu bieten (Contao selbst lieferte dafür
historisch nur den rohen Tag-String `foo::bar::baz?option=1`, der Konsumenten manuell
zerlegen mussten). Laut Nutzer hat sich hier in Contao einiges geändert.

## Analyse

`src/InsertTag/` enthält aktuell nur noch vier Hilfsklassen: `AbstractInsertTagParser`,
`AbstractSingleInsertTagParser`, `ArgumentParser`, `ArgumentParserPlugin`. Das ist bereits
eine reduzierte Zwischenstufe: Commit `5c2b9ff` ("Rewrite insert tag integration. Drop
Parser and Replacer interfaces. As hook listeners can be registered as services in
Contao 4, just provide some abstract parser helpers.") hat die frühere eigene
`Replacer`-/`Parser`-Interface-Abstraktion samt Hook-Registrierungsmechanismus bereits
entfernt. Die verbliebenen Klassen sind reine, rein opt-in genutzte Bausteine: Ein
Konsument registriert **selbst** eine Service-Klasse als `replaceInsertTags`-Hook-Listener
und nutzt darin `AbstractInsertTagParser`/`AbstractSingleInsertTagParser::replace()` bzw.
den `ArgumentParser`, um den rohen Tag-String zu zerlegen. Das Toolkit selbst verdrahtet
nichts davon in `src/Resources/config/services.yml` oder einer Compiler-Pass — kein
öffentlicher Bestandteil ist also von diesen Klassen als Pflichtabhängigkeit betroffen.

`docs/insert-tags/index.rst` ist bei der Rewrite in `5c2b9ff` nicht mitgezogen worden und
beschreibt weiterhin die längst entfernte `Replacer`-/`Parser`-API
(`netzmacht.contao_toolkit.insert_tag.parser`-Service-Tag) — die Dokumentation ist damit
bereits jetzt schlicht falsch.

Contao Core bietet seit 5.0 ein natives, deutlich mächtigeres Insert-Tag-System:

- `#[Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag('name')]` (wiederholbar,
  auf Klasse oder Methode) registriert eine Service-Methode automatisch als
  Insert-Tag-Handler (Tag `contao.insert_tag`, autowiring-fähig).
- Signatur: `__invoke(ResolvedInsertTag $tag): InsertTagResult` (bzw. benannte Methode
  über `method: '...'`).
- `ResolvedInsertTag::getParameters()` liefert exakt den "zuverlässigen Zugriff auf
  Parameter", den die Toolkit-Klasse ursprünglich nachbauen sollte:
  `get(int|string $key)`, `all(?string $name)`, `getScalar()`, `allScalar()` (inkl.
  automatischem int/float-Casting), Named-Parameter-Konvention (`key=value`),
  verschachtelte Tag-Auflösung, Flags sowie Caching-Metadaten
  (`InsertTagResult::withExpiresAt()`/`withCacheTags()`).
- Der alte `replaceInsertTags`-Hook wird laut Contao-6-Changelog **nicht** entfernt
  (bleibt als Legacy-Fallback in `InsertTagParser::handleLegacyTagsHook()` bestehen);
  `ChunkedText` fällt zwar in 6.0-RC2 weg, wird von der Toolkit-Klasse aber nirgends
  verwendet (`replace()` gibt `string|false` zurück, nicht `ChunkedText`). Contao
  verändert hier also nichts brechend — es stellt nur einen klar überlegenen, nativen
  Weg bereit.

Einzige Sache, die `ArgumentParser::parseArgumentQuery()` zusätzlich bot und die Contao
Core nicht nachbildet: das Zerlegen eines einzelnen Parameters nach der
`foo?bar=baz`-Konvention in `['value' => 'foo', 'options' => [...]]`. Laut Nutzer ist das
verzichtbar, da sich das bei Bedarf direkt mit PHPs `parse_str()` nachbauen lässt.

## Entscheidung

Die gesamte `InsertTag`-Komponente (`AbstractInsertTagParser`,
`AbstractSingleInsertTagParser`, `ArgumentParser`, `ArgumentParserPlugin`) wird als Ganzes
deprecated — keine parallele Toolkit-Abstraktion als Ersatz (konsistent mit dem Vorgehen
bei der Template-Komponente und `RequestScopeMatcher`). Konsumenten migrieren auf Contao
Cores natives `#[AsInsertTag]` + `ResolvedInsertTag`/`ResolvedParameters`.

Anders als bei `RequestScopeMatcher` (Pflicht-Konstruktor-Parameter unveränderter
Basisklassen) handelt es sich hier um eine **rein aktiv gewählte** Nutzung: Ein Konsument
entscheidet sich explizit, seine eigene Hook-Listener-Klasse von
`AbstractSingleInsertTagParser` erben zu lassen bzw. `ArgumentParser` zu verwenden. Nach
dem in `2026-09-14-twig-template-compat-design.md` festgelegten Prinzip (siehe
[[deprecation-mechanics]]) bekommt dieser Fall daher eine **Runtime-Warnung**
(`trigger_deprecation('netzmacht/contao-toolkit', '4.1', ...)`), nicht nur eine
Doc-Annotation:

- `AbstractInsertTagParser` erhält einen Konstruktor, der beim Instanziieren (d. h. beim
  Instanziieren der konkreten Konsumenten-Unterklasse) die Warnung auslöst.
- `ArgumentParser::create()` löst ebenfalls eine Warnung aus, da die Klasse auch
  unabhängig von der `AbstractInsertTagParser`-Hierarchie direkt verwendet werden kann.

## Änderungen Version 4.1.0 (Kompatibilitätsschicht)

- `AbstractInsertTagParser`: neuer Konstruktor mit `trigger_deprecation()`-Aufruf
  (Verweis auf `Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag` als Ersatz),
  Klassen-Docblock erhält `@deprecated`.
- `AbstractSingleInsertTagParser`, `ArgumentParserPlugin`: Docblock erhält
  `@deprecated`-Hinweis (Warnung wird bereits über `AbstractInsertTagParser` bzw. über
  `ArgumentParser::create()` ausgelöst, kein zusätzlicher Trigger nötig).
- `ArgumentParser::create()`: `trigger_deprecation()`-Aufruf ergänzt, Klassen-Docblock
  erhält `@deprecated`.
- `docs/insert-tags/index.rst`: komplett neu geschrieben. Entfernt die längst falsche
  Beschreibung der `Replacer`/`Parser`-API, dokumentiert `#[AsInsertTag]` als
  empfohlenen, einzigen Weg (inkl. Beispiel für zuverlässigen Parameterzugriff über
  `ResolvedInsertTag::getParameters()`), erwähnt die Toolkit-eigenen Klassen nur noch als
  deprecated.
- `CHANGELOG.md`: Eintrag unter `[4.1.0]` — Deprecation der gesamten `InsertTag`-
  Komponente, Migrationsempfehlung auf `#[AsInsertTag]`.

## Änderungen Version 5.0.0 (Zielbild, Contao 6)

- Entfernt: `src/InsertTag/` komplett (`AbstractInsertTagParser.php`,
  `AbstractSingleInsertTagParser.php`, `ArgumentParser.php`, `ArgumentParserPlugin.php`).
- Entfernt: `spec/InsertTag/ArgumentParserSpec.php`.

## Bewusst nicht Teil dieses Themas

- Kein Ersatz für die `foo?bar=baz`-Parameterkonvention (`ArgumentParser::parseArgumentQuery()`)
  — laut Nutzer mit `parse_str()` direkt beim Konsumenten lösbar, keine Toolkit-Abstraktion
  nötig.

## Testing

- Bestehende `spec/InsertTag/ArgumentParserSpec.php` bleibt funktional unverändert
  (Klasse funktioniert weiterhin, nur deprecated).
- Neuer Spec-Fall: `AbstractInsertTagParser`-Instanziierung (über eine konkrete
  Testklasse) löst die Deprecation-Warnung aus; `ArgumentParser::create()` löst die
  Deprecation-Warnung aus.
