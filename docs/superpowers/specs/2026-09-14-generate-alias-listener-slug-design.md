# Contao 6 Vorbereitung: `GenerateAliasListener` auf Contaos `Slug`-Service umstellen

Status: approved
Datum: 2026-09-14

## Kontext

Siebter Einzelpunkt der Contao-6-Migrationsreihe (siehe
`2026-09-14-twig-template-compat-design.md`,
`2026-09-14-request-scope-matcher-deprecation-design.md`,
`2026-09-14-fragment-controller-modernization-design.md`,
`2026-09-14-insert-tag-deprecation-design.md`,
`2026-09-14-dca-wizard-listener-deprecation-design.md` und
`2026-09-14-template-options-listener-finder-design.md` für die vorherigen Punkte und die
generelle Arbeitsweise).

`Netzmacht\Contao\Toolkit\Dca\Listener\Save\GenerateAliasListener` ist ein
`save_callback`, der über eine Filter-Kette
(`Data\Alias\FilterBasedAliasGenerator` mit `ExistingAliasFilter`, `SlugifyFilter`,
`SuffixFilter`) und eine austauschbare, per String-Service-Id aus der DCA-Konfiguration
aufgelöste `AliasGeneratorFactory` einen eindeutigen Alias-Wert erzeugt. Laut Nutzer gilt
dieses System heute als überengineert; zudem bietet Contao inzwischen mit dem
`ausi/slug-generator`-basierten `contao.slug`-Service eine native Alternative. Ziel dieses
Punkts war zu klären, in welchem Umfang das toolkit-eigene System zugunsten dieser
nativen Alternative reduziert werden kann.

## Analyse

`Contao\CoreBundle\Slug\Slug` (autowireable über `Contao\CoreBundle\Slug\Slug: '@contao.slug'`)
kapselt `ausi/slug-generator`:

```php
public function generate(
    string $text,
    int|iterable $options = [],
    callable|null $duplicateCheck = null,
    string $integerPrefix = 'id-',
): string
```

Gegen die tatsächlichen Core-DCAs geprüft (`tl_article`, `tl_form`,
`EventListener\DataContainer\PageUrlListener` für `tl_page`, sowie `tl_news` im
`6.0`-Branch via GitHub): Contao selbst besitzt **keine** generische
`GenerateAliasListener`-Abstraktion. Jede DCA schreibt ihren eigenen `save`-Callback mit
eigenem Duplicate-Check-Closure. Das Muster ist überall identisch: Ist der übergebene Wert
leer, wird per `$slug->generate($text, $options, $duplicateCheck)` neu generiert
(inkl. `-2`, `-3`, …-Suffix-Schleife bei Kollision, exakt das Prinzip von
`SuffixFilter`); ist der Wert manuell gesetzt, wird er validiert (kein rein numerischer
Wert, keine Kollision) und im Kollisionsfall eine Exception geworfen — anders als der
bestehende Toolkit-Code, der einen manuell gesetzten, aber nicht eindeutigen Wert
**still mit einem neu generierten überschreibt** (`ExistingAliasFilter` bricht die
Filterkette nur bei Eindeutigkeit ab, sonst läuft sie stillschweigend weiter zu
`SlugifyFilter`).

**Nutzerentscheidungen aus der Diskussion:**

- Der automatische `id-`-Präfix bei rein numerischen Alias-Werten
  (`Slug::generate()`s `$integerPrefix`-Parameter) wird **nicht** übernommen — laut
  Nutzer nicht immer zielführend (der generische Listener bedient beliebige Tabellen, bei
  denen eine Alias/ID-Routing-Kollision nicht zwangsläufig ein Thema ist, anders als bei
  `tl_page`).
- `AliasGeneratorFactory`/`ToolkitAliasGeneratorFactory` (der austauschbare,
  String-Service-Id-basierte Lookup zur Laufzeit) entfällt vollständig. Wer einen wirklich
  individuellen Generator braucht, implementiert direkt das bestehende
  `AliasGenerator`-Interface und verdrahtet ihn selbst im eigenen `save_callback` — analog
  dazu, wie Contao Core selbst pro Tabelle eigene Callbacks schreibt, statt eine
  generische Factory-Indirektion zu pflegen.
- Ein manuell eingegebener, nicht eindeutiger Alias-Wert soll künftig eine Exception
  auslösen (analog Contao Core), statt still überschrieben zu werden. Das ist eine
  bewusste Verhaltensänderung gegenüber dem bisherigen Toolkit-Code.

**Was am bestehenden Toolkit-Code bleibt, weil es kein Contao-Pendant hat:** Die
zusammengesetzte Eindeutigkeitsprüfung von `UniqueDatabaseValueValidator`
(`$uniqueKeyFields`, z. B. Alias eindeutig nur innerhalb eines `pid`) — selbst `tl_news`
prüft laut Quellcode nur global (`WHERE alias=? AND id!=?`, keine `pid`-Eingrenzung).
`Validator`-Interface, `UniqueDatabaseValueValidator` und `InvalidAliasException` bleiben
daher unverändert bestehen.

## Entscheidung

Neue, schlanke Klassen ersetzen die Filter-/Factory-Kette:

- `Data\Alias\SlugAliasGenerator implements AliasGenerator` — nutzt `Contao\CoreBundle\Slug\Slug`
  für die Generierung/Duplicate-Suffix-Schleife und den bestehenden `Validator` für die
  Eindeutigkeitsprüfung:

  ```php
  final class SlugAliasGenerator implements AliasGenerator
  {
      public function __construct(
          private readonly Slug $slug,
          private readonly Validator $validator,
          private readonly string $tableName,
          private readonly array $fields = ['id'],
          private readonly string $separator = '-',
      ) {
      }

      public function generate(object $result, mixed $value = null): string
      {
          $value = $value !== null ? (string) $value : '';

          if ($value !== '') {
              $this->guardValidAlias($result, $value);

              return $value;
          }

          $generated = $this->slug->generate(
              $this->buildSourceText($result),
              ['delimiter' => $this->separator],
              fn (string $alias): bool => ! $this->validator->validate($result, $alias, [(int) $result->id]),
          );

          $this->guardValidAlias($result, $generated);

          return $generated;
      }

      private function buildSourceText(object $result): string
      {
          $values = [];

          foreach ($this->fields as $field) {
              $values[] = (string) $result->$field;
          }

          return implode(' ', array_filter($values, static fn (string $v): bool => $v !== ''));
      }

      private function guardValidAlias(object $result, string $value): void
      {
          if (! $this->validator->validate($result, $value, [(int) $result->id])) {
              throw InvalidAliasException::forDatabaseEntry($this->tableName, (int) $result->id, $value);
          }
      }
  }
  ```

  Der `separator`-Parameter wird 1:1 als `delimiter`-Option an `ausi/slug-generator`
  durchgereicht (dessen natives Konzept für das Trennzeichen zwischen Wörtern) — keine
  eigene Slugify-Logik mehr im Toolkit.

- `Dca\Listener\Save\SlugAliasListener` — neuer `save_callback`, liest dieselben
  DCA-Konfigurationsschlüssel wie bisher unter `toolkit.alias_generator.*`
  (`fields`, neu: `unique_key_fields`, `allow_empty` — 1:1 auf
  `UniqueDatabaseValueValidator`s bestehende Parameter gemappt), baut `SlugAliasGenerator`
  und `UniqueDatabaseValueValidator` direkt ohne Factory-Indirektion:

  ```php
  final class SlugAliasListener
  {
      public function __construct(
          private readonly Slug $slug,
          private readonly Connection $connection,
          private readonly DcaManager $dcaManager,
      ) {
      }

      public function onSaveCallback(mixed $value, DataContainer $dataContainer): string
      {
          return $this->getGenerator($dataContainer)->generate($dataContainer->activeRecord, $value);
      }

      private function getGenerator(DataContainer $dataContainer): AliasGenerator
      {
          $config = $this->getConfig($dataContainer);

          $validator = new UniqueDatabaseValueValidator(
              $this->connection,
              $dataContainer->table,
              $dataContainer->field,
              $config['unique_key_fields'],
              $config['allow_empty'],
          );

          return new SlugAliasGenerator($this->slug, $validator, $dataContainer->table, $config['fields']);
      }

      // getConfig() liest 'fields' (default ['id']), 'unique_key_fields' (default []),
      // 'allow_empty' (default false) aus $definition->get(['fields', $field, 'toolkit', 'alias_generator', ...]).
  }
  ```

  Kein Instanz-Cache mehr nötig (bisher wegen teurem Container-Lookup pro Feld
  vorgehalten) — `new`-Aufrufe sind hier billig.

`GenerateAliasListener` selbst ist der einzige dokumentierte, von Konsumenten aktiv per
Service-Id referenzierte Einstiegspunkt (`netzmacht.contao_toolkit.dca.listeners.alias_generator`
im DCA `save_callback`) — anders als bei `ArgumentParser` im InsertTag-Punkt gibt es für
`FilterBasedAliasGenerator`/die Filter-Klassen/`ToolkitAliasGeneratorFactory` keine separat
dokumentierte Standalone-Nutzung. Nach dem in
`2026-09-14-twig-template-compat-design.md` festgelegten Prinzip (siehe
[[deprecation-mechanics]]) genügt daher **ein** Runtime-Trigger-Punkt: im Konstruktor von
`GenerateAliasListener`.

## Änderungen Version 4.1.0 (Kompatibilitätsschicht)

- Neu: `Data\Alias\SlugAliasGenerator`, `Dca\Listener\Save\SlugAliasListener`, als neuer
  öffentlicher Service `netzmacht.contao_toolkit.dca.listeners.slug_alias_generator`
  registriert (Argumente: `@contao.slug`, `@database_connection`,
  `@netzmacht.contao_toolkit.dca.manager`).
- `GenerateAliasListener`: Konstruktor erhält `trigger_deprecation()`-Aufruf (Verweis auf
  `SlugAliasListener` als Ersatz), Klassen-Docblock erhält `@deprecated`.
- `FilterBasedAliasGenerator`, `Filter`-Interface, `AbstractFilter`, `AbstractValueFilter`,
  `SlugifyFilter`, `SuffixFilter`, `ExistingAliasFilter`, `RawValueFilter`,
  `AliasGeneratorFactory`-Interface, `ToolkitAliasGeneratorFactory`: Klassen-Docblock
  erhält `@deprecated` (doc-only, kein eigener Trigger — Warnung feuert bereits über
  `GenerateAliasListener`).
- `Validator`-Interface, `UniqueDatabaseValueValidator`, `AliasGenerator`-Interface,
  `InvalidAliasException`: unverändert, nicht deprecated (werden vom neuen System
  weiterverwendet).
- Dokumentation: neue Seite für `SlugAliasListener`, bestehende Dokumentation von
  `GenerateAliasListener` als deprecated markiert.
- `CHANGELOG.md`: Eintrag unter `[4.1.0]` — Deprecation von `GenerateAliasListener` und
  der Filter-/Factory-Kette, neuer `SlugAliasListener` auf Basis von `contao.slug`.

## Änderungen Version 5.0.0 (Zielbild, Contao 6)

- Entfernt: `Dca\Listener\Save\GenerateAliasListener.php`,
  `Data\Alias\FilterBasedAliasGenerator.php`, `Data\Alias\Filter.php`,
  `Data\Alias\Filter/{AbstractFilter,AbstractValueFilter,SlugifyFilter,SuffixFilter,ExistingAliasFilter,RawValueFilter}.php`,
  `Data\Alias\Factory/{AliasGeneratorFactory,ToolkitAliasGeneratorFactory}.php`.
- Entfernt aus `services.yml`/`listeners.yml`: die zugehörigen Service- und
  Alias-Definitionen sowie das Parameter
  `netzmacht.contao_toolkit.alias_generator.default`.
- Unverändert: `Data\Alias\SlugAliasGenerator`, `Dca\Listener\Save\SlugAliasListener`,
  `Validator`, `UniqueDatabaseValueValidator`, `AliasGenerator`-Interface,
  `InvalidAliasException`.

## Bewusst nicht Teil dieses Themas

- Automatisches `id-`-Präfixing bei rein numerischen Alias-Werten
  (`Slug::generate()`s `$integerPrefix`) — laut Nutzer nicht übernommen, da nicht immer
  zielführend für einen generischen, tabellenunabhängigen Listener.
- Guard gegen rein numerische, manuell eingegebene Alias-Werte (wie in
  `tl_article`/`tl_form`/`tl_page`) — nicht Teil des bisherigen Toolkit-Verhaltens und an
  Contaos Seiten-Routing-Kontext gebunden, der für beliebige DCA-Tabellen nicht
  zwangsläufig gilt. Kann bei konkretem Bedarf als eigener Nachtrag ergänzt werden.
- Kein Ersatz für den austauschbaren `AliasGeneratorFactory`-Mechanismus — wer einen
  individuellen Generator braucht, implementiert `AliasGenerator` direkt und verdrahtet
  ihn selbst.

## Testing

- Neuer Spec-Fall: `SlugAliasGenerator::generate()` mit leerem `$value` generiert über
  `Slug::generate()` und die Duplicate-Check-Closure einen eindeutigen Alias
  (inkl. Kollisionsfall mit Suffix-Schleife).
- Neuer Spec-Fall: `SlugAliasGenerator::generate()` mit manuell gesetztem, nicht
  eindeutigem `$value` wirft `InvalidAliasException`.
- Neuer Spec-Fall: `SlugAliasListener` liest `fields`/`unique_key_fields`/`allow_empty`
  korrekt aus der DCA-Konfiguration.
- Neuer Spec-Fall: `GenerateAliasListener`-Instanziierung löst die Deprecation-Warnung
  aus, bestehendes Verhalten bleibt unverändert funktionsfähig.
- Bestehende Specs für die Filter-/Factory-Kette bleiben unverändert (weiterhin
  funktionsfähiger, nur deprecateter Code).
