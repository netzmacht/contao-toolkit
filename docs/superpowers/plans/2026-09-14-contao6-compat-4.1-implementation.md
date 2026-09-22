# Contao 6 Compat-Layer 4.1.0 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ship netzmacht/contao-toolkit 4.1.0 as a Contao-6-preparation compatibility layer: raise the minimum host requirement to `contao/core-bundle: ^5.7`, deprecate everything that will be removed in the future breaking 5.0.0 release, and introduce the native-Contao replacements as the new default where sensible — all without breaking the public API of 4.0.x.

**Architecture:** Eleven independent design decisions (see Spec below) are implemented in dependency order: foundation (composer bump) first, then the Template/RequestScopeMatcher/Fragment-Controller trio (many later points build on the new `Controller\Fragment\*` namespace), then the remaining, largely independent deprecations, then the auto-callback-registration feature (depends on the DCA-listener cluster), then final cross-cutting deliverables (UPGRADE-5.0.md, CHANGELOG, regression run). Two deprecation mechanics are used throughout, decided per-class in the specs: doc-only `@deprecated` (no runtime cost) for classes still wired as mandatory constructor dependencies of unchanged public base classes, and `trigger_deprecation()` runtime warnings for classes only reached via an active, opt-in consumer choice.

**Tech Stack:** PHP 8.2+, Symfony DependencyInjection/HttpFoundation/HttpKernel, Contao 5.7 core-bundle (Twig, `ScopeMatcher`, `CacheTagManager`, `Slug`, `FinderFactory`, `#[AsInsertTag]`), phpspec 7/8, `symfony/deprecation-contracts` (new).

**Spec:** This plan implements the "Änderungen Version 4.1.0" section of each of the following 11 approved design docs (the "Änderungen Version 5.0.0" sections are context/target-state only, not in scope here):

1. `docs/superpowers/specs/2026-09-14-twig-template-compat-design.md` — View/Template → Twig-only
2. `docs/superpowers/specs/2026-09-14-request-scope-matcher-deprecation-design.md` — `RequestScopeMatcher` deprecation
3. `docs/superpowers/specs/2026-09-14-fragment-controller-modernization-design.md` — new `Controller\Fragment\*` base classes
4. `docs/superpowers/specs/2026-09-14-insert-tag-deprecation-design.md` — `InsertTag\*` deprecation
5. `docs/superpowers/specs/2026-09-14-dca-wizard-listener-deprecation-design.md` — StateButton/Color/File/PagePicker deprecation + `DatabaseRowUpdater` fix
6. `docs/superpowers/specs/2026-09-14-template-options-listener-finder-design.md` — `TemplateOptionsListener` extension
7. `docs/superpowers/specs/2026-09-14-generate-alias-listener-slug-design.md` — `GenerateAliasListener` → `SlugAliasListener`
8. `docs/superpowers/specs/2026-09-14-backend-frontend-user-factory-deprecation-design.md` — `ContaoServicesFactory` user-factory deprecation
9. `docs/superpowers/specs/2026-09-14-response-tagger-deprecation-design.md` — `ResponseTagger` deprecation
10. `docs/superpowers/specs/2026-09-14-render-backend-view-trait-deprecation-design.md` — `RenderBackendViewTrait` deprecation + `RenderBackendWildcardTrait`
11. `docs/superpowers/specs/2026-09-14-auto-register-field-callbacks-design.md` — auto-register remaining toolkit DCA callbacks

## Global Constraints

- `composer.json` `contao/core-bundle` requirement: `^4.13 || ^5.3` → `^5.7` (exact value from Spec 1's global framing decision — not `^5.5`).
- New dependency `symfony/deprecation-contracts` (exact constraint decided in Task 0.1) for `trigger_deprecation()`.
- Deprecation mechanic per class is fixed by its spec — do not swap doc-only ↔ runtime-trigger without re-checking the spec's reasoning (mandatory-dependency-of-unchanged-base-class → doc-only; active/opt-in consumer choice → runtime trigger).
- All `trigger_deprecation()` calls use the exact signature `trigger_deprecation('netzmacht/contao-toolkit', '4.1', '<message>')`.
- No parallel Toolkit abstraction is introduced as a replacement for any deprecated component (consistent decision across all 11 specs) — consumers migrate to the native Contao 5.7/6 API directly.
- No 5.0.0 removals happen in this plan — deprecated code stays fully functional and its existing specs stay green throughout.
- PHPCS/PHPStan/Psalm conventions of the existing codebase (native types, `#[Override]`, constructor property promotion where already used) are followed for all new/changed code.

---

## File Structure

**New source files:**

- `src/Resources/views/backend/wizard_picker.html.twig`, `wizard_color_picker.html.twig`, `wizard_popup.html.twig` — Twig replacements for the toolkit's own backend wizard `.html5` templates (Task A.1).
- `spec/DeprecationSpecHelper.php` (namespace `spec\Netzmacht\Contao\Toolkit`) — shared phpspec helper trait implementing the `set_error_handler(..., E_USER_DEPRECATED)` capture pattern used by every runtime-trigger spec in this plan (Task A.2).
- `spec/ConcreteDataContainer.php`, `spec/DataContainerSpecHelper.php` — shared concrete `DataContainer` test fixture + reflection-based property setter, used by every spec that needs a real `DataContainer` instance (`Contao\DataContainer` is abstract and its magic `__set()` has no case for `table`) (Task C.5).
- `src/Controller/Fragment/AbstractContentElementController.php`, `AbstractFrontendModuleController.php`, `IsHiddenTrait.php` — new, slim fragment-controller base classes built directly on Contao Core's own base classes (Task A.6).
- `src/Controller/Fragment/RenderBackendWildcardTrait.php` — opt-in backend-wildcard rendering trait for content elements built on `Controller\Fragment\*` (Task F.1).
- `src/Data/Alias/SlugAliasGenerator.php`, `src/Dca/Listener/Save/SlugAliasListener.php` — `contao.slug`-based alias generation replacing the filter/factory chain (Task C.6).
- `src/DependencyInjection/Compiler/RegisterFieldCallbacksPass.php`, `src/Dca/Listener/RegisterFieldCallbacksListener.php` — tagged-service auto-registration of `options_callback`/`save_callback`/`wizard` from `fields.*.toolkit.*` config (Task G.1).
- `UPGRADE-5.0.md` — consumer-facing migration guide skeleton for the future 5.0.0 breaking release (Task H.1).
- New spec files/fixtures alongside each new or newly-deprecated class (none of the DCA listeners, `RequestScopeMatcher` consumer `SetOperationDataAttributeListener`, `DatabaseRowUpdater`, or `ContaoServicesFactory`'s user-factory methods had specs before this plan) — see each task's own "Test:" line for exact paths.

**Modified source files (deprecation markers / behavior changes — see individual tasks for exact edits):**

- `composer.json` (Task 0.1)
- `src/View/Template.php`, `src/View/Template/{TemplateFactory,ToolkitTemplateFactory,FrontendTemplate,BackendTemplate,TemplateTrait,DelegatingTemplateRenderer}.php`, `src/View/Template/Event/GetTemplateHelpersEvent.php`, `src/View/Template/Subscriber/GetTemplateHelpersListener.php`, `src/View/Template/Exception/HelperNotFound.php` (Tasks A.2, A.3)
- `src/Dca/Listener/Wizard/{AbstractPickerListener,ColorPickerListener}.php`, `src/Dca/Listener/Wizard/PopupWizardListener.php` (Task A.4, C.3)
- `src/Routing/RequestScopeMatcher.php`, `src/Dca/Listener/SetOperationDataAttributeListener.php` (Task A.5)
- `src/Controller/AbstractFragmentController.php`, `src/Controller/ContentElement/AbstractContentElementController.php`, `src/Controller/FrontendModule/AbstractFrontendModuleController.php`, `src/Controller/Hybrid/AbstractHybridController.php` (Task A.7)
- `src/InsertTag/{AbstractInsertTagParser,AbstractSingleInsertTagParser,ArgumentParser,ArgumentParserPlugin}.php` (Task B.1)
- `src/Data/Updater/DatabaseRowUpdater.php` (Task C.1)
- `src/Dca/Listener/Button/StateButtonCallbackListener.php` (Task C.2)
- `src/Dca/Listener/Wizard/{FilePickerListener,PagePickerListener}.php` (Task C.4)
- `src/Dca/Listener/Options/TemplateOptionsListener.php` (Task C.5)
- `src/Dca/Listener/Save/GenerateAliasListener.php`, `src/Data/Alias/{FilterBasedAliasGenerator,Filter}.php`, `src/Data/Alias/Filter/{AbstractFilter,AbstractValueFilter,SlugifyFilter,SuffixFilter,ExistingAliasFilter,RawValueFilter}.php`, `src/Data/Alias/Factory/{AliasGeneratorFactory,ToolkitAliasGeneratorFactory}.php` (Task C.7)
- `src/DependencyInjection/ContaoServicesFactory.php`, `src/Resources/config/services.yml` (Task D.1)
- `src/Response/{ResponseTagger,FosCacheResponseTagger,NoOpResponseTagger}.php`, `src/DependencyInjection/Compiler/FosCacheResponseTaggerPass.php`, `src/Exception/InvalidHttpResponseTagException.php` (Task E.1)
- `src/Controller/ContentElement/RenderBackendViewTrait.php`, `src/Controller/FrontendModule/ModuleRenderBackendViewTrait.php` (Task F.1)
- `src/Resources/config/listeners.yml`, `src/NetzmachtContaoToolkitBundle.php` (Task G.2)
- `CHANGELOG.md` (every task cluster appends its own entries; consolidated in Task H.2)

**New/modified documentation:**

- `docs/view/templates.rst` deprecation notice (Task A.3).
- New `docs/controller/index.rst`, `docs/controller/fragment.rst`, new `docs/routing/index.rst`, `docs/routing/scope-matcher.rst`, `docs/index.rst` toctree entries (Task A.8).
- `docs/insert-tags/index.rst` full rewrite (Task B.2).
- `docs/dca/callbacks.rst`, `docs/data/alias.rst`, `docs/data/updater.rst` updates (Task C.8).
- New `docs/dependency-injection/index.rst`, `docs/dependency-injection/contao-services-factory.rst`, `docs/index.rst` toctree entry (Task D.1).
- New `docs/cache/index.rst`, `docs/cache/response-tagger.rst`, `docs/index.rst` toctree entry (Task E.1).
- New `docs/controller/render-backend-wildcard.rst`, `docs/controller/index.rst` toctree entry (Task F.1).
- New `docs/dca/auto-callbacks.rst`, `docs/dca/index.rst` toctree entry (Task G.2).
- `CHANGELOG.md` built up incrementally across Tasks A.8, B.2, C.8, D.1, E.1, F.1, G.2 into one `[4.1.0]` section, consolidated in Task H.2.
- New `UPGRADE-5.0.md` (Task H.1).

---

## Task 0.1: Foundation — raise `contao/core-bundle` requirement, add `symfony/deprecation-contracts`, baseline test run

**Files:**
- Modify: `composer.json:26` (require block)
- Test: none (baseline verification only)

**Interfaces:**
- Consumes: nothing.
- Produces: `contao/core-bundle: ^5.7` as the new floor for every later task; `symfony/deprecation-contracts` available for `trigger_deprecation()` calls from Task A.2 onward.

- [ ] **Step 1: Raise the composer constraint**

In `composer.json`, change:

```json
    "contao/core-bundle": "^4.13 || ^5.3",
```

to:

```json
    "contao/core-bundle": "^5.7",
```

- [ ] **Step 2: Add `symfony/deprecation-contracts`**

In the same `require` block, add (keep alphabetical `sort-packages: true` ordering, i.e. directly after `"contao/core-bundle"`):

```json
    "symfony/deprecation-contracts": "^2.5 || ^3.0",
```

- [ ] **Step 3: Update composer lock and install**

Run: `composer update contao/core-bundle symfony/deprecation-contracts --with-all-dependencies`
Expected: exits 0, `composer.lock` updated, no conflict errors (contao/core-bundle 5.7.13 is already the installed version per the repo's vendor state, so this should resolve without downloading a new major version).

- [ ] **Step 4: Run the baseline test suite before any behavior change**

Run: `vendor/bin/phpspec run`
Expected: all existing specs still pass (this establishes the pre-change baseline; any failure here must be resolved before continuing, as it is unrelated to this plan's changes).

- [ ] **Step 5: Commit**

```bash
git add composer.json composer.lock
git commit -m "Raise contao/core-bundle to ^5.7, add symfony/deprecation-contracts"
```

---

## Task A.1: New Twig backend-wizard templates

**Files:**
- Create: `src/Resources/views/backend/wizard_picker.html.twig`
- Create: `src/Resources/views/backend/wizard_color_picker.html.twig`
- Create: `src/Resources/views/backend/wizard_popup.html.twig`
- Test: none (templates are exercised end-to-end by the specs of Task A.4; manual backend verification noted in Task A.4 Step 4)

**Interfaces:**
- Consumes: Contao's native `backend_icon(src, alt, attrs)` Twig function (available since Contao 5.5, guaranteed present under the `^5.7` floor from Task 0.1).
- Produces: three new `@NetzmachtContaoToolkitBundle/backend/*.html.twig` template identifiers, referenced as new `protected string $template` defaults in Task A.4.

The three existing `.html5` templates being ported (already read in full during design — reproduced here for the port):

`src/Resources/contao/templates/be_wizard_picker.html5`:
```php
<a href="<?= $this->url ?>"
    onclick="Backend.getScrollOffset();Backend.openModalSelector({
        'width':768,
        'title':'<?= $this->jsTitle ?>',
        'url': this.href,
        'id': '<?= $this->field ?>',
        'tag': 'ctrl_<?= $this->id ?>',
        'self': this
    }); return false"
>
<?= \Contao\Image::getHtml($this->icon, $this->title, 'style="cursor:pointer"') ?>
</a>
```

`src/Resources/contao/templates/be_wizard_color_picker.html5`:
```php
<?= \Contao\Image::getHtml(
    $this->icon,
    $this->title,
    'style="cursor:pointer" title="' . $this->title .'" id="moo_' . $this->field . '"'
); ?>
<script>
    window.addEvent('domready', function() {
        new MooRainbow('moo_<?= $this->field ?>', {
            id: 'ctrl_<?= $this->field ?>',
            startColor: ((cl = $('ctrl_<?= $this->field ?>').value.hexToRgb(true)) ? cl : [255, 0, 0]),
            imgPath: 'assets/colorpicker/images/',
            onComplete: function(color) {
                <?php if ($this->replaceHex): ?>
                $('ctrl_<?= $this->field ?>').value = color.hex.replace("#", "");
                <?php else: ?>
                $('ctrl_<?= $this->field ?>').value = color.hex;
                <?php endif; ?>
            }
        });
    });
</script>
```

`src/Resources/contao/templates/be_wizard_popup.html5`:
```php
<a href="<?= $this->href ?>"
   onclick="Backend.getScrollOffset();Backend.openModalSelector({
       'width':768,
       'title':'<?= $this->jsTitle ?>',
       'url': this.href
       }); return false"
   title="<?= $this->label ?>"
   style="padding-left: 3px"
    >
    <?= \Contao\Image::getHtml($this->icon, $this->title) ?>
</a>
```

Icon rendering keeps `MooTools`/`Backend.openModalSelector`/`MooRainbow` markup unchanged (Spec 1 only replaces the *rendering engine and icon markup*, not the JS behavior — `PopupWizardListener`'s own template `be_wizard_popup.html5` also uses `Backend.openModalSelector`, unrelated to the `dcaPicker` migration decided in Spec 5, which stays out of scope for this component). Only `Image::getHtml()` calls become `backend_icon()` calls.

- [ ] **Step 1: Create `wizard_picker.html.twig`**

```twig
<a href="{{ url }}"
    onclick="Backend.getScrollOffset();Backend.openModalSelector({
        'width':768,
        'title':'{{ jsTitle }}',
        'url': this.href,
        'id': '{{ field }}',
        'tag': 'ctrl_{{ id }}',
        'self': this
    }); return false"
>
{{ backend_icon(icon, title, attrs().set('style', 'cursor:pointer')) }}
</a>
```

- [ ] **Step 2: Create `wizard_color_picker.html.twig`**

```twig
{{ backend_icon(icon, title, attrs().set('style', 'cursor:pointer').set('title', title).set('id', 'moo_' ~ field)) }}
<script>
    window.addEvent('domready', function() {
        new MooRainbow('moo_{{ field }}', {
            id: 'ctrl_{{ field }}',
            startColor: ((cl = $('ctrl_{{ field }}').value.hexToRgb(true)) ? cl : [255, 0, 0]),
            imgPath: 'assets/colorpicker/images/',
            onComplete: function(color) {
                {% if replaceHex %}
                $('ctrl_{{ field }}').value = color.hex.replace("#", "");
                {% else %}
                $('ctrl_{{ field }}').value = color.hex;
                {% endif %}
            }
        });
    });
</script>
```

- [ ] **Step 3: Create `wizard_popup.html.twig`**

```twig
<a href="{{ href }}"
   onclick="Backend.getScrollOffset();Backend.openModalSelector({
       'width':768,
       'title':'{{ jsTitle }}',
       'url': this.href
       }); return false"
   title="{{ label }}"
   style="padding-left: 3px"
    >
    {{ backend_icon(icon, title) }}
</a>
```

- [ ] **Step 4: Verify Twig can locate the templates under the bundle namespace**

Run: `php -r '$twig = (new \Symfony\Component\Process\Process(["php", "bin/console", "debug:twig", "--filter=NetzmachtContaoToolkit"]));'` is not available outside a full Contao app; instead verify via a unit-level smoke check once `netzmacht.contao_toolkit.template_renderer` renders one of them in Task A.4's spec (`it_renders_the_color_picker_with_the_new_twig_default`) — no standalone step needed here.

- [ ] **Step 5: Commit**

```bash
git add src/Resources/views/backend/wizard_picker.html.twig src/Resources/views/backend/wizard_color_picker.html.twig src/Resources/views/backend/wizard_popup.html.twig
git commit -m "Add Twig replacements for the toolkit's backend wizard templates"
```

---

## Task A.2: Shared deprecation-spec test helper + runtime trigger on the legacy `DelegatingTemplateRenderer` branch

This is the **first** runtime-trigger deprecation in the plan, so it introduces the shared phpspec capture helper every later runtime-trigger task (B.1, C.2, C.3, C.4, C.7, D.1) reuses.

**Files:**
- Create: `spec/DeprecationSpecHelper.php` (namespace `spec\Netzmacht\Contao\Toolkit`, per the `autoload-dev` `psr-4` root `spec/` → `spec\Netzmacht\Contao\Toolkit\`)
- Modify: `src/View/Template/DelegatingTemplateRenderer.php:77` (`renderContaoTemplate()`)
- Modify: `spec/View/Template/DelegatingTemplateRendererSpec.php`

**Interfaces:**
- Consumes: nothing new.
- Produces: `trait DeprecationSpecHelper` with `private function captureDeprecations(callable $callback): list<string>` and `private function assertDeprecationTriggered(list<string> $messages, string $needle): void` — every later runtime-trigger spec `use`s this trait and calls both methods.

- [ ] **Step 1: Create the shared deprecation-capture helper**

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit;

use RuntimeException;

use function count;
use function implode;
use function set_error_handler;
use function sprintf;
use function str_contains;

use const E_USER_DEPRECATED;

/**
 * Shared phpspec helper to assert that a `trigger_deprecation()` call happened.
 *
 * Symfony's `trigger_deprecation()` reports via `@trigger_error($message, E_USER_DEPRECATED)`.
 * There is no symfony/phpunit-bridge in this project (phpspec, not PHPUnit), so this trait
 * installs a temporary error handler to capture those messages instead.
 */
trait DeprecationSpecHelper
{
    /** @return list<string> */
    private function captureDeprecations(callable $callback): array
    {
        $messages = [];
        $previous = set_error_handler(
            static function (int $errno, string $errstr) use (&$messages): bool {
                $messages[] = $errstr;

                return true;
            },
            E_USER_DEPRECATED,
        );

        try {
            $callback();
        } finally {
            set_error_handler($previous);
        }

        return $messages;
    }

    /** @param list<string> $messages */
    private function assertDeprecationTriggered(array $messages, string $needle): void
    {
        if (count($messages) === 0) {
            throw new RuntimeException('Expected a deprecation warning to be triggered, none was.');
        }

        foreach ($messages as $message) {
            if (str_contains($message, $needle)) {
                return;
            }
        }

        throw new RuntimeException(sprintf(
            'None of the %d captured deprecation message(s) contain "%s". Captured: %s',
            count($messages),
            $needle,
            implode(' | ', $messages),
        ));
    }
}
```

- [ ] **Step 2: Add the runtime trigger to the legacy Contao-template rendering branch**

In `src/View/Template/DelegatingTemplateRenderer.php`, add the import (alphabetically after `str_ends_with`):

```php
use function preg_match;
use function sprintf;
use function str_ends_with;
use function trigger_deprecation;
```

Then change `renderContaoTemplate()`:

```php
    private function renderContaoTemplate(string $name, array $parameters): string
    {
        trigger_deprecation(
            'netzmacht/contao-toolkit',
            '4.1',
            'Rendering legacy Contao templates via "%s" is deprecated and will be removed in 5.0. Use a Twig template instead.',
            $name,
        );

        [$scope, $templateName] = $this->extractScopeAndTemplateName($name);

        return match ($scope) {
            'fe' => $this->templateFactory->createFrontendTemplate($templateName, $parameters)->parse(),
            'be' => $this->templateFactory->createBackendTemplate($templateName, $parameters)->parse(),
            default => throw new InvalidArgumentException(sprintf('Template scope "%s" is not supported', $scope)),
        };
    }
```

- [ ] **Step 3: Add deprecation-warning spec cases**

In `spec/View/Template/DelegatingTemplateRendererSpec.php`, add the trait use and two new examples (keep all existing examples unchanged — they still pass, the trigger just also fires silently alongside them):

```php
use Netzmacht\Contao\Toolkit\Exception\InvalidArgumentException;
use Netzmacht\Contao\Toolkit\Exception\RuntimeException;
use Netzmacht\Contao\Toolkit\View\Template;
use Netzmacht\Contao\Toolkit\View\Template\DelegatingTemplateRenderer;
use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use PhpSpec\ObjectBehavior;
use spec\Netzmacht\Contao\Toolkit\DeprecationSpecHelper;

final class DelegatingTemplateRendererSpec extends ObjectBehavior
{
    use DeprecationSpecHelper;

    // ... existing let()/it_is_initializable()/it_is_a_template_renderer()/
    //     it_renders_*()/it_throws_*() methods stay unchanged ...

    public function it_triggers_a_deprecation_warning_when_rendering_a_contao_template(
        TemplateFactory $templateFactory,
        Template $template,
    ): void {
        $templateFactory->createBackendTemplate('foo', [])->willReturn($template);
        $template->parse()->willReturn('foo_html');

        $messages = $this->captureDeprecations(function (): void {
            $this->render('be:foo');
        });

        $this->assertDeprecationTriggered($messages, 'Rendering legacy Contao templates via "be:foo"');
    }

    public function it_does_not_trigger_a_deprecation_warning_when_rendering_a_twig_template(
        TemplateFactory $templateFactory,
    ): void {
        $templateFactory->createFrontendTemplate()->shouldNotBeCalled();

        // A missing twig environment still throws RuntimeException, but no deprecation is
        // triggered for the twig branch itself.
        $messages = $this->captureDeprecations(function (): void {
            try {
                $this->render('foo.html.twig');
            } catch (RuntimeException) {
                // Expected: no twig environment configured in this spec's let().
            }
        });

        if ($messages !== []) {
            throw new \RuntimeException('Did not expect a deprecation warning for a twig template.');
        }
    }
}
```

- [ ] **Step 4: Run the affected specs**

Run: `vendor/bin/phpspec run spec/View/Template/DelegatingTemplateRendererSpec.php`
Expected: all examples green, including the two new ones.

- [ ] **Step 5: Commit**

```bash
git add spec/DeprecationSpecHelper.php src/View/Template/DelegatingTemplateRenderer.php spec/View/Template/DelegatingTemplateRendererSpec.php
git commit -m "Add deprecation-spec helper, deprecate legacy Contao template rendering path"
```

---

## Task A.3: Doc-only deprecate the legacy `View\Template` component

Mandatory dependency of the unchanged `DelegatingTemplateRenderer`/`ToolkitTemplateFactory` wiring → doc-only, no `trigger_deprecation()` (per the mechanics principle in Spec 1).

**Files:**
- Modify: `src/View/Template.php`, `src/View/Template/TemplateFactory.php`, `src/View/Template/ToolkitTemplateFactory.php`, `src/View/Template/FrontendTemplate.php`, `src/View/Template/BackendTemplate.php`, `src/View/Template/TemplateTrait.php`, `src/View/Template/Event/GetTemplateHelpersEvent.php`, `src/View/Template/Subscriber/GetTemplateHelpersListener.php`, `src/View/Template/Exception/HelperNotFound.php`
- Modify: `docs/view/templates.rst`
- Test: none (doc-only deprecation, existing specs for these classes stay unchanged per the spec's Testing section)

**Interfaces:**
- Consumes: nothing.
- Produces: nothing new — pure docblock annotations.

- [ ] **Step 1: `src/View/Template.php`**

```php
/**
 * Interface describes the templates being used in the toolkit.
 *
 * @deprecated Use native Twig templates instead. Will be removed in 5.0.
 */
// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
interface Template
```

- [ ] **Step 2: `src/View/Template/TemplateFactory.php`**

```php
/**
 * TemplateFactory creates a template with some predefined helpers.
 *
 * @deprecated Use native Twig templates instead. Will be removed in 5.0.
 *
 * phpcs:disable SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue.NullabilityTypeMissing
 */
interface TemplateFactory
```

- [ ] **Step 3: `src/View/Template/ToolkitTemplateFactory.php`**

```php
/**
 * TemplateFactory creates a template with some predefined helpers.
 *
 * @deprecated Use native Twig templates instead. Will be removed in 5.0.
 */
final class ToolkitTemplateFactory implements TemplateFactory
```

- [ ] **Step 4: `src/View/Template/FrontendTemplate.php` and `BackendTemplate.php`**

In both files:

```php
/**
 * FrontendTemplate with extended features.
 *
 * @deprecated Use native Twig templates instead. Will be removed in 5.0.
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class FrontendTemplate extends ContaoFrontendTemplate implements Template
```

(analogously `BackendTemplate` / `ContaoBackendTemplate`)

- [ ] **Step 5: `src/View/Template/TemplateTrait.php`**

```php
/**
 * Trait extends the default Contao template classes.
 *
 * @deprecated Use native Twig templates instead. Will be removed in 5.0.
 */
trait TemplateTrait
```

- [ ] **Step 6: `src/View/Template/Event/GetTemplateHelpersEvent.php` and `src/View/Template/Subscriber/GetTemplateHelpersListener.php`**

```php
/**
 * Class GetTemplateHelpersEvent is triggered when the helpers for a template are generated.
 *
 * @deprecated Part of the deprecated legacy template component. Will be removed in 5.0.
 */
final class GetTemplateHelpersEvent extends Event
```

```php
/**
 * Class GetTemplateHelpersListener registers the default supported template helpers for all templates.
 *
 * @deprecated Part of the deprecated legacy template component. Will be removed in 5.0.
 */
final class GetTemplateHelpersListener
```

- [ ] **Step 7: `src/View/Template/Exception/HelperNotFound.php`**

Add a class docblock (none exists yet):

```php
/**
 * @deprecated Part of the deprecated legacy template component. Will be removed in 5.0.
 */
final class HelperNotFound extends RuntimeException
```

- [ ] **Step 8: Update `docs/view/templates.rst`**

Insert directly after the introductory paragraph (before the `Template renderer` heading):

```rst
.. important::

   The Contao-template-based rendering path described below (``Template``, ``TemplateFactory``,
   ``FrontendTemplate``, ``BackendTemplate``, ``TemplateTrait``, the
   ``netzmacht.contao_toolkit.view.get_template_helpers`` event) is deprecated as of 4.1.0 and
   will be removed in 5.0.0. Contao 6 removes the legacy PHP template engine entirely. Write new
   templates in Twig and keep rendering them via the same
   ``netzmacht.contao_toolkit.template_renderer`` service — Twig templates don't need registered
   helpers, use Contao's own Twig functions/filters instead (e.g. ``trans()``, ``backend_icon()``).
```

And change the `Helpers` heading's intro sentence to flag it as legacy-only:

```rst
.. _template-helpers:

Helpers (deprecated, legacy Contao templates only)
---------------------------------------------------

Some templates requires helpers to improve template development and code quality by reusing helper codes. Instead of
```

- [ ] **Step 9: Verify no runtime behavior changed**

Run: `vendor/bin/phpspec run spec/View/Template/Event/GetTemplateHelpersEventSpec.php spec/View/Template/Subscriber/GetTemplateHelpersListenerSpec.php`
Expected: unchanged, all green (docblock-only edit).

- [ ] **Step 10: Commit**

```bash
git add src/View/Template.php src/View/Template/TemplateFactory.php src/View/Template/ToolkitTemplateFactory.php src/View/Template/FrontendTemplate.php src/View/Template/BackendTemplate.php src/View/Template/TemplateTrait.php src/View/Template/Event/GetTemplateHelpersEvent.php src/View/Template/Subscriber/GetTemplateHelpersListener.php src/View/Template/Exception/HelperNotFound.php docs/view/templates.rst
git commit -m "Doc-deprecate the legacy Contao template component"
```

---

## Task A.4: Switch wizard-template defaults to the new Twig templates

**Files:**
- Modify: `src/Dca/Listener/Wizard/AbstractPickerListener.php:15`
- Modify: `src/Dca/Listener/Wizard/ColorPickerListener.php:17`
- Modify: `src/Dca/Listener/Wizard/PopupWizardListener.php:25`

**Interfaces:**
- Consumes: the three Twig templates created in Task A.1, published under the `@NetzmachtContaoToolkit` Twig namespace (Symfony auto-registers this namespace from `src/Resources/views/` for the `NetzmachtContaoToolkitBundle` bundle — no extra wiring needed, matching Spec 1's "Symfony-Bundle-Legacy-Struktur" note).
- Produces: nothing new (property value change only, same `protected string $template` property).

- [ ] **Step 1: `AbstractPickerListener`**

```php
abstract class AbstractPickerListener extends AbstractWizardListener
{
    /**
     * Template name.
     */
    protected string $template = '@NetzmachtContaoToolkit/backend/wizard_picker.html.twig';
}
```

- [ ] **Step 2: `ColorPickerListener`**

```php
final class ColorPickerListener extends AbstractPickerListener
{
    /**
     * Template name.
     */
    protected string $template = '@NetzmachtContaoToolkit/backend/wizard_color_picker.html.twig';
```

(rest of the class unchanged in this step — Task C.3 adds the deprecation trigger to this same class)

- [ ] **Step 3: `PopupWizardListener`**

```php
final class PopupWizardListener extends AbstractWizardListener
{
    /**
     * Template name.
     */
    protected string $template = '@NetzmachtContaoToolkit/backend/wizard_popup.html.twig';
```

- [ ] **Step 4: Manual backend verification (per Spec 1's Testing section — no automated equivalent exists since no specs cover these listeners' rendered output yet)**

In a Contao backend with this package installed, open a DCA field using the file picker, page picker, color picker and a popup wizard field; confirm the icons and click behavior render identically to before the change (MooTools modal/rainbow-picker JS behavior is unchanged, only the icon markup now comes from `backend_icon()` via Twig instead of `Image::getHtml()` via `.html5`).

- [ ] **Step 5: Commit**

```bash
git add src/Dca/Listener/Wizard/AbstractPickerListener.php src/Dca/Listener/Wizard/ColorPickerListener.php src/Dca/Listener/Wizard/PopupWizardListener.php
git commit -m "Default wizard listeners to the new Twig backend templates"
```

---

## Task A.5: Doc-only deprecate `RequestScopeMatcher`, remove `isInstallRequest()`

**Files:**
- Modify: `src/Routing/RequestScopeMatcher.php`
- Modify: `src/Dca/Listener/SetOperationDataAttributeListener.php:48`
- Create: `spec/Dca/Listener/SetOperationDataAttributeListenerSpec.php` (none existed before this plan)

**Interfaces:**
- Consumes: nothing new.
- Produces: `RequestScopeMatcher` keeps `isFrontendRequest()`/`isBackendRequest()`/`isContaoRequest()` unchanged; `isInstallRequest()` no longer exists.

- [ ] **Step 1: Doc-deprecate the class and remove `isInstallRequest()`**

```php
/**
 * @deprecated Use Contao\CoreBundle\Routing\ScopeMatcher directly. Will be removed in 5.0.
 */
class RequestScopeMatcher
{
```

Delete the entire `isInstallRequest()` method (currently lines 84–100 of `src/Routing/RequestScopeMatcher.php`):

```php
    /**
     * Check if the route of the request is to the install route.
     *
     * If no request is given the current request from the request scope is used.
     *
     * @param Request|null $request Request which should be checked.
     */
    public function isInstallRequest(Request|null $request = null): bool
    {
        $request = $request ?: $this->getCurrentRequest();

        if ($request) {
            return $request->attributes->get('_route') === 'contao_install';
        }

        return false;
    }

```

- [ ] **Step 2: Simplify `SetOperationDataAttributeListener::onLoadDataContainer()`**

```php
    public function onLoadDataContainer(string $dataContainerName): void
    {
        if (! $this->scopeMatcher->isContaoRequest()) {
            return;
        }
```

(the rest of the method body is unchanged)

- [ ] **Step 3: Write a failing spec for the simplified listener (none existed before)**

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Listener;

use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Listener\SetOperationDataAttributeListener;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RuntimeException;

class SetOperationDataAttributeListenerSpec extends ObjectBehavior
{
    public function let(DcaManager $dcaManager, RequestScopeMatcher $scopeMatcher): void
    {
        $this->beConstructedWith($dcaManager, $scopeMatcher);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SetOperationDataAttributeListener::class);
    }

    public function it_does_nothing_outside_a_contao_request(
        RequestScopeMatcher $scopeMatcher,
        DcaManager $dcaManager,
    ): void {
        $scopeMatcher->isContaoRequest()->willReturn(false);
        $dcaManager->getDefinition(Argument::any())->shouldNotBeCalled();

        $this->onLoadDataContainer('tl_example');
    }

    public function it_sets_the_data_operation_attribute_for_toolkit_operations(
        RequestScopeMatcher $scopeMatcher,
        DcaManager $dcaManager,
    ): void {
        $dca = [
            'list' => [
                'operations' => [
                    'toggle' => ['toolkit' => ['state_button' => []]],
                    'edit' => [],
                ],
            ],
        ];

        $definition = new Definition('tl_example', $dca);

        $scopeMatcher->isContaoRequest()->willReturn(true);
        $dcaManager->getDefinition('tl_example')->willReturn($definition);

        $this->onLoadDataContainer('tl_example');

        if ($definition->get(['list', 'operations', 'toggle', 'attributes']) !== 'data-operation="toggle"') {
            throw new RuntimeException('Expected data-operation attribute to be set on the "toggle" operation.');
        }

        if ($definition->get(['list', 'operations', 'edit', 'attributes'], null) !== null) {
            throw new RuntimeException('Did not expect an attribute on the "edit" operation (no toolkit config).');
        }
    }
}
```

- [ ] **Step 4: Run the new spec**

Run: `vendor/bin/phpspec run spec/Dca/Listener/SetOperationDataAttributeListenerSpec.php`
Expected: all examples green.

- [ ] **Step 5: Run the existing `RequestScopeMatcherSpec` to confirm no regression**

Run: `vendor/bin/phpspec run spec/Routing/RequestScopeMatcherSpec.php`
Expected: green (this spec never had an `isInstallRequest()` example, so nothing to remove there).

- [ ] **Step 6: Commit**

```bash
git add src/Routing/RequestScopeMatcher.php src/Dca/Listener/SetOperationDataAttributeListener.php spec/Dca/Listener/SetOperationDataAttributeListenerSpec.php
git commit -m "Deprecate RequestScopeMatcher, remove dead isInstallRequest()"
```

---

## Task A.6: New `Controller\Fragment\*` base classes on top of Contao Core

**Files:**
- Create: `src/Controller/Fragment/IsHiddenTrait.php`
- Create: `src/Controller/Fragment/AbstractContentElementController.php`
- Create: `src/Controller/Fragment/AbstractFrontendModuleController.php`
- Test: Create `spec/Controller/Fragment/ConcreteContentElementController.php`, `spec/Controller/Fragment/AbstractContentElementControllerSpec.php`, `spec/Controller/Fragment/ConcreteFrontendModuleController.php`, `spec/Controller/Fragment/AbstractFrontendModuleControllerSpec.php`

**Interfaces:**
- Consumes: `Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController::getResponse(FragmentTemplate, ContentModel, Request): Response` (abstract, must be implemented), `Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController::getResponse(FragmentTemplate, ModuleModel, Request): Response` (abstract), `Contao\CoreBundle\Twig\FragmentTemplate::{setData(array), getData(): array, getResponse(?Response): Response}` (final class — real instances only, cannot be mocked), `Contao\CoreBundle\Controller\AbstractFragmentController::isBackendScope(?Request): bool` (inherited, protected), `$this->container` (Symfony `AbstractController`, populated via `getSubscribedServices()`).
- Produces: `protected function preGenerate(FragmentTemplate, TModel, Request): Response|null`, `protected function prepareTemplateData(array, Request, TModel): array`, `protected function postGenerate(Response, FragmentTemplate, TModel, Request): Response|null` — the three overridable hooks every consumer subclass uses (mirrors the old `AbstractFragmentController`'s hook names, confirmed by Spec 3 as an intentional migration aid).

- [ ] **Step 1: Create `IsHiddenTrait` for the new namespace**

```php
<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Controller\Fragment;

use Contao\ContentModel;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Symfony\Component\HttpFoundation\Request;

use function time;

/**
 * The IsHiddenTrait provides the isHidden() method to check if a content element must be
 * hidden (invisible, not yet started, already stopped) unless previewed by a backend user.
 */
trait IsHiddenTrait
{
    /**
     * Check if a content element is hidden.
     *
     * @param ContentModel $model   The content element.
     * @param Request      $request The current request.
     */
    protected function isHidden(ContentModel $model, Request $request): bool
    {
        /** @psalm-suppress RiskyTruthyFalsyComparison */
        $isInvisible = $model->invisible
            || ($model->start && $model->start > time())
            || ($model->stop && $model->stop <= time());

        if (! $isInvisible) {
            return false;
        }

        $tokenChecker = $this->container->get('token_checker');

        if ($tokenChecker->hasBackendUser() && $tokenChecker->isPreviewMode()) {
            return false;
        }

        return ! $this->isBackendScope($request);
    }

    /** @return array<string,string> */
    public static function getSubscribedServices(): array
    {
        return [...parent::getSubscribedServices(), 'token_checker' => TokenChecker::class];
    }
}
```

- [ ] **Step 2: Create `Controller\Fragment\AbstractContentElementController`**

```php
<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Controller\Fragment;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController as ContaoAbstractContentElementController;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Slim content element base controller on top of Contao Core's own fragment infrastructure.
 *
 * Combines isHidden(), preGenerate(), the template-data hook and postGenerate() in the fixed
 * order below, mirroring the (now deprecated) Controller\ContentElement\AbstractContentElementController
 * hook names to ease migration.
 */
abstract class AbstractContentElementController extends ContaoAbstractContentElementController
{
    use IsHiddenTrait;

    #[Override]
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

    /**
     * Pre-generate hook. Return a Response to short-circuit the default rendering
     * (e.g. a redirect or a file download instead of the normal template).
     */
    protected function preGenerate(FragmentTemplate $template, ContentModel $model, Request $request): Response|null
    {
        return null;
    }

    /**
     * Prepare the template data before rendering. Must return the (optionally modified) data.
     *
     * @param array<string,mixed> $data
     *
     * @return array<string,mixed>
     */
    protected function prepareTemplateData(array $data, Request $request, ContentModel $model): array
    {
        return $data;
    }

    /**
     * Post-generate hook. Return a Response to replace the default rendered response
     * (e.g. to set additional cache-control directives), or null to keep it unchanged.
     */
    protected function postGenerate(
        Response $response,
        FragmentTemplate $template,
        ContentModel $model,
        Request $request,
    ): Response|null {
        return null;
    }
}
```

- [ ] **Step 3: Create `Controller\Fragment\AbstractFrontendModuleController`**

```php
<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Controller\Fragment;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController as ContaoAbstractFrontendModuleController;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Slim frontend module base controller on top of Contao Core's own fragment infrastructure.
 *
 * Backend-scope wildcard rendering is already handled automatically by
 * Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController::__invoke()
 * before getResponse() is even called — no isHidden()/wildcard logic is needed here.
 */
abstract class AbstractFrontendModuleController extends ContaoAbstractFrontendModuleController
{
    #[Override]
    final protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $response = $this->preGenerate($template, $model, $request);
        if ($response !== null) {
            return $response;
        }

        $template->setData($this->prepareTemplateData($template->getData(), $request, $model));
        $response = $template->getResponse();

        return $this->postGenerate($response, $template, $model, $request) ?? $response;
    }

    /**
     * Pre-generate hook. Return a Response to short-circuit the default rendering.
     */
    protected function preGenerate(FragmentTemplate $template, ModuleModel $model, Request $request): Response|null
    {
        return null;
    }

    /**
     * Prepare the template data before rendering. Must return the (optionally modified) data.
     *
     * @param array<string,mixed> $data
     *
     * @return array<string,mixed>
     */
    protected function prepareTemplateData(array $data, Request $request, ModuleModel $model): array
    {
        return $data;
    }

    /**
     * Post-generate hook. Return a Response to replace the default rendered response,
     * or null to keep it unchanged.
     */
    protected function postGenerate(
        Response $response,
        FragmentTemplate $template,
        ModuleModel $model,
        Request $request,
    ): Response|null {
        return null;
    }
}
```

- [ ] **Step 4: Create the content-element test fixture**

`spec/Controller/Fragment/ConcreteContentElementController.php`:

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Controller\Fragment;

use Closure;
use Contao\ContentModel;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractContentElementController;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ConcreteContentElementController extends AbstractContentElementController
{
    public Closure|null $preGenerateCallback = null;
    public Closure|null $prepareTemplateDataCallback = null;
    public Closure|null $postGenerateCallback = null;

    public function callGetResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        return $this->getResponse($template, $model, $request);
    }

    #[Override]
    protected function preGenerate(FragmentTemplate $template, ContentModel $model, Request $request): Response|null
    {
        return $this->preGenerateCallback
            ? ($this->preGenerateCallback)($template, $model, $request)
            : parent::preGenerate($template, $model, $request);
    }

    #[Override]
    protected function prepareTemplateData(array $data, Request $request, ContentModel $model): array
    {
        return $this->prepareTemplateDataCallback
            ? ($this->prepareTemplateDataCallback)($data, $request, $model)
            : parent::prepareTemplateData($data, $request, $model);
    }

    #[Override]
    protected function postGenerate(
        Response $response,
        FragmentTemplate $template,
        ContentModel $model,
        Request $request,
    ): Response|null {
        return $this->postGenerateCallback
            ? ($this->postGenerateCallback)($response, $template, $model, $request)
            : parent::postGenerate($response, $template, $model, $request);
    }
}
```

- [ ] **Step 5: Write the content-element spec**

`spec/Controller/Fragment/AbstractContentElementControllerSpec.php`:

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Controller\Fragment;

use Contao\ContentModel;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractContentElementController;
use PhpSpec\ObjectBehavior;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function time;

class AbstractContentElementControllerSpec extends ObjectBehavior
{
    public function let(ContainerInterface $container): void
    {
        $this->beAnInstanceOf(ConcreteContentElementController::class);
        $this->beConstructedWith();
        $this->setContainer($container->getWrappedObject());
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AbstractContentElementController::class);
    }

    public function it_returns_an_empty_response_for_an_invisible_element(Request $request): void
    {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->invisible = true;

        $template = new FragmentTemplate('content_element/text', static fn (): Response => new Response('should not be called'));

        $this->callGetResponse($template, $model, $request)->getContent()->shouldReturn('');
    }

    public function it_renders_a_visible_element(ContainerInterface $container, TokenChecker $tokenChecker, Request $request): void
    {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->invisible = false;

        $rendered = false;
        $template = new FragmentTemplate(
            'content_element/text',
            static function (FragmentTemplate $t, Response|null $pre) use (&$rendered): Response {
                $rendered = true;

                return $pre ?? new Response('rendered');
            },
        );

        $this->callGetResponse($template, $model, $request)->getContent()->shouldReturn('rendered');

        if (! $rendered) {
            throw new \RuntimeException('Expected the template response closure to be called.');
        }
    }

    public function it_shows_a_hidden_element_in_backend_preview_mode(
        ContainerInterface $container,
        TokenChecker $tokenChecker,
        Request $request,
    ): void {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->invisible = true;

        $container->get('token_checker')->willReturn($tokenChecker->getWrappedObject());
        $tokenChecker->hasBackendUser()->willReturn(true);
        $tokenChecker->isPreviewMode()->willReturn(true);

        $template = new FragmentTemplate('content_element/text', static fn (): Response => new Response('preview'));

        $this->callGetResponse($template, $model, $request)->getContent()->shouldReturn('preview');
    }

    public function it_short_circuits_via_pre_generate(Request $request): void
    {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->invisible = false;

        $this->preGenerateCallback = static fn (): Response => new Response('redirected', 302);

        $template = new FragmentTemplate('content_element/text', static fn (): Response => new Response('should not be called'));

        $this->callGetResponse($template, $model, $request)->getStatusCode()->shouldReturn(302);
    }

    public function it_applies_prepare_template_data(Request $request): void
    {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->invisible = false;

        $this->prepareTemplateDataCallback = static function (array $data): array {
            $data['foo'] = 'bar';

            return $data;
        };

        $seen     = null;
        $template = new FragmentTemplate(
            'content_element/text',
            static function (FragmentTemplate $t) use (&$seen): Response {
                $seen = $t->getData();

                return new Response('rendered');
            },
        );

        $this->callGetResponse($template, $model, $request);

        if (($seen['foo'] ?? null) !== 'bar') {
            throw new \RuntimeException('Expected prepareTemplateData() to have added "foo" => "bar".');
        }
    }

    public function it_allows_post_generate_to_replace_the_response(Request $request): void
    {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->invisible = false;

        $this->postGenerateCallback = static fn (Response $response): Response => $response->setStatusCode(201);

        $template = new FragmentTemplate('content_element/text', static fn (): Response => new Response('rendered'));

        $this->callGetResponse($template, $model, $request)->getStatusCode()->shouldReturn(201);
    }
}
```

- [ ] **Step 6: Run the content-element spec**

Run: `vendor/bin/phpspec run spec/Controller/Fragment/AbstractContentElementControllerSpec.php`
Expected: all examples green.

- [ ] **Step 7: Create the frontend-module test fixture**

`spec/Controller/Fragment/ConcreteFrontendModuleController.php`:

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Controller\Fragment;

use Closure;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractFrontendModuleController;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ConcreteFrontendModuleController extends AbstractFrontendModuleController
{
    public Closure|null $preGenerateCallback = null;
    public Closure|null $postGenerateCallback = null;

    public function callGetResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        return $this->getResponse($template, $model, $request);
    }

    #[Override]
    protected function preGenerate(FragmentTemplate $template, ModuleModel $model, Request $request): Response|null
    {
        return $this->preGenerateCallback
            ? ($this->preGenerateCallback)($template, $model, $request)
            : parent::preGenerate($template, $model, $request);
    }

    #[Override]
    protected function postGenerate(
        Response $response,
        FragmentTemplate $template,
        ModuleModel $model,
        Request $request,
    ): Response|null {
        return $this->postGenerateCallback
            ? ($this->postGenerateCallback)($response, $template, $model, $request)
            : parent::postGenerate($response, $template, $model, $request);
    }
}
```

- [ ] **Step 8: Write the frontend-module spec**

`spec/Controller/Fragment/AbstractFrontendModuleControllerSpec.php`:

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Controller\Fragment;

use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractFrontendModuleController;
use PhpSpec\ObjectBehavior;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AbstractFrontendModuleControllerSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beAnInstanceOf(ConcreteFrontendModuleController::class);
        $this->beConstructedWith();
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AbstractFrontendModuleController::class);
    }

    public function it_renders_the_module(Request $request): void
    {
        $model = (new ReflectionClass(ModuleModel::class))->newInstanceWithoutConstructor();

        $template = new FragmentTemplate('content_element/list', static fn (): Response => new Response('rendered'));

        $this->callGetResponse($template, $model, $request)->getContent()->shouldReturn('rendered');
    }

    public function it_short_circuits_via_pre_generate(Request $request): void
    {
        $model = (new ReflectionClass(ModuleModel::class))->newInstanceWithoutConstructor();

        $this->preGenerateCallback = static fn (): Response => new Response('redirected', 302);

        $template = new FragmentTemplate('content_element/list', static fn (): Response => new Response('should not be called'));

        $this->callGetResponse($template, $model, $request)->getStatusCode()->shouldReturn(302);
    }

    public function it_allows_post_generate_to_replace_the_response(Request $request): void
    {
        $model = (new ReflectionClass(ModuleModel::class))->newInstanceWithoutConstructor();

        $this->postGenerateCallback = static fn (Response $response): Response => $response->setStatusCode(201);

        $template = new FragmentTemplate('content_element/list', static fn (): Response => new Response('rendered'));

        $this->callGetResponse($template, $model, $request)->getStatusCode()->shouldReturn(201);
    }
}
```

- [ ] **Step 9: Run the frontend-module spec**

Run: `vendor/bin/phpspec run spec/Controller/Fragment/AbstractFrontendModuleControllerSpec.php`
Expected: all examples green.

- [ ] **Step 10: Commit**

```bash
git add src/Controller/Fragment spec/Controller/Fragment
git commit -m "Add new Controller\\Fragment base classes on top of Contao Core's fragment controllers"
```

---

## Task A.7: Runtime-trigger the implicit `fe:` auto-prefix, doc-deprecate the old Fragment-Controller base classes

**Files:**
- Modify: `src/Controller/AbstractFragmentController.php:222` (`render()`)
- Modify: `src/Controller/AbstractFragmentController.php` (class docblock), `src/Controller/ContentElement/AbstractContentElementController.php` (class docblock), `src/Controller/FrontendModule/AbstractFrontendModuleController.php` (class docblock), `src/Controller/Hybrid/AbstractHybridController.php` (class docblock)
- Test: Modify `spec/Controller/ContentElement/AbstractContentElementControllerSpec.php`, `spec/Controller/FrontendModule/AbstractFrontendModuleControllerSpec.php`, `spec/Controller/Hybrid/AbstractHybridControllerSpec.php` (add deprecation-warning coverage; existing examples are left as-is and will now also silently trigger the warning, which is expected and harmless — matches Spec 9's already-accepted tradeoff for the same reason)

**Interfaces:**
- Consumes: `DeprecationSpecHelper` (Task A.2).
- Produces: nothing new — behavior unchanged for template names that already carry an explicit `.twig`/`toolkit:`/`fe:`/`be:` marker; only bare names now also emit a warning.

- [ ] **Step 1: Add the runtime trigger to the implicit `fe:` prefix**

In `src/Controller/AbstractFragmentController.php`, add the import (the file already has a `use function ...;` block):

```php
use function array_pad;
use function array_unshift;
use function array_values;
use function implode;
use function is_array;
use function ltrim;
use function sprintf;
use function str_ends_with;
use function str_starts_with;
use function strrchr;
use function substr;
use function trigger_deprecation;
use function trim;
```

Change `render()`:

```php
    protected function render(string $templateName, array $data): string
    {
        if (
            ! str_ends_with($templateName, '.twig')
            && ! str_starts_with($templateName, 'toolkit:')
            && ! str_starts_with($templateName, 'fe:')
            && ! str_starts_with($templateName, 'be:')
        ) {
            trigger_deprecation(
                'netzmacht/contao-toolkit',
                '4.1',
                'Implicitly prefixing template name "%s" with "fe:" is deprecated. Pass an explicit scope prefix or a ".twig" template name instead.',
                $templateName,
            );

            $templateName = 'fe:' . $templateName;
        }

        return $this->templateRenderer->render($templateName, $data);
    }
```

- [ ] **Step 2: Doc-deprecate the four old Fragment-Controller base classes**

`src/Controller/AbstractFragmentController.php`:

```php
/**
 * This class a the base class for the base fragment controller provided by the Toolkit.
 *
 * @deprecated Use Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractContentElementController or
 *             AbstractFrontendModuleController instead. Will be removed in 5.0.
 *
 * @template TModel of Model
 */
abstract class AbstractFragmentController implements FragmentOptionsAwareInterface
```

`src/Controller/ContentElement/AbstractContentElementController.php`:

```php
/**
 * Class AbstractContentElementController is the base fragment controller for content elements
 *
 * @deprecated Use Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractContentElementController instead.
 *             Will be removed in 5.0.
 *
 * @extends AbstractFragmentController<ContentModel>
 */
abstract class AbstractContentElementController extends AbstractFragmentController
```

`src/Controller/FrontendModule/AbstractFrontendModuleController.php`:

```php
/**
 * Class AbstractModuleController is the base fragment controller for frontend modules
 *
 * @deprecated Use Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractFrontendModuleController instead.
 *             Will be removed in 5.0.
 *
 * @extends AbstractFragmentController<ModuleModel>
 */
abstract class AbstractFrontendModuleController extends AbstractFragmentController
```

`src/Controller/Hybrid/AbstractHybridController.php`:

```php
/**
 * Class AbstractHybridController is a base controller for hybrid fragment controllers.
 *
 * Hybrid fragment controllers might be used as frontend modules or content elements. Be aware that you have to register
 * the methods renderAsContentElement() and renderAsFrontendModule() as fragment controllers.
 *
 * @deprecated There is no Hybrid-controller successor in the new Controller\Fragment\* namespace — split your
 *             controller into a Controller\Fragment\AbstractContentElementController and/or
 *             Controller\Fragment\AbstractFrontendModuleController subclass instead. Will be removed in 5.0.
 *
 * @extends AbstractFragmentController<ContentModel|ModuleModel>
 */
abstract class AbstractHybridController extends AbstractFragmentController
```

- [ ] **Step 3: Add deprecation-warning coverage to the existing content-element spec**

In `spec/Controller/ContentElement/AbstractContentElementControllerSpec.php`, add the trait use and one new example (all other examples are unchanged):

```php
use spec\Netzmacht\Contao\Toolkit\DeprecationSpecHelper;

class AbstractContentElementControllerSpec extends ObjectBehavior
{
    use DeprecationSpecHelper;

    // ... existing let()/letGo()/it_*() methods unchanged ...

    public function it_triggers_a_deprecation_warning_for_the_implicit_fe_prefix(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TokenChecker $tokenChecker,
        TemplateRenderer $templateRenderer,
    ): void {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->invisible = false;
        $model->cssID     = serialize(['foo', 'bar']);
        $model->headline  = serialize(['value' => 'Headline', 'unit' => 'h1']);

        $scopeMatcher->isBackendRequest($request)->willReturn(false);
        $tokenChecker->hasBackendUser()->willReturn(false);
        $templateRenderer->render(Argument::cetera())->willReturn('HTML');

        $messages = $this->captureDeprecations(function () use ($request, $model): void {
            $this->__invoke($request, $model, 'main');
        });

        $this->assertDeprecationTriggered($messages, 'Implicitly prefixing template name');
    }
}
```

- [ ] **Step 4: Mirror the same new example in the frontend-module and hybrid specs**

In `spec/Controller/FrontendModule/AbstractFrontendModuleControllerSpec.php` and `spec/Controller/Hybrid/AbstractHybridControllerSpec.php`, add `use spec\Netzmacht\Contao\Toolkit\DeprecationSpecHelper;`, `use DeprecationSpecHelper;` inside the class, and one analogous `it_triggers_a_deprecation_warning_for_the_implicit_fe_prefix()` example calling the respective invocation method (`__invoke()` for the frontend-module spec, `renderAsContentElement()` or `renderAsFrontendModule()` for the hybrid spec) with a non-`.twig`/non-prefixed template outcome, asserting via `assertDeprecationTriggered($messages, 'Implicitly prefixing template name')`.

- [ ] **Step 5: Run the affected specs**

Run: `vendor/bin/phpspec run spec/Controller/ContentElement/AbstractContentElementControllerSpec.php spec/Controller/FrontendModule/AbstractFrontendModuleControllerSpec.php spec/Controller/Hybrid/AbstractHybridControllerSpec.php`
Expected: all examples green (including all pre-existing ones — they now also silently trigger the same warning, which does not fail phpspec).

- [ ] **Step 6: Commit**

```bash
git add src/Controller/AbstractFragmentController.php src/Controller/ContentElement/AbstractContentElementController.php src/Controller/FrontendModule/AbstractFrontendModuleController.php src/Controller/Hybrid/AbstractHybridController.php spec/Controller
git commit -m "Deprecate implicit fe: template prefix and the old Fragment-Controller base classes"
```

---

## Task A.8: Docs and CHANGELOG for cluster A (points 1+2+3)

**Files:**
- Create: `docs/controller/index.rst`, `docs/controller/fragment.rst`
- Create: `docs/routing/index.rst`, `docs/routing/scope-matcher.rst` (no docs page exists for `RequestScopeMatcher` today — new page per the migration's documentation-gap inventory)
- Modify: `docs/index.rst:16` (toctree)
- Modify: `CHANGELOG.md`

**Interfaces:** none (documentation only).

- [ ] **Step 1: Create `docs/routing/index.rst` and `docs/routing/scope-matcher.rst`**

`docs/routing/index.rst`:

```rst
Routing
=======

.. _contents:

.. toctree::
   :maxdepth: 1

   scope-matcher
```

`docs/routing/scope-matcher.rst`:

```rst
RequestScopeMatcher
=====================

.. important::

   ``Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher`` is deprecated as of 4.1.0 and will be
   removed in 5.0.0. Use ``Contao\CoreBundle\Routing\ScopeMatcher`` directly instead — since
   Contao 5, its ``isFrontendRequest()``/``isBackendRequest()``/``isContaoRequest()`` methods
   already accept an optional ``?Request`` argument and fall back to the current request from the
   request stack themselves, making the toolkit's own wrapper redundant.

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\CoreBundle\Routing\ScopeMatcher;

   final class MyService
   {
       public function __construct(private readonly ScopeMatcher $scopeMatcher)
       {
       }

       public function example(): bool
       {
           // No $request argument needed - falls back to the request stack internally.
           return $this->scopeMatcher->isBackendRequest();
       }
   }

``RequestScopeMatcher::isInstallRequest()`` has been removed outright (not just deprecated) — the
``contao_install`` route it checked for no longer exists since Contao 5, so the method was already
permanently returning ``false`` under this package's ``^5.7`` floor.
```

- [ ] **Step 2: Create `docs/controller/index.rst`**

```rst
Controller
==========

Toolkit provides base fragment-controller classes to build content elements and frontend modules on top of Contao's
own fragment infrastructure.

.. _contents:

.. toctree::
   :maxdepth: 1

   fragment
```

- [ ] **Step 3: Create `docs/controller/fragment.rst`**

```rst
Fragment controllers
=====================

.. important::

   The legacy ``Netzmacht\Contao\Toolkit\Controller\AbstractFragmentController`` and its
   ``ContentElement``/``FrontendModule``/``Hybrid`` subclasses are deprecated as of 4.1.0 and will
   be removed in 5.0.0. New controllers should extend the classes documented on this page instead.

``Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractContentElementController`` and
``AbstractFrontendModuleController`` are slim base classes built directly on top of
``Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController`` and
``Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController``. They combine
visibility checks, an optional pre-generate short-circuit, template-data preparation and an
optional post-generate hook into the familiar sequence:

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\ContentModel;
   use Contao\CoreBundle\Twig\FragmentTemplate;
   use Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractContentElementController;
   use Symfony\Component\HttpFoundation\Request;
   use Symfony\Component\HttpFoundation\Response;

   final class TextController extends AbstractContentElementController
   {
       protected function prepareTemplateData(array $data, Request $request, ContentModel $model): array
       {
           $data['text'] = $model->text;

           return $data;
       }
   }

Neither class requires a constructor — additional dependencies are declared via Symfony's
``ServiceSubscriberInterface`` (``getSubscribedServices()``), the same mechanism Contao Core's own
base classes use.

For frontend modules, the backend-preview "wildcard" placeholder is rendered automatically by
Contao Core before ``getResponse()`` is even called — no extra code needed. For content elements,
use the opt-in ``Netzmacht\Contao\Toolkit\Controller\Fragment\RenderBackendWildcardTrait`` (see
:doc:`render-backend-wildcard`) if your element needs the same placeholder.
```

- [ ] **Step 4: Register the new pages in the docs toctree**

In `docs/index.rst`, change:

```rst
.. toctree::
   :maxdepth: 2

   introduction
   data/index
   dca/index
   view/index
   insert-tags/index
```

to:

```rst
.. toctree::
   :maxdepth: 2

   introduction
   data/index
   dca/index
   view/index
   controller/index
   routing/index
   insert-tags/index
```

- [ ] **Step 5: Add CHANGELOG entries**

In `CHANGELOG.md`, under `[Unreleased]` (create a `[4.1.0]` subsection if the plan's cluster tasks are the first to add content there — later clusters append to the same subsection):

```markdown
[Unreleased]
------------

[4.1.0]

### Added

 - New `Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractContentElementController` and
   `AbstractFrontendModuleController` base classes built directly on Contao Core's own fragment
   infrastructure. See `docs/controller/fragment.rst`.
 - New Twig backend-wizard templates (`@NetzmachtContaoToolkit/backend/wizard_picker.html.twig`,
   `wizard_color_picker.html.twig`, `wizard_popup.html.twig`), now the default for
   `AbstractPickerListener`, `ColorPickerListener` and `PopupWizardListener`.

### Changed

 - Raised `contao/core-bundle` requirement to `^5.7`. Support for Contao 4.13 is dropped in 4.1.0.

### Deprecated

 - `Netzmacht\Contao\Toolkit\View\Template` and its implementations (`FrontendTemplate`,
   `BackendTemplate`, `TemplateTrait`, `TemplateFactory`, `ToolkitTemplateFactory`,
   `GetTemplateHelpersEvent`/`GetTemplateHelpersListener`, `HelperNotFound`). Use native Twig
   templates instead.
 - Rendering legacy Contao templates via `DelegatingTemplateRenderer` (`be:`/`fe:`/`toolkit:`
   prefixed names) and the implicit `fe:` auto-prefix in
   `Controller\AbstractFragmentController::render()`. Both now trigger a runtime deprecation
   warning when actually used.
 - `Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher`. Use
   `Contao\CoreBundle\Routing\ScopeMatcher` directly. `isInstallRequest()` is removed outright
   (already dead code under the new `^5.7` floor).
 - `Netzmacht\Contao\Toolkit\Controller\AbstractFragmentController` and its
   `ContentElement`/`FrontendModule`/`Hybrid` subclasses. Use the new
   `Controller\Fragment\AbstractContentElementController`/`AbstractFrontendModuleController`
   instead.
```

- [ ] **Step 6: Commit**

```bash
git add docs/controller docs/routing docs/index.rst CHANGELOG.md
git commit -m "Add docs and CHANGELOG entries for the template/RequestScopeMatcher/Fragment-Controller cluster"
```

---

## Task B.1: Deprecate the `InsertTag\*` component

**Files:**
- Modify: `src/InsertTag/AbstractInsertTagParser.php`
- Modify: `src/InsertTag/AbstractSingleInsertTagParser.php`
- Modify: `src/InsertTag/ArgumentParserPlugin.php`
- Modify: `src/InsertTag/ArgumentParser.php:37` (`create()`)
- Test: Create `spec/InsertTag/ConcreteInsertTagParser.php`, `spec/InsertTag/AbstractInsertTagParserSpec.php`; modify `spec/InsertTag/ArgumentParserSpec.php`

**Interfaces:**
- Consumes: `DeprecationSpecHelper` (Task A.2).
- Produces: nothing new — `AbstractInsertTagParser::replace()`/`ArgumentParser::parse()` behavior is unchanged, only a warning is added.

- [ ] **Step 1: Deprecate `AbstractInsertTagParser` with a runtime-trigger constructor**

```php
<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\InsertTag;

use function explode;
use function trigger_deprecation;

/**
 * @deprecated Use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag instead. Will be removed in 5.0.
 */
abstract class AbstractInsertTagParser
{
    public function __construct()
    {
        trigger_deprecation(
            'netzmacht/contao-toolkit',
            '4.1',
            'The InsertTag component (%s) is deprecated. Use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag instead.',
            static::class,
        );
    }

    /**
     * Replace an insert tag.
     * ... (rest of the class body unchanged)
     */
```

(only the class docblock and the new constructor are added; `replace()`, `supports()`, `parseArguments()`, `parseTag()` stay exactly as they are today)

- [ ] **Step 2: Doc-deprecate `AbstractSingleInsertTagParser` (no separate trigger — already fires via the parent constructor)**

```php
/**
 * @deprecated Use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag instead. Will be removed in 5.0.
 */
abstract class AbstractSingleInsertTagParser extends AbstractInsertTagParser
```

- [ ] **Step 3: Doc-deprecate `ArgumentParserPlugin` (no separate trigger — fires via `ArgumentParser::create()` in Step 4, the trait's only supported way to obtain a parser)**

```php
/**
 * The argument parser plugin parses the arguments by a default schema.
 *
 * Following parsing strategy is used:
 * - Splits query by '::' into arguments
 * - Checks if any argument contains an url style query (foo?bar=baz)
 *
 * @deprecated Use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag and
 *             ResolvedInsertTag::getParameters() instead. Will be removed in 5.0.
 */
trait ArgumentParserPlugin
```

- [ ] **Step 4: Deprecate `ArgumentParser::create()`**

Add the import (after the existing `use function` block, alphabetically):

```php
use function array_key_exists;
use function explode;
use function is_array;
use function is_string;
use function parse_str;
use function sprintf;
use function str_replace;
use function trigger_deprecation;
```

Add the class docblock and update `create()`:

```php
/**
 * @deprecated Use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag and
 *             ResolvedInsertTag::getParameters() instead. Will be removed in 5.0.
 */
final class ArgumentParser
{
    // ...

    public static function create(): self
    {
        trigger_deprecation(
            'netzmacht/contao-toolkit',
            '4.1',
            'The InsertTag component (%s) is deprecated. Use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag instead.',
            self::class,
        );

        return new self();
    }
```

- [ ] **Step 5: Add a concrete test fixture for `AbstractInsertTagParser`**

`spec/InsertTag/ConcreteInsertTagParser.php`:

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\InsertTag;

use Netzmacht\Contao\Toolkit\InsertTag\AbstractInsertTagParser;
use Override;

final class ConcreteInsertTagParser extends AbstractInsertTagParser
{
    #[Override]
    protected function supports(string $tag, bool $cache): bool
    {
        return $tag === 'foo';
    }

    #[Override]
    protected function parseArguments(string $query): array
    {
        return [$query];
    }

    #[Override]
    protected function parseTag(array $arguments, string $tag, string $raw): bool|string
    {
        return 'parsed:' . $tag;
    }
}
```

- [ ] **Step 6: Write the `AbstractInsertTagParser` spec**

`spec/InsertTag/AbstractInsertTagParserSpec.php`:

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\InsertTag;

use Netzmacht\Contao\Toolkit\InsertTag\AbstractInsertTagParser;
use PhpSpec\ObjectBehavior;
use spec\Netzmacht\Contao\Toolkit\DeprecationSpecHelper;

class AbstractInsertTagParserSpec extends ObjectBehavior
{
    use DeprecationSpecHelper;

    public function let(): void
    {
        $this->beAnInstanceOf(ConcreteInsertTagParser::class);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AbstractInsertTagParser::class);
    }

    public function it_triggers_a_deprecation_warning_when_instantiated(): void
    {
        $messages = $this->captureDeprecations(function (): void {
            $this->getWrappedObject();
        });

        $this->assertDeprecationTriggered($messages, 'InsertTag component');
    }

    public function it_still_replaces_a_supported_tag(): void
    {
        $this->replace('foo::bar')->shouldReturn('parsed:foo');
    }

    public function it_still_returns_false_for_an_unsupported_tag(): void
    {
        $this->replace('unsupported::bar')->shouldReturn(false);
    }
}
```

- [ ] **Step 7: Add a deprecation-warning example to the existing `ArgumentParserSpec`**

In `spec/InsertTag/ArgumentParserSpec.php`, add the imports and one new example (all existing examples stay unchanged):

```php
namespace spec\Netzmacht\Contao\Toolkit\InsertTag;

use Netzmacht\Contao\Toolkit\InsertTag\ArgumentParser;
use PhpSpec\ObjectBehavior;
use RuntimeException;
use spec\Netzmacht\Contao\Toolkit\DeprecationSpecHelper;

final class ArgumentParserSpec extends ObjectBehavior
{
    use DeprecationSpecHelper;

    // ... existing examples unchanged ...

    public function it_triggers_a_deprecation_warning_via_create(): void
    {
        $messages = $this->captureDeprecations(static function (): void {
            ArgumentParser::create();
        });

        $this->assertDeprecationTriggered($messages, 'InsertTag component');
    }
}
```

- [ ] **Step 8: Run the affected specs**

Run: `vendor/bin/phpspec run spec/InsertTag/AbstractInsertTagParserSpec.php spec/InsertTag/ArgumentParserSpec.php`
Expected: all examples green.

- [ ] **Step 9: Commit**

```bash
git add src/InsertTag spec/InsertTag
git commit -m "Deprecate the InsertTag component in favor of Contao's native #[AsInsertTag]"
```

---

## Task B.2: Rewrite `docs/insert-tags/index.rst`, CHANGELOG entry

The current page describes a `Replacer`/`Parser` API already removed by commit `5c2b9ff` before this plan — it is simply wrong today, not just outdated.

**Files:**
- Modify: `docs/insert-tags/index.rst` (full rewrite)
- Modify: `CHANGELOG.md`

**Interfaces:** none (documentation only).

- [ ] **Step 1: Replace the full content of `docs/insert-tags/index.rst`**

```rst
Insert tags
===========

Contao provides a native, attribute-based way to register insert tags since Contao 5.0. This is the recommended way
to implement custom insert tags — use it instead of Toolkit's own (deprecated) ``InsertTag`` classes.

Registering an insert tag
--------------------------

Mark an invokable service method with ``#[AsInsertTag('name')]``. The attribute is repeatable and can be placed on
the class or on individual methods.

.. code-block:: php

   <?php

   declare(strict_types=1);

   namespace App\InsertTag;

   use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag;
   use Contao\CoreBundle\InsertTag\InsertTagResult;
   use Contao\CoreBundle\InsertTag\OutputType;
   use Contao\CoreBundle\InsertTag\ResolvedInsertTag;

   #[AsInsertTag('smiley')]
   final class SmileyInsertTag
   {
       public function __invoke(ResolvedInsertTag $insertTag): InsertTagResult
       {
           $mood = $insertTag->getParameters()->get(0) ?? 'happy';

           return new InsertTagResult($this->renderSmiley($mood), OutputType::html);
       }

       private function renderSmiley(string $mood): string
       {
           // ...
       }
   }

Reliable parameter access
--------------------------

``ResolvedInsertTag::getParameters()`` returns a ``ResolvedParameters`` value object which replaces what Toolkit's
own ``ArgumentParser``/``AbstractSingleInsertTagParser`` tried to provide on top of the raw tag string:

.. code-block:: php

   <?php

   $parameters = $insertTag->getParameters();

   $parameters->get(0);            // First positional parameter.
   $parameters->all();             // All positional parameters as a list.
   $parameters->get('name');       // Named parameter ("name=value" convention), if present.
   $parameters->getScalar(0);      // Automatically cast to int/float where applicable.

Named parameters, nested insert-tag resolution and caching metadata (``InsertTagResult::withExpiresAt()``,
``withCacheTags()``) are all handled natively — no manual query parsing is required.

.. _insert-tags-deprecated:

Deprecated: Toolkit's own `InsertTag` classes
-----------------------------------------------

.. important::

   ``Netzmacht\Contao\Toolkit\InsertTag\AbstractInsertTagParser``, ``AbstractSingleInsertTagParser``,
   ``ArgumentParser`` and ``ArgumentParserPlugin`` are deprecated as of 4.1.0 and will be removed in 5.0.0.
   Instantiating ``AbstractInsertTagParser`` (directly or via a subclass) or calling ``ArgumentParser::create()``
   triggers a runtime deprecation warning. Migrate to ``#[AsInsertTag]`` as shown above.

These classes were originally built to provide reliable parameter access on top of Contao's historic raw
``replaceInsertTags`` hook string. Contao's native insert-tag system now covers this natively and more, so no
replacement abstraction is provided by Toolkit — register your insert tag as a native Contao service instead.
```

- [ ] **Step 2: Add the CHANGELOG entry**

In `CHANGELOG.md`, under the `[4.1.0]` → `Deprecated` section started in Task A.8, add:

```markdown
 - `Netzmacht\Contao\Toolkit\InsertTag\AbstractInsertTagParser`, `AbstractSingleInsertTagParser`,
   `ArgumentParser`, `ArgumentParserPlugin`. Use Contao's native
   `Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag` instead. See
   `docs/insert-tags/index.rst`.
```

- [ ] **Step 3: Commit**

```bash
git add docs/insert-tags/index.rst CHANGELOG.md
git commit -m "Rewrite insert-tags docs for #[AsInsertTag], document InsertTag component deprecation"
```

---

## Task C.1: Fix `DatabaseRowUpdater::hasUserAccess()` to use Symfony's permission voter (internal correction, not a deprecation)

**Files:**
- Modify: `src/Data/Updater/DatabaseRowUpdater.php:6,72-82`
- Test: Create `spec/Data/Updater/DatabaseRowUpdaterSpec.php` (none exists today)

**Interfaces:**
- Consumes: `Symfony\Bundle\SecurityBundle\Security::isGranted(string, mixed): bool` (already injected as `$this->security`, no new constructor dependency).
- Produces: `hasUserAccess(string, string): bool` signature unchanged, same observable true/false outcome for `alexf`-permission checks.

- [ ] **Step 1: Swap the legacy `BackendUser::hasAccess()` call for `Security::isGranted()`**

Change the imports (remove the now-unused `Contao\BackendUser` import, add the permission constant):

```php
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\Versions;
use Doctrine\DBAL\Connection;
```

Change `hasUserAccess()`:

```php
    /**
     * Check if user has access.
     *
     * @param string $dataContainerName Data container name.
     * @param string $columnName        Column name.
     */
    #[Override]
    public function hasUserAccess(string $dataContainerName, string $columnName): bool
    {
        return $this->security->isGranted(
            ContaoCorePermissions::USER_CAN_EDIT_FIELD_OF_TABLE,
            $dataContainerName . '::' . $columnName,
        );
    }
```

- [ ] **Step 2: Write the spec (none existed for this class before)**

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Data\Updater;

use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Toolkit\Callback\Invoker;
use Netzmacht\Contao\Toolkit\Data\Updater\DatabaseRowUpdater;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use PhpSpec\ObjectBehavior;
use Symfony\Bundle\SecurityBundle\Security;

class DatabaseRowUpdaterSpec extends ObjectBehavior
{
    public function let(Security $security, Connection $connection, DcaManager $dcaManager, Invoker $invoker): void
    {
        $this->beConstructedWith($security, $connection, $dcaManager, $invoker);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DatabaseRowUpdater::class);
    }

    public function it_grants_access_when_the_security_voter_grants_it(Security $security): void
    {
        $security->isGranted('contao_user.alexf', 'tl_example::title')->willReturn(true);

        $this->hasUserAccess('tl_example', 'title')->shouldReturn(true);
    }

    public function it_denies_access_when_the_security_voter_denies_it(Security $security): void
    {
        $security->isGranted('contao_user.alexf', 'tl_example::title')->willReturn(false);

        $this->hasUserAccess('tl_example', 'title')->shouldReturn(false);
    }
}
```

- [ ] **Step 3: Run the new spec**

Run: `vendor/bin/phpspec run spec/Data/Updater/DatabaseRowUpdaterSpec.php`
Expected: all examples green.

- [ ] **Step 4: Commit**

```bash
git add src/Data/Updater/DatabaseRowUpdater.php spec/Data/Updater/DatabaseRowUpdaterSpec.php
git commit -m "DatabaseRowUpdater: use Security::isGranted() instead of legacy BackendUser::hasAccess()"
```

---

## Task C.2: Deprecate `StateButtonCallbackListener`

**Files:**
- Modify: `src/Dca/Listener/Button/StateButtonCallbackListener.php`
- Test: Create `spec/Dca/Listener/Button/StateButtonCallbackListenerSpec.php` (none exists today)

**Interfaces:**
- Consumes: `DeprecationSpecHelper` (Task A.2).
- Produces: nothing new.

- [ ] **Step 1: Add the class docblock and the runtime trigger**

Add the import (after the existing `use function` block):

```php
use function array_merge;
use function preg_match;
use function preg_replace;
use function sprintf;
use function trigger_deprecation;
```

```php
/**
 * StateButtonCallback creates the state toggle button known in Contao.
 *
 * @deprecated Use the native "toggle" field eval instead
 *             (Contao\CoreBundle\DataContainer\DataContainerOperationsBuilder::handleToggle()).
 *             Will be removed in 5.0.
 */
final class StateButtonCallbackListener
{
    // ... unchanged properties ...

    public function __construct(
        Adapter $backend,
        Adapter $input,
        Updater $updater,
        DcaManager $dcaManager,
        private SystemLogger|null $logger = null,
    ) {
        $this->input      = $input;
        $this->updater    = $updater;
        $this->dcaManager = $dcaManager;
        $this->backend    = $backend;

        trigger_deprecation(
            'netzmacht/contao-toolkit',
            '4.1',
            'StateButtonCallbackListener is deprecated. Use the native "toggle" field eval instead.',
        );
    }
```

(the rest of the class — `onButtonCallback()`, `disableIcon()`, `getConfig()`, `getOperationName()` — is unchanged)

- [ ] **Step 2: Write the spec (none existed before)**

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Listener\Button;

use Contao\Backend;
use Contao\CoreBundle\Framework\Adapter;
use Contao\Input;
use Netzmacht\Contao\Toolkit\Data\Updater\Updater;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Listener\Button\StateButtonCallbackListener;
use PhpSpec\ObjectBehavior;
use spec\Netzmacht\Contao\Toolkit\DeprecationSpecHelper;

class StateButtonCallbackListenerSpec extends ObjectBehavior
{
    use DeprecationSpecHelper;

    /**
     * @param Adapter<Backend> $backend
     * @param Adapter<Input>   $input
     */
    public function let(Adapter $backend, Adapter $input, Updater $updater, DcaManager $dcaManager): void
    {
        $this->beConstructedWith($backend, $input, $updater, $dcaManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(StateButtonCallbackListener::class);
    }

    public function it_triggers_a_deprecation_warning_when_instantiated(): void
    {
        $messages = $this->captureDeprecations(function (): void {
            $this->getWrappedObject();
        });

        $this->assertDeprecationTriggered($messages, 'StateButtonCallbackListener is deprecated');
    }
}
```

- [ ] **Step 3: Run the new spec**

Run: `vendor/bin/phpspec run spec/Dca/Listener/Button/StateButtonCallbackListenerSpec.php`
Expected: all examples green.

- [ ] **Step 4: Commit**

```bash
git add src/Dca/Listener/Button/StateButtonCallbackListener.php spec/Dca/Listener/Button/StateButtonCallbackListenerSpec.php
git commit -m "Deprecate StateButtonCallbackListener in favor of the native toggle field eval"
```

---

## Task C.3: Deprecate `ColorPickerListener` (own constructor)

**Files:**
- Modify: `src/Dca/Listener/Wizard/ColorPickerListener.php`
- Test: Create `spec/Dca/Listener/Wizard/ColorPickerListenerSpec.php` (none exists today)

**Interfaces:**
- Consumes: `DeprecationSpecHelper` (Task A.2). Unlike `FilePickerListener`/`PagePickerListener`, `ColorPickerListener` has no constructor of its own today (it inherits `AbstractWizardListener::__construct()`) — this task gives it one so the deprecation trigger fires only for `ColorPickerListener`, not for every `AbstractWizardListener` subclass.
- Produces: `ColorPickerListener::__construct(TemplateRenderer, Translator, DcaManager)` (same three parameters `AbstractWizardListener` already accepts, `$template` argument dropped since Task A.4 already fixed the default and no caller passes a custom one).

- [ ] **Step 1: Add an own constructor with the runtime trigger**

```php
<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Wizard;

use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Override;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

use function array_merge;
use function trigger_deprecation;

/**
 * @deprecated Use the native `eval => ['colorpicker' => true]` field eval instead. Will be removed in 5.0.
 */
final class ColorPickerListener extends AbstractPickerListener
{
    /**
     * Template name.
     */
    protected string $template = '@NetzmachtContaoToolkit/backend/wizard_color_picker.html.twig';

    public function __construct(TemplateRenderer $templateRenderer, Translator $translator, DcaManager $dcaManager)
    {
        parent::__construct($templateRenderer, $translator, $dcaManager);

        trigger_deprecation(
            'netzmacht/contao-toolkit',
            '4.1',
            'ColorPickerListener is deprecated. Use the native "colorpicker" field eval instead.',
        );
    }

    /**
     * Generate the color picker.
     *
     * @param string $dataContainerName Data container name.
     * @param string $fieldName         Field name.
     */
    public function generate(string $dataContainerName, string $fieldName): string
    {
        $config          = $this->getConfig($dataContainerName, $fieldName);
        $config['field'] = $fieldName;

        return $this->render($this->template, $config);
    }

    /** {@inheritDoc} */
    #[Override]
    public function onWizardCallback(DataContainer $dataContainer): string
    {
        return $this->generate($dataContainer->table, $dataContainer->field);
    }

    /**
     * Get the picker configuration.
     *
     * @param string $dataContainerName Data container name.
     * @param string $fieldName         Field name.
     *
     * @return array<string,mixed>
     */
    public function getConfig(string $dataContainerName, string $fieldName): array
    {
        $definition = $this->dcaManager->getDefinition($dataContainerName);
        $config     = [
            'title'      => $this->translator->trans('MSC.colorpicker', [], 'contao_default'),
            'template'   => $this->template,
            'icon'       => 'pickcolor.svg',
            'replaceHex' => false,
        ];

        return array_merge(
            $config,
            (array) $definition->get(['fields', $fieldName, 'toolkit', 'color_picker']),
        );
    }
}
```

- [ ] **Step 2: Write the spec (none existed before)**

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Listener\Wizard;

use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\ColorPickerListener;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Netzmacht\Contao\Toolkit\DeprecationSpecHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class ColorPickerListenerSpec extends ObjectBehavior
{
    use DeprecationSpecHelper;

    public function let(TemplateRenderer $templateRenderer, TranslatorInterface $translator, DcaManager $dcaManager): void
    {
        $this->beConstructedWith($templateRenderer, $translator, $dcaManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ColorPickerListener::class);
    }

    public function it_triggers_a_deprecation_warning_when_instantiated(): void
    {
        $messages = $this->captureDeprecations(function (): void {
            $this->getWrappedObject();
        });

        $this->assertDeprecationTriggered($messages, 'ColorPickerListener is deprecated');
    }

    public function it_still_renders_with_the_new_twig_default_template(
        TemplateRenderer $templateRenderer,
        TranslatorInterface $translator,
        DcaManager $dcaManager,
    ): void {
        $dca = [];

        $translator->trans('MSC.colorpicker', [], 'contao_default')->willReturn('Pick a color');
        $dcaManager->getDefinition('tl_example')->willReturn(new Definition('tl_example', $dca));

        $templateRenderer
            ->render('@NetzmachtContaoToolkit/backend/wizard_color_picker.html.twig', Argument::type('array'))
            ->willReturn('<svg></svg>');

        $this->generate('tl_example', 'color')->shouldReturn('<svg></svg>');
    }
}
```

- [ ] **Step 3: Run the new spec**

Run: `vendor/bin/phpspec run spec/Dca/Listener/Wizard/ColorPickerListenerSpec.php`
Expected: all examples green.

- [ ] **Step 4: Commit**

```bash
git add src/Dca/Listener/Wizard/ColorPickerListener.php spec/Dca/Listener/Wizard/ColorPickerListenerSpec.php
git commit -m "Deprecate ColorPickerListener in favor of the native colorpicker field eval"
```

---

## Task C.4: Deprecate `FilePickerListener` and `PagePickerListener`

**Files:**
- Modify: `src/Dca/Listener/Wizard/FilePickerListener.php:38-49`
- Modify: `src/Dca/Listener/Wizard/PagePickerListener.php:36-47`
- Test: Create `spec/Dca/Listener/Wizard/FilePickerListenerSpec.php`, `spec/Dca/Listener/Wizard/PagePickerListenerSpec.php` (neither exists today)

**Interfaces:**
- Consumes: `DeprecationSpecHelper` (Task A.2).
- Produces: nothing new — both classes already have their own constructor, only a trigger call is added to its body.

- [ ] **Step 1: `FilePickerListener`**

Add the import and class docblock:

```php
use function sprintf;
use function str_replace;
use function trigger_deprecation;

/**
 * FilePicker wizard.
 *
 * @deprecated Use the native `eval => ['dcaPicker' => [...]]` field eval
 *             (Contao\Backend::getDcaPickerWizard()) instead. Will be removed in 5.0.
 */
final class FilePickerListener extends AbstractFieldPickerListener
```

Add the trigger call at the end of the constructor body:

```php
    public function __construct(
        TemplateRenderer $templateRenderer,
        Translator $translator,
        DcaManager $dcaManager,
        Adapter $input,
        private RouterInterface $router,
        string $template = '',
    ) {
        parent::__construct($templateRenderer, $translator, $dcaManager, $template);

        $this->input = $input;

        trigger_deprecation(
            'netzmacht/contao-toolkit',
            '4.1',
            'FilePickerListener is deprecated. Use the native "dcaPicker" field eval instead.',
        );
    }
```

- [ ] **Step 2: `PagePickerListener`**

Same pattern:

```php
use function sprintf;
use function str_replace;
use function trigger_deprecation;

/**
 * @deprecated Use the native `eval => ['dcaPicker' => [...]]` field eval
 *             (Contao\Backend::getDcaPickerWizard()) instead. Will be removed in 5.0.
 */
final class PagePickerListener extends AbstractFieldPickerListener
```

```php
    public function __construct(
        TemplateRenderer $templateRenderer,
        Translator $translator,
        DcaManager $dcaManager,
        Adapter $input,
        private RouterInterface $router,
        string $template = '',
    ) {
        parent::__construct($templateRenderer, $translator, $dcaManager, $template);

        $this->input = $input;

        trigger_deprecation(
            'netzmacht/contao-toolkit',
            '4.1',
            'PagePickerListener is deprecated. Use the native "dcaPicker" field eval instead.',
        );
    }
```

- [ ] **Step 3: Write the `FilePickerListener` spec**

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Listener\Wizard;

use Contao\CoreBundle\Framework\Adapter;
use Contao\Input;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\FilePickerListener;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use PhpSpec\ObjectBehavior;
use spec\Netzmacht\Contao\Toolkit\DeprecationSpecHelper;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FilePickerListenerSpec extends ObjectBehavior
{
    use DeprecationSpecHelper;

    /** @param Adapter<Input> $input */
    public function let(
        TemplateRenderer $templateRenderer,
        TranslatorInterface $translator,
        DcaManager $dcaManager,
        Adapter $input,
        RouterInterface $router,
    ): void {
        $this->beConstructedWith($templateRenderer, $translator, $dcaManager, $input, $router);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(FilePickerListener::class);
    }

    public function it_triggers_a_deprecation_warning_when_instantiated(): void
    {
        $messages = $this->captureDeprecations(function (): void {
            $this->getWrappedObject();
        });

        $this->assertDeprecationTriggered($messages, 'FilePickerListener is deprecated');
    }
}
```

- [ ] **Step 4: Write the `PagePickerListener` spec**

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Listener\Wizard;

use Contao\CoreBundle\Framework\Adapter;
use Contao\Input;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\PagePickerListener;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use PhpSpec\ObjectBehavior;
use spec\Netzmacht\Contao\Toolkit\DeprecationSpecHelper;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PagePickerListenerSpec extends ObjectBehavior
{
    use DeprecationSpecHelper;

    /** @param Adapter<Input> $input */
    public function let(
        TemplateRenderer $templateRenderer,
        TranslatorInterface $translator,
        DcaManager $dcaManager,
        Adapter $input,
        RouterInterface $router,
    ): void {
        $this->beConstructedWith($templateRenderer, $translator, $dcaManager, $input, $router);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(PagePickerListener::class);
    }

    public function it_triggers_a_deprecation_warning_when_instantiated(): void
    {
        $messages = $this->captureDeprecations(function (): void {
            $this->getWrappedObject();
        });

        $this->assertDeprecationTriggered($messages, 'PagePickerListener is deprecated');
    }
}
```

- [ ] **Step 5: Run the new specs**

Run: `vendor/bin/phpspec run spec/Dca/Listener/Wizard/FilePickerListenerSpec.php spec/Dca/Listener/Wizard/PagePickerListenerSpec.php`
Expected: all examples green.

- [ ] **Step 6: Commit**

```bash
git add src/Dca/Listener/Wizard/FilePickerListener.php src/Dca/Listener/Wizard/PagePickerListener.php spec/Dca/Listener/Wizard/FilePickerListenerSpec.php spec/Dca/Listener/Wizard/PagePickerListenerSpec.php
git commit -m "Deprecate FilePickerListener and PagePickerListener in favor of the native dcaPicker field eval"
```

---

## Task C.5: Extend `TemplateOptionsListener` with modern fragment-template support

**Files:**
- Modify: `src/Dca/Listener/Options/TemplateOptionsListener.php`
- Test: Create `spec/Dca/Listener/Options/TemplateOptionsListenerSpec.php` (none exists today)

**Interfaces:**
- Consumes: `Contao\CoreBundle\Twig\Finder\FinderFactory::create(): Finder`, `Finder::identifier(string): Finder`, `Finder::withVariants(): Finder`, `Finder::asIdentifierList(): list<string>` (all native Contao 5.7+ API, no version guard needed).
- Produces: new constructor parameter `FinderFactory $finderFactory` — this is a signature change, but `TemplateOptionsListener` is `final` and DI-only (no documented direct instantiation by consumers), so it ships without a deprecation cycle (same precedent as every other pure-service-class dependency addition in this plan).

- [ ] **Step 1: Add the `FinderFactory` dependency and branch on the prefix style**

```php
<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Options;

use Contao\Controller;
use Contao\CoreBundle\Twig\Finder\FinderFactory;
use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;

use function array_diff;
use function array_map;
use function array_merge;
use function array_values;
use function str_contains;

final class TemplateOptionsListener
{
    public function __construct(
        private readonly DcaManager $dcaManager,
        private readonly FinderFactory $finderFactory,
    ) {
    }

    /**
     * Handle the option callback.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return list<string>
     *
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
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

    /**
     * Get the callback config.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return array<string,mixed>
     */
    private function getConfig(DataContainer $dataContainer): array
    {
        $definition = $this->dcaManager->getDefinition($dataContainer->table);

        return array_merge(
            [
                'prefix' => '',
                'exclude' => null,
            ],
            (array) $definition->get(['fields', $dataContainer->field, 'toolkit', 'template_options']),
        );
    }
}
```

(the constructor drops the old manual property assignment in favor of promoted `readonly` properties, consistent with newer classes already added in this codebase, e.g. `DatabaseRowUpdater`)

- [ ] **Step 2: Update `src/Resources/config/listeners.yml` for the new dependency**

```yaml
  Netzmacht\Contao\Toolkit\Dca\Listener\Options\TemplateOptionsListener:
    public: true
    arguments:
      - '@netzmacht.contao_toolkit.dca.manager'
      - '@Contao\CoreBundle\Twig\Finder\FinderFactory'
```

- [ ] **Step 3: Create a shared `DataContainer` test fixture (reused by this task and by C.6/C.7)**

`Contao\DataContainer` is abstract, and its magic `__set()` has no case for `table` (only `id`/`field`/`inputName`/`createNewVersion`/`activeRecord` — a plain `$dataContainer->table = 'x'` would silently create an unrelated dynamic property instead of setting the internal `$strTable` the class actually reads from). Every spec in this plan that needs a real `DataContainer` therefore goes through reflection instead of magic-property assignment.

`spec/ConcreteDataContainer.php` (namespace `spec\Netzmacht\Contao\Toolkit`, alongside `DeprecationSpecHelper`):

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit;

use Contao\DataContainer;
use Override;

final class ConcreteDataContainer extends DataContainer
{
    #[Override]
    public function getPalette()
    {
        return '';
    }

    #[Override]
    protected function save($varValue)
    {
        return $varValue;
    }
}
```

`spec/DataContainerSpecHelper.php`:

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit;

use Contao\DataContainer;
use ReflectionClass;
use ReflectionProperty;

trait DataContainerSpecHelper
{
    private function createDataContainer(
        string $table,
        string $field = '',
        mixed $value = null,
        object|null $activeRecord = null,
    ): DataContainer {
        $dataContainer = (new ReflectionClass(ConcreteDataContainer::class))->newInstanceWithoutConstructor();

        $this->setDataContainerProperty($dataContainer, 'strTable', $table);
        $this->setDataContainerProperty($dataContainer, 'strField', $field);
        $this->setDataContainerProperty($dataContainer, 'varValue', $value);
        $this->setDataContainerProperty($dataContainer, 'objActiveRecord', $activeRecord);

        return $dataContainer;
    }

    private function setDataContainerProperty(DataContainer $dataContainer, string $property, mixed $value): void
    {
        $reflectionProperty = new ReflectionProperty(DataContainer::class, $property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($dataContainer, $value);
    }
}
```

- [ ] **Step 4: Write the spec (none existed before)**

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Listener\Options;

use Contao\CoreBundle\Twig\Finder\Finder;
use Contao\CoreBundle\Twig\Finder\FinderFactory;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Listener\Options\TemplateOptionsListener;
use PhpSpec\ObjectBehavior;
use spec\Netzmacht\Contao\Toolkit\DataContainerSpecHelper;

class TemplateOptionsListenerSpec extends ObjectBehavior
{
    use DataContainerSpecHelper;

    public function let(DcaManager $dcaManager, FinderFactory $finderFactory): void
    {
        $this->beConstructedWith($dcaManager, $finderFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TemplateOptionsListener::class);
    }

    public function it_uses_get_template_group_for_a_classic_prefix(DcaManager $dcaManager): void
    {
        $dca = ['fields' => ['template' => ['toolkit' => ['template_options' => ['prefix' => 'ce_']]]]];

        $dcaManager->getDefinition('tl_content')->willReturn(new Definition('tl_content', $dca));

        $dataContainer = $this->createDataContainer('tl_content', 'template');

        // Controller::getTemplateGroup() hits the real (empty, in the test env) template
        // filesystem — the assertion only checks the type, not concrete found templates.
        $this->onOptionsCallback($dataContainer)->shouldHaveType('array');
    }

    public function it_uses_the_finder_for_a_modern_namespaced_prefix(
        DcaManager $dcaManager,
        FinderFactory $finderFactory,
        Finder $finder,
    ): void {
        $dca = ['fields' => ['template' => ['toolkit' => ['template_options' => ['prefix' => 'content_element/text']]]]];

        $dcaManager->getDefinition('tl_content')->willReturn(new Definition('tl_content', $dca));

        $finderFactory->create()->willReturn($finder);
        $finder->identifier('content_element/text')->willReturn($finder);
        $finder->withVariants()->willReturn($finder);
        $finder->asIdentifierList()->willReturn(['content_element/text', 'content_element/text/custom1']);

        $dataContainer = $this->createDataContainer('tl_content', 'template');

        $this->onOptionsCallback($dataContainer)->shouldReturn([
            'content_element/text',
            'content_element/text/custom1',
        ]);
    }

    public function it_still_applies_the_exclude_list_for_the_finder_path(
        DcaManager $dcaManager,
        FinderFactory $finderFactory,
        Finder $finder,
    ): void {
        $dca = [
            'fields' => [
                'template' => [
                    'toolkit' => [
                        'template_options' => [
                            'prefix'  => 'content_element/text',
                            'exclude' => ['content_element/text/custom1'],
                        ],
                    ],
                ],
            ],
        ];

        $dcaManager->getDefinition('tl_content')->willReturn(new Definition('tl_content', $dca));

        $finderFactory->create()->willReturn($finder);
        $finder->identifier('content_element/text')->willReturn($finder);
        $finder->withVariants()->willReturn($finder);
        $finder->asIdentifierList()->willReturn(['content_element/text', 'content_element/text/custom1']);

        $dataContainer = $this->createDataContainer('tl_content', 'template');

        $this->onOptionsCallback($dataContainer)->shouldReturn(['content_element/text']);
    }
}
```

- [ ] **Step 5: Run the new spec**

Run: `vendor/bin/phpspec run spec/Dca/Listener/Options/TemplateOptionsListenerSpec.php`
Expected: all examples green (the `Controller::getTemplateGroup()` example only asserts the return type since it hits Contao's real static template lookup in the test environment — narrowing this further would require faking Contao's template filesystem, out of scope for this extension).

- [ ] **Step 6: Commit**

```bash
git add src/Dca/Listener/Options/TemplateOptionsListener.php src/Resources/config/listeners.yml spec/Dca/Listener/Options/TemplateOptionsListenerSpec.php spec/ConcreteDataContainer.php spec/DataContainerSpecHelper.php
git commit -m "Extend TemplateOptionsListener to support modern fragment-template identifiers"
```

---

## Task C.6: New `SlugAliasGenerator` + `SlugAliasListener` on top of Contao's `Slug` service

**Files:**
- Create: `src/Data/Alias/SlugAliasGenerator.php`
- Create: `src/Dca/Listener/Save/SlugAliasListener.php`
- Modify: `src/Resources/config/listeners.yml`
- Test: Create `spec/Data/Alias/SlugAliasGeneratorSpec.php`, `spec/Dca/Listener/Save/SlugAliasListenerSpec.php`

**Interfaces:**
- Consumes: `Contao\CoreBundle\Slug\Slug::generate(string, int|iterable, ?callable, string): string` (autowireable, `@contao.slug`), `Netzmacht\Contao\Toolkit\Data\Alias\Validator::validate(object, mixed, ?array): bool` (existing interface, unchanged), `Netzmacht\Contao\Toolkit\Data\Alias\Validator\UniqueDatabaseValueValidator` (existing, unchanged).
- Produces: `SlugAliasGenerator implements AliasGenerator` with `generate(object $result, mixed $value = null): string` (covariant narrowing of the interface's `string|null`); `SlugAliasListener::onSaveCallback(mixed $value, DataContainer $dataContainer): string` — the new `save_callback` entry point, reading `fields`/`unique_key_fields`/`allow_empty` from `fields.<field>.toolkit.alias_generator` (same config path `GenerateAliasListener` already reads today, `factory` key no longer applicable).

- [ ] **Step 1: Create `Data\Alias\SlugAliasGenerator`**

```php
<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias;

use Contao\CoreBundle\Slug\Slug;
use Netzmacht\Contao\Toolkit\Data\Alias\Exception\InvalidAliasException;
use Override;

use function array_filter;
use function implode;

final class SlugAliasGenerator implements AliasGenerator
{
    /** @param list<string> $fields */
    public function __construct(
        private readonly Slug $slug,
        private readonly Validator $validator,
        private readonly string $tableName,
        private readonly array $fields = ['id'],
        private readonly string $separator = '-',
    ) {
    }

    #[Override]
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

- [ ] **Step 2: Create `Dca\Listener\Save\SlugAliasListener`**

```php
<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Save;

use Contao\CoreBundle\Slug\Slug;
use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Toolkit\Data\Alias\SlugAliasGenerator;
use Netzmacht\Contao\Toolkit\Data\Alias\Validator\UniqueDatabaseValueValidator;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;

use function array_merge;

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

    private function getGenerator(DataContainer $dataContainer): SlugAliasGenerator
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

    /** @return array{fields: list<string>, unique_key_fields: list<string>, allow_empty: bool} */
    private function getConfig(DataContainer $dataContainer): array
    {
        $definition = $this->dcaManager->getDefinition($dataContainer->table);

        return array_merge(
            [
                'fields' => ['id'],
                'unique_key_fields' => [],
                'allow_empty' => false,
            ],
            (array) $definition->get(['fields', $dataContainer->field, 'toolkit', 'alias_generator']),
        );
    }
}
```

- [ ] **Step 3: Register the new listener service**

In `src/Resources/config/listeners.yml`, add (after the existing `GenerateAliasListener` block):

```yaml
  Netzmacht\Contao\Toolkit\Dca\Listener\Save\SlugAliasListener:
    public: true
    arguments:
      - '@contao.slug'
      - '@database_connection'
      - '@netzmacht.contao_toolkit.dca.manager'

  netzmacht.contao_toolkit.dca.listeners.slug_alias_generator:
    alias: Netzmacht\Contao\Toolkit\Dca\Listener\Save\SlugAliasListener
    public: true
```

- [ ] **Step 4: Write the `SlugAliasGenerator` spec**

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Data\Alias;

use Contao\CoreBundle\Slug\Slug;
use Netzmacht\Contao\Toolkit\Data\Alias\Exception\InvalidAliasException;
use Netzmacht\Contao\Toolkit\Data\Alias\SlugAliasGenerator;
use Netzmacht\Contao\Toolkit\Data\Alias\Validator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use stdClass;

class SlugAliasGeneratorSpec extends ObjectBehavior
{
    public function let(Slug $slug, Validator $validator): void
    {
        $this->beConstructedWith($slug, $validator, 'tl_example', ['title']);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SlugAliasGenerator::class);
    }

    public function it_generates_a_slug_from_the_configured_fields_when_value_is_empty(
        Slug $slug,
        Validator $validator,
    ): void {
        $result        = new stdClass();
        $result->id    = 1;
        $result->title = 'Hello World';

        $slug
            ->generate('Hello World', ['delimiter' => '-'], Argument::type('callable'))
            ->willReturn('hello-world');

        $validator->validate($result, 'hello-world', [1])->willReturn(true);

        $this->generate($result, null)->shouldReturn('hello-world');
    }

    public function it_keeps_a_manually_set_unique_value(Slug $slug, Validator $validator): void
    {
        $result     = new stdClass();
        $result->id = 1;

        $validator->validate($result, 'custom-alias', [1])->willReturn(true);
        $slug->generate(Argument::cetera())->shouldNotBeCalled();

        $this->generate($result, 'custom-alias')->shouldReturn('custom-alias');
    }

    public function it_throws_when_a_manually_set_value_is_not_unique(Validator $validator): void
    {
        $result     = new stdClass();
        $result->id = 1;

        $validator->validate($result, 'duplicate', [1])->willReturn(false);

        $this->shouldThrow(InvalidAliasException::class)->during('generate', [$result, 'duplicate']);
    }

    public function it_throws_when_the_generated_value_is_still_not_unique(Slug $slug, Validator $validator): void
    {
        $result        = new stdClass();
        $result->id    = 1;
        $result->title = 'Hello World';

        $slug
            ->generate('Hello World', ['delimiter' => '-'], Argument::type('callable'))
            ->willReturn('hello-world');

        $validator->validate($result, 'hello-world', [1])->willReturn(false);

        $this->shouldThrow(InvalidAliasException::class)->during('generate', [$result, null]);
    }
}
```

- [ ] **Step 5: Write the `SlugAliasListener` spec**

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Listener\Save;

use Contao\CoreBundle\Slug\Slug;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Listener\Save\SlugAliasListener;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Netzmacht\Contao\Toolkit\DataContainerSpecHelper;
use stdClass;

class SlugAliasListenerSpec extends ObjectBehavior
{
    use DataContainerSpecHelper;

    public function let(Slug $slug, Connection $connection, DcaManager $dcaManager): void
    {
        $this->beConstructedWith($slug, $connection, $dcaManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SlugAliasListener::class);
    }

    public function it_generates_an_alias_using_the_configured_fields(
        Slug $slug,
        Connection $connection,
        DcaManager $dcaManager,
        QueryBuilder $queryBuilder,
    ): void {
        $dca = [
            'fields' => [
                'alias' => ['toolkit' => ['alias_generator' => ['fields' => ['title']]]],
            ],
        ];

        $dcaManager->getDefinition('tl_example')->willReturn(new Definition('tl_example', $dca));

        $activeRecord        = new stdClass();
        $activeRecord->id    = 1;
        $activeRecord->title = 'Hello World';

        $dataContainer = $this->createDataContainer('tl_example', 'alias', null, $activeRecord);

        // UniqueDatabaseValueValidator::validate() builds: select(...)->from($table)->where($col.'= :value')
        // ->setParameter('value', $value)->andWhere('id NOT IN(:excluded)')->setParameter('excluded', [1], ...)
        // and reads the result via the QueryBuilder's own fetchOne() (not ->execute()).
        $connection->createQueryBuilder()->willReturn($queryBuilder);
        $queryBuilder->select(Argument::type('string'))->willReturn($queryBuilder);
        $queryBuilder->from('tl_example')->willReturn($queryBuilder);
        $queryBuilder->where('alias= :value')->willReturn($queryBuilder);
        $queryBuilder->andWhere('id NOT IN(:excluded)')->willReturn($queryBuilder);
        $queryBuilder->setParameter(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->fetchOne()->willReturn(0);

        $slug
            ->generate('Hello World', ['delimiter' => '-'], Argument::type('callable'))
            ->willReturn('hello-world');

        $this->onSaveCallback(null, $dataContainer)->shouldReturn('hello-world');
    }
}
```

- [ ] **Step 6: Run the new specs**

Run: `vendor/bin/phpspec run spec/Data/Alias/SlugAliasGeneratorSpec.php spec/Dca/Listener/Save/SlugAliasListenerSpec.php`
Expected: all examples green.

- [ ] **Step 7: Commit**

```bash
git add src/Data/Alias/SlugAliasGenerator.php src/Dca/Listener/Save/SlugAliasListener.php src/Resources/config/listeners.yml spec/Data/Alias/SlugAliasGeneratorSpec.php spec/Dca/Listener/Save/SlugAliasListenerSpec.php
git commit -m "Add SlugAliasGenerator/SlugAliasListener on top of Contao's native Slug service"
```

---

## Task C.7: Deprecate `GenerateAliasListener` and the old filter/factory chain

**Files:**
- Modify: `src/Dca/Listener/Save/GenerateAliasListener.php`
- Modify: `src/Data/Alias/FilterBasedAliasGenerator.php`, `src/Data/Alias/Filter.php`, `src/Data/Alias/Filter/AbstractFilter.php`, `src/Data/Alias/Filter/AbstractValueFilter.php`, `src/Data/Alias/Filter/SlugifyFilter.php`, `src/Data/Alias/Filter/SuffixFilter.php`, `src/Data/Alias/Filter/ExistingAliasFilter.php`, `src/Data/Alias/Filter/RawValueFilter.php`, `src/Data/Alias/Factory/AliasGeneratorFactory.php`, `src/Data/Alias/Factory/ToolkitAliasGeneratorFactory.php` (doc-only, no separate trigger — per Spec 7, the warning already fires via `GenerateAliasListener`'s constructor, the sole documented entry point)
- Test: Create `spec/Dca/Listener/Save/GenerateAliasListenerSpec.php` (none exists today)

**Interfaces:**
- Consumes: `DeprecationSpecHelper` (Task A.2).
- Produces: nothing new — `GenerateAliasListener::onSaveCallback()` behavior is unchanged, only a constructor-time warning is added.

- [ ] **Step 1: Add the runtime trigger to `GenerateAliasListener`'s constructor**

Add the import:

```php
use function array_values;
use function assert;
use function sprintf;
use function trigger_deprecation;
```

```php
/**
 * Class GenerateAliasCallback is designed to create an alias of a column.
 *
 * @deprecated Use Netzmacht\Contao\Toolkit\Dca\Listener\Save\SlugAliasListener instead. Will be removed in 5.0.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
final class GenerateAliasListener
{
    // ... unchanged properties ...

    public function __construct(Container $container, DcaManager $dcaManager, string $defaultFactoryServiceId)
    {
        $this->container               = $container;
        $this->defaultFactoryServiceId = $defaultFactoryServiceId;
        $this->dcaManager              = $dcaManager;

        trigger_deprecation(
            'netzmacht/contao-toolkit',
            '4.1',
            'GenerateAliasListener is deprecated. Use Netzmacht\Contao\Toolkit\Dca\Listener\Save\SlugAliasListener instead.',
        );
    }
```

(the rest of the class — `onSaveCallback()`, `getFactoryServiceId()`, `guardIsAliasGeneratorFactory()`, `getGenerator()` — is unchanged)

- [ ] **Step 2: Doc-deprecate the old filter/factory chain (no separate trigger on any of these)**

`src/Data/Alias/FilterBasedAliasGenerator.php`:

```php
/**
 * Alias generator.
 *
 * @deprecated Use Netzmacht\Contao\Toolkit\Data\Alias\SlugAliasGenerator instead. Will be removed in 5.0.
 */
final class FilterBasedAliasGenerator implements AliasGenerator
```

`src/Data/Alias/Filter.php`:

```php
/**
 * Filter modifies a value for the alias generator.
 *
 * @deprecated Part of the deprecated filter-based alias generator. Will be removed in 5.0.
 */
interface Filter
```

`src/Data/Alias/Filter/AbstractFilter.php`:

```php
/**
 * Base filter class.
 *
 * @deprecated Part of the deprecated filter-based alias generator. Will be removed in 5.0.
 */
abstract class AbstractFilter implements Filter
```

`src/Data/Alias/Filter/AbstractValueFilter.php`:

```php
/**
 * Base class for filters depending on values from other columns.
 *
 * @deprecated Part of the deprecated filter-based alias generator. Will be removed in 5.0.
 */
abstract class AbstractValueFilter extends AbstractFilter
```

`src/Data/Alias/Filter/SlugifyFilter.php`:

```php
/**
 * SlugifyFilter creates a slug value of the columns being represented.
 *
 * @deprecated Part of the deprecated filter-based alias generator. Will be removed in 5.0.
 */
final class SlugifyFilter extends AbstractValueFilter
```

`src/Data/Alias/Filter/SuffixFilter.php`:

```php
/**
 * SuffixFilter adds a numeric suffix until a unique value is given.
 *
 * @deprecated Part of the deprecated filter-based alias generator. Will be removed in 5.0.
 */
final class SuffixFilter extends AbstractFilter
```

`src/Data/Alias/Filter/ExistingAliasFilter.php`:

```php
/**
 * Class ExistingAliasFilter uses the existing value.
 *
 * @deprecated Part of the deprecated filter-based alias generator. Will be removed in 5.0.
 */
final class ExistingAliasFilter implements Filter
```

`src/Data/Alias/Filter/RawValueFilter.php`:

```php
/**
 * RawValueFilter uses the values as given.
 *
 * @deprecated Part of the deprecated filter-based alias generator. Will be removed in 5.0.
 */
final class RawValueFilter extends AbstractValueFilter
```

`src/Data/Alias/Factory/AliasGeneratorFactory.php`:

```php
<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Factory;

use Netzmacht\Contao\Toolkit\Data\Alias\AliasGenerator;

/**
 * @deprecated Implement Netzmacht\Contao\Toolkit\Data\Alias\AliasGenerator directly instead.
 *             Will be removed in 5.0.
 */
interface AliasGeneratorFactory
```

`src/Data/Alias/Factory/ToolkitAliasGeneratorFactory.php`:

```php
/**
 * @deprecated Use Netzmacht\Contao\Toolkit\Data\Alias\SlugAliasGenerator instead. Will be removed in 5.0.
 */
final class ToolkitAliasGeneratorFactory implements AliasGeneratorFactory
```

- [ ] **Step 3: Write the `GenerateAliasListener` spec (none existed before)**

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Listener\Save;

use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Listener\Save\GenerateAliasListener;
use PhpSpec\ObjectBehavior;
use spec\Netzmacht\Contao\Toolkit\DeprecationSpecHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GenerateAliasListenerSpec extends ObjectBehavior
{
    use DeprecationSpecHelper;

    public function let(ContainerInterface $container, DcaManager $dcaManager): void
    {
        $this->beConstructedWith($container, $dcaManager, 'netzmacht.contao_toolkit.data.alias_generator.factory.default_factory');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(GenerateAliasListener::class);
    }

    public function it_triggers_a_deprecation_warning_when_instantiated(): void
    {
        $messages = $this->captureDeprecations(function (): void {
            $this->getWrappedObject();
        });

        $this->assertDeprecationTriggered($messages, 'GenerateAliasListener is deprecated');
    }
}
```

- [ ] **Step 4: Run the new spec, and the existing filter-chain specs to confirm no regression**

Run: `vendor/bin/phpspec run spec/Dca/Listener/Save/GenerateAliasListenerSpec.php spec/Data/Alias`
Expected: all examples green (the filter/factory specs are unaffected — docblock-only changes).

- [ ] **Step 5: Commit**

```bash
git add src/Dca/Listener/Save/GenerateAliasListener.php src/Data/Alias/FilterBasedAliasGenerator.php src/Data/Alias/Filter.php src/Data/Alias/Filter src/Data/Alias/Factory spec/Dca/Listener/Save/GenerateAliasListenerSpec.php
git commit -m "Deprecate GenerateAliasListener and the filter/factory alias-generation chain"
```

---

## Task C.8: Docs and CHANGELOG for cluster C (points 5+6+7)

**Files:**
- Modify: `docs/dca/callbacks.rst`
- Modify: `docs/data/alias.rst`
- Modify: `docs/data/updater.rst`
- Modify: `CHANGELOG.md`

**Interfaces:** none (documentation only).

- [ ] **Step 1: Update the alias-generator section of `docs/dca/callbacks.rst`**

Replace the existing "Alias generator callback" section:

```rst
.. _callbacks-alias:

Alias generator callback
~~~~~~~~~~~~~~~~~~~~~~~~

.. important::

   `GenerateAliasListener` (and the filter-/factory-based alias generator it drives) is deprecated
   as of 4.1.0 and will be removed in 5.0.0. Use `SlugAliasListener` below instead, which is
   built directly on Contao's native `contao.slug` service.

`SlugAliasListener` uses the :doc:`../data/alias` to create an alias callback based on Contao's own
`contao.slug` service. The `fields` configuration is required.

.. code-block:: php

   <?php

    $GLOBALS['TL_DCA']['tl_example']['fields']['alias']['save_callback'][] = [
        Netzmacht\Contao\Toolkit\Dca\Listener\Save\SlugAliasListener::class,
        'onSaveCallback'
    ];

    $GLOBALS['TL_DCA']['tl_example']['fields']['alias']['toolkit']['alias_generator'] = [
        'fields' => ['title'],
        'unique_key_fields' => [],
        'allow_empty' => false,
    ];

For more details please have a look at the `SlugAliasListener`_.
```

- [ ] **Step 2: Add a deprecation note to the "State button callback", "Color picker wizard", "File picker wizard" and "Page picker wizard" sections**

Directly after each of the four section headings (`State button callback`, `Color picker wizard`, `File picker wizard`, `Page picker wizard`), insert:

```rst
.. important::

   This listener is deprecated as of 4.1.0 and will be removed in 5.0.0.
```

(followed by that section's existing content unchanged) — the specific native replacement per listener (`toggle` field eval, `colorpicker` field eval, `dcaPicker` field eval) is already named in each source class's own `@deprecated` docblock.

- [ ] **Step 3: Document the `TemplateOptionsListener` extension**

In the "Get templates callback" section, after the existing code block, add:

```rst
As of 4.1.0, `prefix` also supports modern, namespaced fragment-template identifiers
(e.g. `content_element/text`) in addition to classic prefixes (`ce_`, `mod_`, …) — the listener
automatically uses Contao's `contao.twig.finder_factory` service for the former.
```

- [ ] **Step 4: Add the reference link and fix the update to the reference list**

At the bottom of `docs/dca/callbacks.rst`, add:

```rst
.. _SlugAliasListener: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Listener/Save/SlugAliasListener.php
```

- [ ] **Step 5: Update `docs/data/alias.rst`**

Insert directly after the title (before the "Filter based alias generator" heading):

```rst
.. important::

   The filter-based alias generator described below (`FilterBasedAliasGenerator`, the `Filter`
   chain, `AliasGeneratorFactory`/`ToolkitAliasGeneratorFactory`) is deprecated as of 4.1.0 and
   will be removed in 5.0.0. Use the `Slug-based generator`_ instead.

.. _Slug-based generator:

Slug-based generator (recommended)
-----------------------------------

`Netzmacht\\Contao\\Toolkit\\Data\\Alias\\SlugAliasGenerator` implements the same
`AliasGenerator`_ interface but delegates to Contao's own `contao.slug` service
(`Contao\\CoreBundle\\Slug\\Slug`, backed by `ausi/slug-generator`) instead of a custom filter
chain. It still uses the existing `Validator`_ (typically `UniqueDatabaseValueValidator`_) to
guard uniqueness — including for a manually entered, non-unique value, which now throws
`InvalidAliasException`_ instead of being silently overwritten (a deliberate behavior change
versus the deprecated filter-based generator).

Use it via the `SlugAliasListener` callback — see :doc:`../dca/callbacks`.

Filter based alias generator (deprecated)
-------------------------------------------
```

(this turns the old "Filter based alias generator" `====` title into a `---`-level subsection heading nested under the page, keeping its content otherwise unchanged)

- [ ] **Step 6: Note the `DatabaseRowUpdater` permission-check fix in `docs/data/updater.rst`**

At the end of the file, before the reference links, add:

```rst
.. note::

   As of 4.1.0, `DatabaseRowUpdater::hasUserAccess()` checks permissions via Symfony's
   `Security::isGranted(ContaoCorePermissions::USER_CAN_EDIT_FIELD_OF_TABLE, ...)` instead of the
   legacy `Contao\BackendUser::hasAccess()`. The observable true/false outcome for the standard
   `alexf` field-permission check is unchanged.
```

- [ ] **Step 7: Add the CHANGELOG entries**

In `CHANGELOG.md`, under the `[4.1.0]` section:

```markdown
### Added

 - New `Netzmacht\Contao\Toolkit\Data\Alias\SlugAliasGenerator` and
   `Dca\Listener\Save\SlugAliasListener`, built on Contao's native `contao.slug` service.

### Changed

 - `TemplateOptionsListener` now also supports modern, namespaced fragment-template identifiers
   (e.g. `content_element/text`), resolved via `contao.twig.finder_factory`.
 - `DatabaseRowUpdater::hasUserAccess()` now checks permissions via Symfony's
   `Security::isGranted(ContaoCorePermissions::USER_CAN_EDIT_FIELD_OF_TABLE, ...)` instead of the
   legacy `Contao\BackendUser::hasAccess()`.

### Deprecated

 - `Dca\Listener\Button\StateButtonCallbackListener`. Use the native `toggle` field eval instead.
 - `Dca\Listener\Wizard\ColorPickerListener`. Use the native `eval => ['colorpicker' => true]`
   field eval instead.
 - `Dca\Listener\Wizard\FilePickerListener`, `PagePickerListener`. Use the native
   `eval => ['dcaPicker' => [...]]` field eval instead.
 - `Dca\Listener\Save\GenerateAliasListener` and the filter-/factory-based alias generator
   (`FilterBasedAliasGenerator`, `Filter` and its implementations, `AliasGeneratorFactory`,
   `ToolkitAliasGeneratorFactory`). Use `Dca\Listener\Save\SlugAliasListener` instead.
```

- [ ] **Step 8: Commit**

```bash
git add docs/dca/callbacks.rst docs/data/alias.rst docs/data/updater.rst CHANGELOG.md
git commit -m "Add docs and CHANGELOG entries for the DCA-listener deprecation cluster"
```

---

## Task D.1: Deprecate `ContaoServicesFactory`'s Backend-/FrontendUser factory methods

**Files:**
- Modify: `src/DependencyInjection/ContaoServicesFactory.php:116-127`
- Modify: `src/Resources/config/services.yml:56-62`
- Modify: `spec/DependencyInjection/ContaoServicesFactorySpec.php`
- Create: `docs/dependency-injection/index.rst`, `docs/dependency-injection/contao-services-factory.rst` (no docs page exists for `ContaoServicesFactory` today — new page per the migration's documentation-gap inventory)
- Modify: `docs/index.rst` (toctree)
- Modify: `CHANGELOG.md`

**Interfaces:**
- Consumes: `DeprecationSpecHelper` (Task A.2).
- Produces: nothing new — `createBackendUserInstance()`/`createFrontendUserInstance()` keep returning the same singleton instances, only a call-time warning is added. All other `ContaoServicesFactory` methods (`createBackendAdapter()`, etc.) are untouched.

- [ ] **Step 1: Add the runtime triggers**

Add the import (the file currently only imports `use function assert;`):

```php
use function assert;
use function trigger_deprecation;
```

```php
    /**
     * Create backend user instance.
     *
     * @deprecated Use Symfony\Bundle\SecurityBundle\Security::isGranted() for permission checks,
     *             or ::getUser() for the concrete user object, instead. Will be removed in 5.0.
     */
    public function createBackendUserInstance(): BackendUser
    {
        trigger_deprecation(
            'netzmacht/contao-toolkit',
            '4.1',
            'ContaoServicesFactory::createBackendUserInstance() is deprecated. Use Symfony\Bundle\SecurityBundle\Security::isGranted() or ::getUser() instead.',
        );

        return $this->createInstance(BackendUser::class);
    }

    /**
     * Frontend user.
     *
     * @deprecated Use Symfony\Bundle\SecurityBundle\Security::isGranted() for permission checks,
     *             or ::getUser() for the concrete user object, instead. Will be removed in 5.0.
     */
    public function createFrontendUserInstance(): FrontendUser
    {
        trigger_deprecation(
            'netzmacht/contao-toolkit',
            '4.1',
            'ContaoServicesFactory::createFrontendUserInstance() is deprecated. Use Symfony\Bundle\SecurityBundle\Security::isGranted() or ::getUser() instead.',
        );

        return $this->createInstance(FrontendUser::class);
    }
```

- [ ] **Step 2: Mark the two service definitions as deprecated**

In `src/Resources/config/services.yml`:

```yaml
  # @deprecated Use Symfony\Bundle\SecurityBundle\Security::isGranted()/::getUser() instead. Will be removed in 5.0.
  netzmacht.contao_toolkit.contao.backend_user:
    class: Contao\BackendUser
    factory: ['@netzmacht.contao_toolkit.contao_services_factory', 'createBackendUserInstance']

  # @deprecated Use Symfony\Bundle\SecurityBundle\Security::isGranted()/::getUser() instead. Will be removed in 5.0.
  netzmacht.contao_toolkit.contao.frontend_user:
    class: Contao\FrontendUser
    factory: ['@netzmacht.contao_toolkit.contao_services_factory', 'createFrontendUserInstance']
```

- [ ] **Step 3: Add deprecation-warning examples to the existing spec**

In `spec/DependencyInjection/ContaoServicesFactorySpec.php`, add the trait use and two new examples (all existing examples stay unchanged):

```php
use spec\Netzmacht\Contao\Toolkit\DeprecationSpecHelper;

class ContaoServicesFactorySpec extends ObjectBehavior
{
    use DeprecationSpecHelper;

    // ... existing let()/it_is_initializable()/it_creates_*_adapter()/
    //     it_creates_backend_user_instance()/it_creates_frontend_user_instance()/
    //     expectAdapterWillBeReturned()/expectInstanceWillBeCreated() methods unchanged ...

    public function it_triggers_a_deprecation_warning_when_creating_a_backend_user_instance(
        ContaoFramework $framework,
        BackendUser $backendUser,
    ): void {
        $this->expectInstanceWillBeCreated($framework, BackendUser::class, $backendUser);

        $messages = $this->captureDeprecations(function (): void {
            $this->createBackendUserInstance();
        });

        $this->assertDeprecationTriggered($messages, 'createBackendUserInstance() is deprecated');
    }

    public function it_triggers_a_deprecation_warning_when_creating_a_frontend_user_instance(
        ContaoFramework $framework,
        FrontendUser $frontendUser,
    ): void {
        $this->expectInstanceWillBeCreated($framework, FrontendUser::class, $frontendUser);

        $messages = $this->captureDeprecations(function (): void {
            $this->createFrontendUserInstance();
        });

        $this->assertDeprecationTriggered($messages, 'createFrontendUserInstance() is deprecated');
    }
}
```

- [ ] **Step 4: Create the new `ContaoServicesFactory` docs page (none existed before)**

`docs/dependency-injection/index.rst`:

```rst
Dependency injection
=====================

Toolkit wraps a few Contao Core classes behind small factory/adapter services so they can be
autowired like any other Symfony service.

.. _contents:

.. toctree::
   :maxdepth: 1

   contao-services-factory
```

`docs/dependency-injection/contao-services-factory.rst`:

```rst
ContaoServicesFactory
======================

``Netzmacht\Contao\Toolkit\DependencyInjection\ContaoServicesFactory`` provides
``Contao\CoreBundle\Framework\Adapter`` instances for a number of Contao framework classes
(``Backend``, ``Config``, ``Controller``, ``System``, ``Environment``, ``Frontend``, ``Image``,
``Model``, ``Message``, ``Dbafs``, ``Input``), each registered as its own autowireable service
(e.g. ``netzmacht.contao_toolkit.contao.backend_adapter``).

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\Backend;
   use Contao\CoreBundle\Framework\Adapter;

   final class MyService
   {
       /** @param Adapter<Backend> $backendAdapter */
       public function __construct(private readonly Adapter $backendAdapter)
       {
       }
   }

.. important::

   ``createBackendUserInstance()``/``createFrontendUserInstance()`` (and the corresponding
   ``netzmacht.contao_toolkit.contao.backend_user``/``...frontend_user`` services) are deprecated
   as of 4.1.0 and will be removed in 5.0.0. Unlike the adapter methods above, they resolve the
   legacy ``Contao\BackendUser::getInstance()``/``Contao\FrontendUser::getInstance()`` singleton.
   Migrate to Symfony's security component instead:

   - For a permission check, use
     ``Symfony\Bundle\SecurityBundle\Security::isGranted($permission, $subject)`` with the
     appropriate ``Contao\CoreBundle\Security\ContaoCorePermissions::*`` constant.
   - For the concrete user object, use ``Symfony\Bundle\SecurityBundle\Security::getUser()``
     (optionally combined with an ``instanceof BackendUser``/``FrontendUser`` check).
```

- [ ] **Step 5: Register the new page in the docs toctree**

In `docs/index.rst`:

```rst
.. toctree::
   :maxdepth: 2

   introduction
   data/index
   dca/index
   view/index
   controller/index
   routing/index
   dependency-injection/index
   insert-tags/index
```

- [ ] **Step 6: Add the CHANGELOG entry**

Under `[4.1.0]` → `Deprecated`:

```markdown
 - `DependencyInjection\ContaoServicesFactory::createBackendUserInstance()`/
   `createFrontendUserInstance()` (and the `netzmacht.contao_toolkit.contao.backend_user`/
   `...frontend_user` services). Use `Symfony\Bundle\SecurityBundle\Security::isGranted()`/
   `::getUser()` instead. See `docs/dependency-injection/contao-services-factory.rst`.
```

- [ ] **Step 7: Run the affected spec**

Run: `vendor/bin/phpspec run spec/DependencyInjection/ContaoServicesFactorySpec.php`
Expected: all examples green.

- [ ] **Step 8: Commit**

```bash
git add src/DependencyInjection/ContaoServicesFactory.php src/Resources/config/services.yml spec/DependencyInjection/ContaoServicesFactorySpec.php docs/dependency-injection docs/index.rst CHANGELOG.md
git commit -m "Deprecate ContaoServicesFactory's Backend-/FrontendUser factory methods"
```

---

## Task E.1: Doc-only deprecate the `ResponseTagger` encapsulation

Mandatory dependency of the unchanged old Fragment-Controller base classes (Task A.7 doc-deprecated those, but left them functional) → doc-only, no `trigger_deprecation()` (per Spec 9's mechanics decision — a runtime trigger here would double-warn on every request of every unmigrated project on top of the Fragment-Controller warning already in place, with no added value).

**Files:**
- Modify: `src/Response/ResponseTagger.php`, `src/Response/FosCacheResponseTagger.php`, `src/Response/NoOpResponseTagger.php`, `src/DependencyInjection/Compiler/FosCacheResponseTaggerPass.php`, `src/Exception/InvalidHttpResponseTagException.php`
- Create: `docs/cache/index.rst`, `docs/cache/response-tagger.rst` (no docs page exists for `ResponseTagger` today — new page per the migration's documentation-gap inventory)
- Modify: `docs/index.rst` (toctree)
- Modify: `CHANGELOG.md`
- Test: none (doc-only deprecation, existing `FosCacheResponseTagger`/`NoOpResponseTagger` specs stay unchanged per Spec 9's Testing section)

**Interfaces:** none (docblock-only changes).

- [ ] **Step 1: `src/Response/ResponseTagger.php`**

```php
/**
 * Interface ResponseTagger is introduced as a backward compatibility layer for Contao < 4.6.
 *
 * It allows you to use the response tagger in your userland code. The tags are only added if Contao can handle it
 * (since version 4.6).
 *
 * @deprecated Use Contao\CoreBundle\Cache\CacheTagManager instead. Will be removed in 5.0.
 */
interface ResponseTagger
```

- [ ] **Step 2: `src/Response/FosCacheResponseTagger.php`**

```php
/**
 * @deprecated Use Contao\CoreBundle\Cache\CacheTagManager instead. Will be removed in 5.0.
 */
final class FosCacheResponseTagger implements ResponseTagger
```

- [ ] **Step 3: `src/Response/NoOpResponseTagger.php`**

```php
/**
 * Class NoOpResponseTagger is there for BC reasons. It's used if Contao < 4.6 is used.
 *
 * @deprecated Use Contao\CoreBundle\Cache\CacheTagManager instead. Will be removed in 5.0.
 */
final class NoOpResponseTagger implements ResponseTagger
```

- [ ] **Step 4: `src/DependencyInjection/Compiler/FosCacheResponseTaggerPass.php`**

```php
/**
 * Class FosCacheResponseTaggerPass registers the FosCacheResponseTagger if it's supported.
 *
 * The response tagger is supported since Contao 4.6 if the fos http cache is installed and enabled.
 *
 * @deprecated Part of the deprecated ResponseTagger encapsulation. Will be removed in 5.0.
 */
final class FosCacheResponseTaggerPass implements CompilerPass
```

- [ ] **Step 5: `src/Exception/InvalidHttpResponseTagException.php`**

```php
/**
 * Class InvalidHttpResponseTagException is thrown when applying tags to response failed
 *
 * @deprecated Part of the deprecated ResponseTagger encapsulation. Will be removed in 5.0.
 */
final class InvalidHttpResponseTagException extends InvalidArgumentException implements Exception
```

- [ ] **Step 6: Create `docs/cache/index.rst` and `docs/cache/response-tagger.rst` (none existed before)**

`docs/cache/index.rst`:

```rst
Cache
=====

.. _contents:

.. toctree::
   :maxdepth: 1

   response-tagger
```

`docs/cache/response-tagger.rst`:

```rst
ResponseTagger
===============

.. important::

   ``Netzmacht\Contao\Toolkit\Response\ResponseTagger`` (and its ``FosCacheResponseTagger``/
   ``NoOpResponseTagger`` implementations) is deprecated as of 4.1.0 and will be removed in 5.0.0.
   It was introduced "as a backward compatibility layer for Contao < 4.6". Contao's own
   ``Contao\CoreBundle\Cache\CacheTagManager`` (service ``contao.cache.tag_manager``) now covers
   the same need natively — every ``tagWith*()`` method already no-ops internally when FOS
   HttpCache isn't installed, exactly what the toolkit's own encapsulation was for.

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\CoreBundle\Cache\CacheTagManager;

   final class MyService
   {
       public function __construct(private readonly CacheTagManager $cacheTagManager)
       {
       }

       public function example(object $model): void
       {
           // Instead of: $this->responseTagger->addTags(['contao.db.tl_example.1']);
           $this->cacheTagManager->tagWithModelInstance($model);
       }
   }

``CacheTagManager`` additionally offers automatic tag derivation from Model/Entity instances and
classes, and tag invalidation via ``invalidateTagsFor()``/``invalidateTags()`` — capabilities the
toolkit's own ``addTags()``-only interface never provided.
```

- [ ] **Step 7: Register the new page in the docs toctree**

In `docs/index.rst`:

```rst
.. toctree::
   :maxdepth: 2

   introduction
   data/index
   dca/index
   view/index
   controller/index
   routing/index
   dependency-injection/index
   cache/index
   insert-tags/index
```

- [ ] **Step 8: Add the CHANGELOG entry**

Under `[4.1.0]` → `Deprecated`:

```markdown
 - `Response\ResponseTagger`, `FosCacheResponseTagger`, `NoOpResponseTagger`,
   `DependencyInjection\Compiler\FosCacheResponseTaggerPass`,
   `Exception\InvalidHttpResponseTagException`. Use `Contao\CoreBundle\Cache\CacheTagManager`
   instead. See `docs/cache/response-tagger.rst`.
```

- [ ] **Step 9: Verify no runtime behavior changed**

Run: `vendor/bin/phpspec run`
Expected: full suite green (docblock-only edit, no spec changes needed for this task).

- [ ] **Step 10: Commit**

```bash
git add src/Response src/DependencyInjection/Compiler/FosCacheResponseTaggerPass.php src/Exception/InvalidHttpResponseTagException.php docs/cache docs/index.rst CHANGELOG.md
git commit -m "Doc-deprecate the ResponseTagger encapsulation in favor of Contao's CacheTagManager"
```

---

## Task F.1: Doc-deprecate `RenderBackendViewTrait`/`ModuleRenderBackendViewTrait`, add `RenderBackendWildcardTrait`

**Files:**
- Modify: `src/Controller/ContentElement/RenderBackendViewTrait.php`, `src/Controller/FrontendModule/ModuleRenderBackendViewTrait.php` (doc-only, no separate trigger — same reasoning as Task E.1: dominant usage is via the already doc-deprecated old Fragment-Controller hierarchy)
- Create: `src/Controller/Fragment/RenderBackendWildcardTrait.php`
- Test: Create `spec/Controller/Fragment/ConcreteRenderBackendWildcardController.php`, `spec/Controller/Fragment/RenderBackendWildcardTraitSpec.php`
- Create: `docs/controller/render-backend-wildcard.rst`
- Modify: `docs/controller/index.rst`, `docs/controller/fragment.rst` (fix the `:doc:` cross-reference added in Task A.8, which pointed at this not-yet-existing page)
- Modify: `CHANGELOG.md`

**Interfaces:**
- Consumes: `Contao\CoreBundle\Controller\AbstractFragmentController::isBackendScope(?Request): bool`, `Symfony\Bundle\FrameworkBundle\Controller\AbstractController::render(string, array, ?Response): Response` (both already inherited by `Controller\Fragment\AbstractContentElementController`, Task A.6).
- Produces: `protected function renderBackendWildcard(ContentModel $model, Request $request): Response` — opt-in, called by a consumer's own `preGenerate()` hook.

- [ ] **Step 1: Doc-deprecate the two old traits**

`src/Controller/ContentElement/RenderBackendViewTrait.php`:

```php
/**
 * The RenderBackendViewTrait renders the backend placeholder view for content elements
 *
 * @deprecated Use Netzmacht\Contao\Toolkit\Controller\Fragment\RenderBackendWildcardTrait instead.
 *             Will be removed in 5.0.
 */
trait RenderBackendViewTrait
```

`src/Controller/FrontendModule/ModuleRenderBackendViewTrait.php`:

```php
/**
 * The RenderBackendViewTrait renders the backend placeholder view for modules
 *
 * @deprecated No successor needed: Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController
 *             already renders the backend wildcard automatically before getResponse() is called.
 *             Will be removed in 5.0.
 */
trait ModuleRenderBackendViewTrait
```

- [ ] **Step 2: Create `Controller\Fragment\RenderBackendWildcardTrait`**

```php
<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Controller\Fragment;

use Contao\ContentModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

use function sprintf;

/**
 * Opt-in trait rendering the "###Wildcard###" backend placeholder for content elements that need
 * one (e.g. structural/side-effecting elements like sliders, accordions or forms). Not
 * auto-wired into Controller\Fragment\AbstractContentElementController — call it from your own
 * preGenerate() hook when $this->isBackendScope($request) is true.
 */
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
            'id' => $model->id,
            'link' => $name,
            'href' => $href,
        ]);
    }

    abstract protected function getType(): string;

    /** @return array<string,string> */
    public static function getSubscribedServices(): array
    {
        return [...parent::getSubscribedServices(), 'translator' => TranslatorInterface::class];
    }
}
```

- [ ] **Step 3: Create the test fixture**

`spec/Controller/Fragment/ConcreteRenderBackendWildcardController.php`:

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Controller\Fragment;

use Contao\ContentModel;
use Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractContentElementController;
use Netzmacht\Contao\Toolkit\Controller\Fragment\RenderBackendWildcardTrait;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ConcreteRenderBackendWildcardController extends AbstractContentElementController
{
    use RenderBackendWildcardTrait;

    public function callRenderBackendWildcard(ContentModel $model, Request $request): Response
    {
        return $this->renderBackendWildcard($model, $request);
    }

    #[Override]
    protected function getType(): string
    {
        return 'text';
    }
}
```

- [ ] **Step 4: Write the spec**

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Controller\Fragment;

use Contao\ContentModel;
use Netzmacht\Contao\Toolkit\Controller\Fragment\RenderBackendWildcardTrait;
use PhpSpec\ObjectBehavior;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RenderBackendWildcardTraitSpec extends ObjectBehavior
{
    public function let(ContainerInterface $container): void
    {
        $this->beAnInstanceOf(ConcreteRenderBackendWildcardController::class);
        $this->beConstructedWith();
        $this->setContainer($container->getWrappedObject());
    }

    public function it_uses_render_backend_wildcard_trait(): void
    {
        $this->shouldUseTrait(RenderBackendWildcardTrait::class);
    }

    public function it_renders_the_backend_wildcard(
        ContainerInterface $container,
        TranslatorInterface $translator,
        RouterInterface $router,
    ): void {
        $model     = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->id = 42;

        $request = Request::create('/contao?do=article');

        $container->get('translator')->willReturn($translator->getWrappedObject());
        $container->get('router')->willReturn($router->getWrappedObject());

        $translator->trans('CTE.text.0', [], 'contao_tl_content')->willReturn('Text');
        $router
            ->generate('contao_backend', ['do' => 'article', 'table' => 'tl_content', 'act' => 'edit', 'id' => 42])
            ->willReturn('/contao?do=article&table=tl_content&act=edit&id=42');

        $this->callRenderBackendWildcard($model, $request)->getContent()->shouldContain('###Text###');
    }
}
```

`shouldUseTrait()` is not a built-in phpspec matcher — replace it with a plain reflection assertion instead (phpspec only generates custom `should*` matchers for methods that exist on the subject, not for trait membership):

```php
    public function it_uses_render_backend_wildcard_trait(): void
    {
        $traits = (new ReflectionClass(ConcreteRenderBackendWildcardController::class))->getTraitNames();

        if (! in_array(RenderBackendWildcardTrait::class, $traits, true)) {
            throw new \RuntimeException('Expected ConcreteRenderBackendWildcardController to use RenderBackendWildcardTrait.');
        }
    }
```

- [ ] **Step 5: Run the new spec**

Run: `vendor/bin/phpspec run spec/Controller/Fragment/RenderBackendWildcardTraitSpec.php`
Expected: all examples green.

- [ ] **Step 6: Create `docs/controller/render-backend-wildcard.rst`**

```rst
RenderBackendWildcardTrait
=============================

``Netzmacht\Contao\Toolkit\Controller\Fragment\RenderBackendWildcardTrait`` renders the classic
"###Wildcard###" backend-editor placeholder for content elements that need one — structural or
side-effecting elements (sliders, accordions, forms) where showing the real frontend markup in
the backend editor would not make sense. Contao Core itself still renders this placeholder for
such elements even in its own modern base classes, just not automatically for custom ones.

Unlike the deprecated ``Controller\ContentElement\RenderBackendViewTrait``, it is **not**
auto-invoked — call it explicitly from your own ``preGenerate()`` hook:

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\ContentModel;
   use Contao\CoreBundle\Twig\FragmentTemplate;
   use Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractContentElementController;
   use Netzmacht\Contao\Toolkit\Controller\Fragment\RenderBackendWildcardTrait;
   use Symfony\Component\HttpFoundation\Request;
   use Symfony\Component\HttpFoundation\Response;

   final class SliderStartController extends AbstractContentElementController
   {
       use RenderBackendWildcardTrait;

       protected function preGenerate(FragmentTemplate $template, ContentModel $model, Request $request): Response|null
       {
           if ($this->isBackendScope($request)) {
               return $this->renderBackendWildcard($model, $request);
           }

           return null;
       }

       protected function getType(): string
       {
           return 'slider_start';
       }
   }

For frontend modules, no equivalent trait is needed:
``Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController::__invoke()``
already renders the backend wildcard automatically before ``getResponse()`` is even called.

.. important::

   If you combine this trait with another trait that also declares its own
   ``getSubscribedServices()`` (e.g. the toolkit's own ``Controller\Fragment\IsHiddenTrait``), PHP
   does not merge same-named trait methods automatically — your consuming class must declare its
   own ``getSubscribedServices()`` that merges both.
```

- [ ] **Step 7: Link the new page from `docs/controller/index.rst` and fix the cross-reference in `docs/controller/fragment.rst`**

In `docs/controller/index.rst`:

```rst
.. toctree::
   :maxdepth: 1

   fragment
   render-backend-wildcard
```

In `docs/controller/fragment.rst`, the sentence added in Task A.8 already references `:doc:`render-backend-wildcard`` — no further edit needed now that the target page exists.

- [ ] **Step 8: Add the CHANGELOG entry**

Under `[4.1.0]`:

```markdown
### Added

 - New `Netzmacht\Contao\Toolkit\Controller\Fragment\RenderBackendWildcardTrait`, an opt-in
   replacement for the deprecated `RenderBackendViewTrait`. See
   `docs/controller/render-backend-wildcard.rst`.

### Deprecated

 - `Controller\ContentElement\RenderBackendViewTrait`,
   `Controller\FrontendModule\ModuleRenderBackendViewTrait`.
```

(append to the existing `[4.1.0]` → `Added`/`Deprecated` subsections started in earlier cluster tasks)

- [ ] **Step 9: Commit**

```bash
git add src/Controller/ContentElement/RenderBackendViewTrait.php src/Controller/FrontendModule/ModuleRenderBackendViewTrait.php src/Controller/Fragment/RenderBackendWildcardTrait.php spec/Controller/Fragment/ConcreteRenderBackendWildcardController.php spec/Controller/Fragment/RenderBackendWildcardTraitSpec.php docs/controller CHANGELOG.md
git commit -m "Deprecate RenderBackendViewTrait, add opt-in RenderBackendWildcardTrait"
```

---

## Task G.1: New `netzmacht.contao_toolkit.dca.auto_callback` tag, `RegisterFieldCallbacksPass`, `RegisterFieldCallbacksListener`

**Files:**
- Create: `src/DependencyInjection/Compiler/RegisterFieldCallbacksPass.php`
- Create: `src/Dca/Listener/RegisterFieldCallbacksListener.php`
- Test: Create `spec/DependencyInjection/Compiler/RegisterFieldCallbacksPassSpec.php`, `spec/Dca/Listener/RegisterFieldCallbacksListenerSpec.php`

**Interfaces:**
- Consumes: `Symfony\Component\DependencyInjection\ContainerBuilder::findTaggedServiceIds(string): array<string,array<int,array<string,string>>>` (tag attributes `key`, `slot`, `method`), `Netzmacht\Contao\Toolkit\Dca\Definition::{get,has,set,modify}` (Task-independent, existing API).
- Produces: `RegisterFieldCallbacksListener::__construct(DcaManager, RequestScopeMatcher, array<string,array{slot:string,service:string,method:string}> $callbacks)`, hooked on `loadDataContainer` in Task G.2 — this is the array shape Task G.2's `listeners.yml` wiring and the compiler pass must agree on.

- [ ] **Step 1: Create the compiler pass**

Modeled directly on the existing `RepositoriesPass` (tagged-service collection into a constructor argument of an already-registered service definition):

```php
<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\DependencyInjection\Compiler;

use Netzmacht\Contao\Toolkit\Dca\Listener\RegisterFieldCallbacksListener;
use Override;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface as CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Collects services tagged with "netzmacht.contao_toolkit.dca.auto_callback" into the config map
 * consumed by RegisterFieldCallbacksListener.
 */
final class RegisterFieldCallbacksPass implements CompilerPass
{
    private const TAG = 'netzmacht.contao_toolkit.dca.auto_callback';

    #[Override]
    public function process(ContainerBuilder $container): void
    {
        if (! $container->hasDefinition(RegisterFieldCallbacksListener::class)) {
            return;
        }

        $callbacks = [];

        foreach ($container->findTaggedServiceIds(self::TAG) as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                $callbacks[$attributes['key']] = [
                    'slot' => $attributes['slot'],
                    'service' => $serviceId,
                    'method' => $attributes['method'],
                ];
            }
        }

        $container->getDefinition(RegisterFieldCallbacksListener::class)->setArgument(2, $callbacks);
    }
}
```

- [ ] **Step 2: Create the listener**

```php
<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener;

use Netzmacht\Contao\Toolkit\Assertion\AssertionFailed;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;

use function array_keys;
use function in_array;

/**
 * Auto-registers the remaining toolkit DCA field callbacks based on "fields.<field>.toolkit.<key>"
 * config presence, so consumers no longer have to wire options_callback/save_callback/wizard
 * manually in addition to the toolkit config.
 */
final class RegisterFieldCallbacksListener
{
    /** Callback slots that hold an array of [class, method] pairs, safe to append to. */
    private const LIST_SLOTS = ['save_callback', 'wizard'];

    /** @param array<string,array{slot:string,service:string,method:string}> $callbacks */
    public function __construct(
        private readonly DcaManager $dcaManager,
        private readonly RequestScopeMatcher $scopeMatcher,
        private readonly array $callbacks,
    ) {
    }

    public function onLoadDataContainer(string $dataContainerName): void
    {
        if (! $this->scopeMatcher->isContaoRequest()) {
            return;
        }

        try {
            $definition = $this->dcaManager->getDefinition($dataContainerName);
        } catch (AssertionFailed) {
            // No valid dca config found. Just ignore the data container.
            return;
        }

        $fields = (array) $definition->get(['fields']);

        foreach ($fields as $field => $config) {
            $toolkitConfig = (array) ($config['toolkit'] ?? []);

            foreach (array_keys($toolkitConfig) as $key) {
                if (! isset($this->callbacks[$key])) {
                    continue;
                }

                $this->registerCallback($definition, (string) $field, $this->callbacks[$key]);
            }
        }
    }

    /** @param array{slot:string,service:string,method:string} $callback */
    private function registerCallback(Definition $definition, string $field, array $callback): void
    {
        $path  = ['fields', $field, $callback['slot']];
        $entry = [$callback['service'], $callback['method']];

        if (in_array($callback['slot'], self::LIST_SLOTS, true)) {
            $definition->modify($path, static function (mixed $current) use ($entry): array {
                $current = (array) $current;

                if (in_array($entry, $current, true)) {
                    return $current;
                }

                return [...$current, $entry];
            });

            return;
        }

        // Single slot (e.g. options_callback): never replace an already-set callback.
        if ($definition->has($path)) {
            return;
        }

        $definition->set($path, $entry);
    }
}
```

- [ ] **Step 3: Write the `RegisterFieldCallbacksPass` spec**

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\DependencyInjection\Compiler;

use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\RegisterFieldCallbacksPass;
use Netzmacht\Contao\Toolkit\Dca\Listener\RegisterFieldCallbacksListener;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class RegisterFieldCallbacksPassSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RegisterFieldCallbacksPass::class);
    }

    public function it_is_a_compiler_pass(): void
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    public function it_does_nothing_if_the_listener_is_not_registered(ContainerBuilder $container): void
    {
        $container->hasDefinition(RegisterFieldCallbacksListener::class)->willReturn(false);
        $container->findTaggedServiceIds(Argument::any())->shouldNotBeCalled();

        $this->process($container);
    }

    public function it_builds_the_callback_map_from_tagged_services(
        ContainerBuilder $container,
        Definition $definition,
    ): void {
        $taggedServices = [
            'App\TemplateOptionsListener' => [
                ['key' => 'template_options', 'slot' => 'options_callback', 'method' => 'onOptionsCallback'],
            ],
            'App\SlugAliasListener' => [
                ['key' => 'alias_generator', 'slot' => 'save_callback', 'method' => 'onSaveCallback'],
            ],
        ];

        $container->hasDefinition(RegisterFieldCallbacksListener::class)->willReturn(true);
        $container->findTaggedServiceIds('netzmacht.contao_toolkit.dca.auto_callback')->willReturn($taggedServices);
        $container->getDefinition(RegisterFieldCallbacksListener::class)->willReturn($definition);

        $definition
            ->setArgument(2, [
                'template_options' => [
                    'slot' => 'options_callback',
                    'service' => 'App\TemplateOptionsListener',
                    'method' => 'onOptionsCallback',
                ],
                'alias_generator' => [
                    'slot' => 'save_callback',
                    'service' => 'App\SlugAliasListener',
                    'method' => 'onSaveCallback',
                ],
            ])
            ->shouldBeCalled();

        $this->process($container);
    }
}
```

- [ ] **Step 4: Write the `RegisterFieldCallbacksListener` spec**

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Listener;

use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Listener\RegisterFieldCallbacksListener;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RuntimeException;

class RegisterFieldCallbacksListenerSpec extends ObjectBehavior
{
    public function let(DcaManager $dcaManager, RequestScopeMatcher $scopeMatcher): void
    {
        $callbacks = [
            'template_options' => [
                'slot' => 'options_callback',
                'service' => 'App\TemplateOptionsListener',
                'method' => 'onOptionsCallback',
            ],
            'alias_generator' => [
                'slot' => 'save_callback',
                'service' => 'App\SlugAliasListener',
                'method' => 'onSaveCallback',
            ],
        ];

        $this->beConstructedWith($dcaManager, $scopeMatcher, $callbacks);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RegisterFieldCallbacksListener::class);
    }

    public function it_does_nothing_outside_a_contao_request(
        RequestScopeMatcher $scopeMatcher,
        DcaManager $dcaManager,
    ): void {
        $scopeMatcher->isContaoRequest()->willReturn(false);
        $dcaManager->getDefinition(Argument::any())->shouldNotBeCalled();

        $this->onLoadDataContainer('tl_example');
    }

    public function it_registers_a_single_slot_callback_when_none_is_set(
        RequestScopeMatcher $scopeMatcher,
        DcaManager $dcaManager,
    ): void {
        $dca        = ['fields' => ['template' => ['toolkit' => ['template_options' => ['prefix' => 'ce_']]]]];
        $definition = new Definition('tl_content', $dca);

        $scopeMatcher->isContaoRequest()->willReturn(true);
        $dcaManager->getDefinition('tl_content')->willReturn($definition);

        $this->onLoadDataContainer('tl_content');

        if ($definition->get(['fields', 'template', 'options_callback']) !== ['App\TemplateOptionsListener', 'onOptionsCallback']) {
            throw new RuntimeException('Expected options_callback to be auto-registered.');
        }
    }

    public function it_does_not_overwrite_an_already_set_single_slot_callback(
        RequestScopeMatcher $scopeMatcher,
        DcaManager $dcaManager,
    ): void {
        $dca = [
            'fields' => [
                'template' => [
                    'toolkit' => ['template_options' => ['prefix' => 'ce_']],
                    'options_callback' => ['App\CustomListener', 'onCustom'],
                ],
            ],
        ];
        $definition = new Definition('tl_content', $dca);

        $scopeMatcher->isContaoRequest()->willReturn(true);
        $dcaManager->getDefinition('tl_content')->willReturn($definition);

        $this->onLoadDataContainer('tl_content');

        if ($definition->get(['fields', 'template', 'options_callback']) !== ['App\CustomListener', 'onCustom']) {
            throw new RuntimeException('Did not expect the manual options_callback to be overwritten.');
        }
    }

    public function it_appends_a_list_slot_callback_without_duplicating_it(
        RequestScopeMatcher $scopeMatcher,
        DcaManager $dcaManager,
    ): void {
        $dca = [
            'fields' => [
                'alias' => [
                    'toolkit' => ['alias_generator' => ['fields' => ['title']]],
                    'save_callback' => [['App\ExistingListener', 'onSave']],
                ],
            ],
        ];
        $definition = new Definition('tl_content', $dca);

        $scopeMatcher->isContaoRequest()->willReturn(true);
        $dcaManager->getDefinition('tl_content')->willReturn($definition);

        $this->onLoadDataContainer('tl_content');
        $this->onLoadDataContainer('tl_content');

        $expected = [
            ['App\ExistingListener', 'onSave'],
            ['App\SlugAliasListener', 'onSaveCallback'],
        ];

        if ($definition->get(['fields', 'alias', 'save_callback']) !== $expected) {
            throw new RuntimeException('Expected the save_callback to be appended exactly once, even across two loadDataContainer calls.');
        }
    }
}
```

- [ ] **Step 5: Run the new specs**

Run: `vendor/bin/phpspec run spec/DependencyInjection/Compiler/RegisterFieldCallbacksPassSpec.php spec/Dca/Listener/RegisterFieldCallbacksListenerSpec.php`
Expected: all examples green.

- [ ] **Step 6: Commit**

```bash
git add src/DependencyInjection/Compiler/RegisterFieldCallbacksPass.php src/Dca/Listener/RegisterFieldCallbacksListener.php spec/DependencyInjection/Compiler/RegisterFieldCallbacksPassSpec.php spec/Dca/Listener/RegisterFieldCallbacksListenerSpec.php
git commit -m "Add RegisterFieldCallbacksPass/RegisterFieldCallbacksListener for DCA callback auto-registration"
```

---

## Task G.2: Wire the auto-callback tag, register the compiler pass, docs and CHANGELOG

**Files:**
- Modify: `src/Resources/config/listeners.yml`
- Modify: `src/NetzmachtContaoToolkitBundle.php`
- Create: `docs/dca/auto-callbacks.rst`
- Modify: `docs/dca/index.rst` (toctree)
- Modify: `CHANGELOG.md`

**Interfaces:**
- Consumes: `RegisterFieldCallbacksListener` (Task G.1), `RegisterFieldCallbacksPass` (Task G.1).
- Produces: nothing new — pure wiring.

- [ ] **Step 1: Tag the three surviving listeners**

In `src/Resources/config/listeners.yml`, add a `tags` entry to each of the three existing service definitions (in addition to their `public: true`):

```yaml
  Netzmacht\Contao\Toolkit\Dca\Listener\Options\TemplateOptionsListener:
    public: true
    arguments:
      - '@netzmacht.contao_toolkit.dca.manager'
      - '@Contao\CoreBundle\Twig\Finder\FinderFactory'
    tags:
      - { name: 'netzmacht.contao_toolkit.dca.auto_callback', key: 'template_options', slot: 'options_callback', method: 'onOptionsCallback' }
```

```yaml
  Netzmacht\Contao\Toolkit\Dca\Listener\Save\SlugAliasListener:
    public: true
    arguments:
      - '@contao.slug'
      - '@database_connection'
      - '@netzmacht.contao_toolkit.dca.manager'
    tags:
      - { name: 'netzmacht.contao_toolkit.dca.auto_callback', key: 'alias_generator', slot: 'save_callback', method: 'onSaveCallback' }
```

```yaml
  Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\PopupWizardListener:
    public: true
    arguments:
      - '@netzmacht.contao_toolkit.template_renderer'
      - '@translator'
      - '@netzmacht.contao_toolkit.dca.manager'
      - '@security.csrf.token_manager'
      - '@router'
      - '%contao.csrf_token_name%'
    tags:
      - { name: 'netzmacht.contao_toolkit.dca.auto_callback', key: 'popup_wizard', slot: 'wizard', method: 'onWizardCallback' }
```

- [ ] **Step 2: Register the new listener service**

In `src/Resources/config/listeners.yml`, add (the third argument — the callback map — is a placeholder overwritten by `RegisterFieldCallbacksPass` at container-compile time):

```yaml
  netzmacht.contao_toolkit.listeners.register_field_callbacks:
    class: Netzmacht\Contao\Toolkit\Dca\Listener\RegisterFieldCallbacksListener
    public: true
    arguments:
      - '@netzmacht.contao_toolkit.dca.manager'
      - '@netzmacht.contao_toolkit.routing.scope_matcher'
      - []
    tags:
      - { name: 'contao.hook', hook: 'loadDataContainer', method: 'onLoadDataContainer' }
```

- [ ] **Step 3: Register the compiler pass**

In `src/NetzmachtContaoToolkitBundle.php`:

```php
<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit;

use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\FosCacheResponseTaggerPass;
use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\RegisterContaoModelPass;
use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\RegisterFieldCallbacksPass;
use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\RepositoriesPass;
use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\TemplateRendererPass;
use Override;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NetzmachtContaoToolkitBundle extends Bundle
{
    #[Override]
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RepositoriesPass());
        $container->addCompilerPass(new FosCacheResponseTaggerPass());
        $container->addCompilerPass(new RegisterContaoModelPass());
        $container->addCompilerPass(new TemplateRendererPass());
        $container->addCompilerPass(new RegisterFieldCallbacksPass());
    }
}
```

- [ ] **Step 4: Smoke-test the container compiles**

Run: `vendor/bin/phpspec run`
Expected: full suite green — none of the existing specs build a real `ContainerBuilder` from the YAML configs, so this step is a manual sanity note rather than an automated one: in a real Contao application (or via a small standalone `ContainerBuilder` + `YamlFileLoader` script), loading `services.yml` + `listeners.yml` and calling `$container->compile()` must not throw (validates the YAML syntax and the new compiler pass wiring).

- [ ] **Step 5: Create `docs/dca/auto-callbacks.rst` (none existed before)**

```rst
Auto-registered field callbacks
=================================

As of 4.1.0, setting a `fields.<field>.toolkit.<key>` configuration for one of the three
remaining toolkit DCA listeners automatically registers the matching Contao callback — you no
longer have to also wire `options_callback`/`save_callback`/`wizard` manually.

============================  =================  ===============================================
Config key                    DCA slot           Listener (method)
============================  =================  ===============================================
`toolkit.template_options`    `options_callback` `TemplateOptionsListener::onOptionsCallback`
`toolkit.alias_generator`     `save_callback`    `SlugAliasListener::onSaveCallback`
`toolkit.popup_wizard`        `wizard`           `PopupWizardListener::onWizardCallback`
============================  =================  ===============================================

.. code-block:: php

   <?php

    // This alone is now enough - no manual save_callback registration needed:
    $GLOBALS['TL_DCA']['tl_example']['fields']['alias']['toolkit']['alias_generator'] = [
        'fields' => ['title'],
    ];

Manually registering a callback remains a fully supported, explicit override:

- For `options_callback` (a single `[class, method]` pair in Contao), a manually set callback
  always wins — auto-registration never overwrites it.
- For `save_callback`/`wizard` (arrays of `[class, method]` pairs in Contao), the toolkit callback
  is appended alongside any manually registered ones, without duplicating itself across repeated
  `loadDataContainer` invocations.

This auto-registration only applies to the three listeners above — the deprecated
`GenerateAliasListener`, `StateButtonCallbackListener`, `ColorPickerListener`, `FilePickerListener`
and `PagePickerListener` are intentionally excluded (see :doc:`callbacks`), as is the generic
`AbstractPickerListener`/`AbstractFieldPickerListener` infrastructure (no fixed service/method to
tag).
```

- [ ] **Step 6: Register the new page in the docs toctree**

In `docs/dca/index.rst`, change:

```rst
.. toctree::
   :maxdepth: 1

   definition
   callbacks
   formatter
```

to:

```rst
.. toctree::
   :maxdepth: 1

   definition
   callbacks
   formatter
   auto-callbacks
```

- [ ] **Step 7: Add the CHANGELOG entry**

Under `[4.1.0]`:

```markdown
### Added

 - Toolkit DCA field callbacks (`TemplateOptionsListener`, `SlugAliasListener`,
   `PopupWizardListener`) are now automatically registered based on
   `fields.<field>.toolkit.<key>` config presence — manual `options_callback`/`save_callback`/
   `wizard` registration is no longer required (but remains a supported explicit override). See
   `docs/dca/auto-callbacks.rst`.
```

- [ ] **Step 8: Commit**

```bash
git add src/Resources/config/listeners.yml src/NetzmachtContaoToolkitBundle.php docs/dca/auto-callbacks.rst docs/dca/index.rst CHANGELOG.md
git commit -m "Wire auto-registration tags for TemplateOptionsListener/SlugAliasListener/PopupWizardListener"
```

---

## Task H.1: `UPGRADE-5.0.md` skeleton

No `UPGRADE.md`/`UPGRADE-5.0.md` exists in the repo today (only `CHANGELOG.md`, Keep-a-Changelog style). This consolidates every spec's already-written "Änderungen Version 5.0.0" section into one consumer-facing migration guide. Its content can only be fully finalized once the 5.0.0 breaking removal actually happens, but the skeleton itself — one section per point, listing what 5.0.0 removes and what to migrate to before then — is fully derivable now from the 11 approved specs and is real, not placeholder, content.

**Files:**
- Create: `UPGRADE-5.0.md`

**Interfaces:** none (documentation only).

- [ ] **Step 1: Create `UPGRADE-5.0.md`**

```markdown
Upgrading from 4.1 to 5.0
==========================

Version 5.0.0 is the breaking release that completes the Contao 6 preparation started in 4.1.0.
Every item below was already deprecated in 4.1.0 (see `CHANGELOG.md`) — this guide only lists what
actually gets **removed** in 5.0.0 and what to migrate to, one section per 4.1.0 deprecation.

Migrate each item while still on 4.1.x (the deprecated code stays fully functional there) before
upgrading to 5.0.0.

## Legacy Contao templates (`View\Template`)

Removed: `View\Template` (interface), `View\Template\TemplateFactory`,
`ToolkitTemplateFactory`, `View\Template\FrontendTemplate`, `BackendTemplate`, `TemplateTrait`,
`View\Template\Exception\HelperNotFound`, `View\Template\Event\GetTemplateHelpersEvent`,
`Subscriber\GetTemplateHelpersListener`, `DependencyInjection\Compiler\TemplateRendererPass`, and
the package's own `src/Resources/contao/templates/*.html5` files.

Migrate to: native Twig templates rendered via `netzmacht.contao_toolkit.template_renderer`
(`DelegatingTemplateRenderer` keeps working, but only renders Twig from 5.0.0 on — its
`TemplateFactory` constructor parameter is dropped, `Twig\Environment` becomes non-nullable).
Replace `$this->helper('name')` calls with Twig-native functions/filters (e.g. `trans()`,
`backend_icon()`). See `docs/view/templates.rst`.

## `RequestScopeMatcher`

Removed: `Routing\RequestScopeMatcher` (`src/Routing/RequestScopeMatcher.php`), the
`netzmacht.contao_toolkit.routing.scope_matcher` service. The four Fragment-Controller base
classes' constructor signatures change from `RequestScopeMatcher` to
`Contao\CoreBundle\Routing\ScopeMatcher` (only relevant if you still use the deprecated old
Fragment-Controller base classes at that point — see below).

Migrate to: `Contao\CoreBundle\Routing\ScopeMatcher` directly. See `docs/routing/scope-matcher.rst`.

## Old Fragment-Controller base classes

Removed: `Controller\AbstractFragmentController`, `Controller\ContentElement\` (old
`AbstractContentElementController`, `IsHiddenTrait`, `RenderBackendViewTrait`),
`Controller\FrontendModule\AbstractFrontendModuleController` (old) + `ModuleRenderBackendViewTrait`,
`Controller\Hybrid\` (entirely).

Migrate to: `Controller\Fragment\AbstractContentElementController`/`AbstractFrontendModuleController`
(no Hybrid successor — split a hybrid controller into a content-element and/or frontend-module
controller). See `docs/controller/fragment.rst`.

## `InsertTag\*`

Removed: `InsertTag\AbstractInsertTagParser`, `AbstractSingleInsertTagParser`, `ArgumentParser`,
`ArgumentParserPlugin` (all of `src/InsertTag/`).

Migrate to: `Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag` +
`ResolvedInsertTag`/`ResolvedParameters`. See `docs/insert-tags/index.rst`.

## DCA wizard listeners (StateButton/Color/File/Page picker)

Removed: `Dca\Listener\Button\StateButtonCallbackListener`, `Dca\Listener\Wizard\ColorPickerListener`,
`FilePickerListener`, `PagePickerListener`.

Unchanged, stay available: `AbstractPickerListener`, `AbstractFieldPickerListener`,
`AbstractWizardListener`, `PopupWizardListener`, `Data\Updater\Updater`/`DatabaseRowUpdater`.

Migrate to: the native `toggle` field eval (state button), `eval => ['colorpicker' => true]`
(color picker), `eval => ['dcaPicker' => [...]]` + `Contao\Backend::getDcaPickerWizard()`
(file/page picker). See `docs/dca/callbacks.rst`.

## `GenerateAliasListener` and the filter/factory alias chain

Removed: `Dca\Listener\Save\GenerateAliasListener`, `Data\Alias\FilterBasedAliasGenerator`,
`Data\Alias\Filter` (interface) and its implementations (`AbstractFilter`, `AbstractValueFilter`,
`SlugifyFilter`, `SuffixFilter`, `ExistingAliasFilter`, `RawValueFilter`),
`Data\Alias\Factory\AliasGeneratorFactory`, `ToolkitAliasGeneratorFactory`. Also removed from
`services.yml`/`listeners.yml`: the corresponding service/alias definitions and the
`netzmacht.contao_toolkit.alias_generator.default` parameter.

Unchanged, stay available: `Data\Alias\SlugAliasGenerator`, `Dca\Listener\Save\SlugAliasListener`,
`Validator`, `UniqueDatabaseValueValidator`, `AliasGenerator` (interface), `InvalidAliasException`.

Migrate to: `SlugAliasListener` (`toolkit.alias_generator.fields`/`unique_key_fields`/
`allow_empty` config, same as before minus `factory`). Note the behavior change: a manually
entered, non-unique alias now throws `InvalidAliasException` instead of being silently
overwritten. See `docs/data/alias.rst`.

## `ContaoServicesFactory` user factories

Removed: `DependencyInjection\ContaoServicesFactory::createBackendUserInstance()`,
`createFrontendUserInstance()`; the `netzmacht.contao_toolkit.contao.backend_user`/
`...frontend_user` services. All other `ContaoServicesFactory` methods (the `Adapter`-based ones)
are unaffected.

Migrate to: `Symfony\Bundle\SecurityBundle\Security::isGranted()` (permission checks) or
`::getUser()` (concrete user object). See `docs/dependency-injection/contao-services-factory.rst`.

## `ResponseTagger`

Removed: `Response\ResponseTagger`, `FosCacheResponseTagger`, `NoOpResponseTagger`,
`DependencyInjection\Compiler\FosCacheResponseTaggerPass`,
`Exception\InvalidHttpResponseTagException`; the `netzmacht.contao_toolkit.response_tagger`
service; the compiler-pass registration in `NetzmachtContaoToolkitBundle`. Also removed from
`composer.json`: the `friendsofsymfony/http-cache` `require-dev`/`conflict` entries (if not needed
for anything else by then).

Migrate to: `Contao\CoreBundle\Cache\CacheTagManager` (`contao.cache.tag_manager`). See
`docs/cache/response-tagger.rst`.

## `RenderBackendViewTrait`/`ModuleRenderBackendViewTrait`

Removed: `Controller\ContentElement\RenderBackendViewTrait`,
`Controller\FrontendModule\ModuleRenderBackendViewTrait` (already removed as part of the old
Fragment-Controller hierarchy above, listed separately here for completeness).

Unchanged, stays available: `Controller\Fragment\RenderBackendWildcardTrait`.

Migrate to: for frontend modules, no code needed (Contao Core renders the backend wildcard
automatically). For content elements, opt in to `RenderBackendWildcardTrait` from your own
`preGenerate()` hook. See `docs/controller/render-backend-wildcard.rst`.

## `TemplateOptionsListener` and DCA auto-registration

No removals — both the modern-fragment-template support added to `TemplateOptionsListener` and the
DCA auto-callback-registration tag/compiler pass are permanent additions, unchanged going into
5.0.0.
```

- [ ] **Step 2: Commit**

```bash
git add UPGRADE-5.0.md
git commit -m "Add UPGRADE-5.0.md skeleton consolidating all 4.1.0 deprecations' 5.0.0 removal targets"
```

---

## Task H.2: Final regression run and CHANGELOG consolidation check

**Files:**
- Modify: `CHANGELOG.md` (consolidation pass only, no new content — verify Tasks A.8/B.2/C.8/D.1/E.1/F.1/G.2 produced one coherent `[4.1.0]` section, not fragmented/duplicated subsections)
- Test: full suite

**Interfaces:** none.

- [ ] **Step 1: Run the full spec suite**

Run: `vendor/bin/phpspec run`
Expected: 0 failures. This is the final regression gate for the whole 4.1.0 compat-layer scope — every task in this plan (0 through H.1) has already run its own scoped spec subset; this step re-runs everything together to catch cross-task interference (e.g. two tasks accidentally editing the same file inconsistently).

- [ ] **Step 2: Grep for any stray `@deprecated` without a runtime-trigger/doc-only classification mismatch**

Run: `grep -rn "@deprecated" src/ | wc -l`
Expected: matches the count of deprecated classes/interfaces/traits/methods across all 11 points (Template component: 8 classes/interfaces; `RequestScopeMatcher`: 1; old Fragment-Controller hierarchy: 4; `InsertTag\*`: 4; DCA wizard listeners: 4; `GenerateAliasListener` + filter/factory chain: 10; `ContaoServicesFactory`: 2 methods; `ResponseTagger` chain: 5; `RenderBackendViewTrait`/`ModuleRenderBackendViewTrait`: 2 — cross-check this count against the CHANGELOG's `Deprecated` bullets from Step 3 below, investigate any mismatch before proceeding).

- [ ] **Step 3: Read through the consolidated `[4.1.0]` CHANGELOG section**

Open `CHANGELOG.md` and confirm the `[4.1.0]` section (built up incrementally by Tasks A.8, B.2, C.8, D.1, E.1, F.1, G.2) reads as one coherent set of `Added`/`Changed`/`Deprecated` subsections — merge any subsections that ended up duplicated (e.g. two separate `### Deprecated` headings) into one per category, keeping every individual bullet. Cross-reference against the count from Step 2.

- [ ] **Step 4: Verify `composer.json` and the new `symfony/deprecation-contracts` dependency are consistent**

Run: `composer validate --strict`
Expected: exits 0.

- [ ] **Step 5: Commit (only if Step 3 required CHANGELOG edits)**

```bash
git add CHANGELOG.md
git commit -m "Consolidate CHANGELOG [4.1.0] section"
```

If Step 3 found the CHANGELOG already coherent, skip this commit — there is nothing to commit.

---

**End of plan.** All 4.1.0 compat-layer work is now complete: composer floor raised, every deprecation from the 11 approved specs implemented with its decided mechanic (doc-only vs. runtime-trigger), the new `Controller\Fragment\*`/`SlugAliasGenerator`/`SlugAliasListener`/auto-callback-registration additions in place, documentation and `CHANGELOG.md` updated, and `UPGRADE-5.0.md` seeded for the future breaking release. The actual 5.0.0 removal work itself is explicitly out of scope for this plan (see each spec's own "Änderungen Version 5.0.0" section and the `UPGRADE-5.0.md` skeleton this plan produced).
