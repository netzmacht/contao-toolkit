# Contao 6 Breaking Removal — 5.0.0 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Remove every class/trait/interface/service that was marked `@deprecated` in 4.1.0 as part of the Contao 6 compatibility layer, so netzmacht/contao-toolkit 5.0.0 is a clean, Contao-6-ready breaking release, and publish the consumer-facing `UPGRADE-5.0.md` migration guide.

**Architecture:** Pure removal work. No new abstractions are introduced — every removal target already has a native Contao replacement (documented in the corresponding 4.1.0-era spec) that consumers were pointed at via `@deprecated`/`trigger_deprecation()` since 4.1.0. Each task deletes the deprecated `src/`+`spec/` files for one migration point, updates any DI wiring (`services.yml`/`listeners.yml`/`NetzmachtContaoToolkitBundle.php`) that referenced them, and strips the now-obsolete documentation section for that point.

**Tech Stack:** PHP 8.3+, Symfony DI (`services.yml`/`listeners.yml`, YAML), phpspec 7/8 (`ObjectBehavior`, Prophecy doubles), Sphinx `.rst` docs, Keep a Changelog-style `CHANGELOG.md`. Docker-wrapped toolchain (see Global Constraints).

**Spec:** The 9 approved design docs in `docs/superpowers/specs/2026-09-14-*.md` (twig-template-compat, request-scope-matcher-deprecation, fragment-controller-modernization, insert-tag-deprecation, dca-wizard-listener-deprecation, generate-alias-listener-slug, backend-frontend-user-factory-deprecation, response-tagger-deprecation, render-backend-view-trait-deprecation), each covering both the already-shipped 4.1.0 compat layer and its own "Änderungen Version 5.0.0" section, which this plan implements. `render-backend-view-trait-deprecation-design.md` (point 10) has no removal of its own beyond what point 3's task already covers — folded into Task 3. `template-options-listener-finder-design.md` (point 6) and `auto-register-field-callbacks-design.md` (point 11) require **no removal task** — both are permanent, unchanged 4.1.0 additions; do not invent work for them.

## Global Constraints

- PHP `^8.3`, `contao/core-bundle: ^5.7 || ^6.0`, `symfony/*: ^6.4 || ^7.4 || ^8.0`, `doctrine/dbal: ^3.10 || ^4.4` — already set in `composer.json` this session (companion receipt `projects/contao-bundle/5.7-6.0`), do not re-touch these constraints.
- `dev-develop` branch-alias is already `5.0.x-dev`; `dev-master` stays `4.1.x-dev` — do not touch `extra.branch-alias`.
- All PHP tool invocations (composer, phpspec, phpcq) run through the repo's Docker wrapper, since host PHP is not directly available:
  `docker run --rm -v "$(pwd):/app" -w /app -u $(id -u):$(id -g) 3liz/liz-php-cli:8.4 <command>`
  (composer additionally needs `-v "$HOME/.config/composer:/home/userphp/.config/composer:rw" -e COMPOSER_HOME='/home/userphp/.config/composer'`, not needed for phpspec/phpcq).
- phpspec is invoked directly, not through phpcq: `vendor/bin/phpspec run [path/to/FooSpec.php]`.
- Every removed class must be deleted via `git rm` (not just unlinked), and every `git commit` at the end of a task must include all files touched by that task (src, spec, services.yml/listeners.yml, docs, CHANGELOG.md line if added incrementally — CHANGELOG.md itself is done once in Task 10, not per-task).
- Follow the existing phpspec conventions used throughout `spec/`: `ObjectBehavior`, `let()` for construction, `it_*`-named example methods, `Prophecy\Argument` for loose matching, the repo's own `DeprecationSpecHelper` trait for deprecation-capture assertions.
- `Netzmacht\Contao\Toolkit\View\Assets\AssetsManager`/`GlobalsAssetsManager` are explicitly **out of scope** (user decision, 2026-09-16: stays in as-is, not part of this migration series).

---

## Task 0: Baseline verification

**Files:**
- None modified. Read-only verification task.

**Interfaces:**
- Produces: a documented baseline pass/fail count for later tasks to diff against.

- [ ] **Step 1: Confirm the dependency matrix from this session's prior work**

Run:
```bash
grep -A2 '"name": "contao/core-bundle"' composer.lock | head -3
grep -A2 '"name": "doctrine/dbal"' composer.lock | head -3
```
Expected: `contao/core-bundle` at `6.0.0`, `doctrine/dbal` at `4.4.x`. This was already produced by `composer update` earlier this session (companion receipt switch + branch-alias change) — do not re-run `composer update` as part of this task.

- [ ] **Step 2: Run the full phpspec baseline and record the result**

Run:
```bash
docker run --rm -v "$(pwd):/app" -w /app -u $(id -u):$(id -g) 3liz/liz-php-cli:8.4 vendor/bin/phpspec run
```
Expected at the start of this plan: **306 examples, 264 passed, 42 broken.** The 42 broken examples are all `Error("Call to a member function createSchemaManager() on null")`, surfaced by the `doctrine/dbal` `^4.4` resolution triggered by this session's companion-receipt switch (`Data\Updater\DatabaseRowUpdater::listTableColumns()` at `src/Data/Updater/DatabaseRowUpdater.php:173` calls `$this->connection->createSchemaManager()`, and the phpspec `Connection` double doesn't stub it, so it returns `null`).

This is a **pre-existing regression from the DBAL 4.4 upgrade, not part of any of the 9 migration-point specs**, and out of scope for this plan's removal work. Do not attempt to fix it here — it needs its own decision (new spec point or standalone fix) outside this plan. Record the 42-broken baseline so later tasks in this plan can tell "still the same 42" apart from "I introduced a new failure."

- [ ] **Step 3: No commit for this task** (read-only, nothing to stage).

---

## Task 1: Remove legacy `View\Template` component (Point 1)

**Files:**
- Delete: `src/View/Template.php`, `src/View/Template/TemplateFactory.php`, `src/View/Template/ToolkitTemplateFactory.php`, `src/View/Template/FrontendTemplate.php`, `src/View/Template/BackendTemplate.php`, `src/View/Template/TemplateTrait.php`, `src/View/Template/Exception/HelperNotFound.php`, `src/View/Template/Event/GetTemplateHelpersEvent.php`, `src/View/Template/Subscriber/GetTemplateHelpersListener.php`, `src/DependencyInjection/Compiler/TemplateRendererPass.php`, `src/Resources/contao/templates/be_wizard_picker.html5`, `src/Resources/contao/templates/be_wizard_color_picker.html5`, `src/Resources/contao/templates/be_wizard_popup.html5`
- Delete: `spec/View/Template/Event/GetTemplateHelpersEventSpec.php`, `spec/View/Template/Subscriber/GetTemplateHelpersListenerSpec.php`
- Modify: `src/View/Template/DelegatingTemplateRenderer.php`, `src/View/Template/TemplateRenderer.php`, `src/Resources/config/services.yml`, `src/Resources/config/listeners.yml`, `src/NetzmachtContaoToolkitBundle.php`, `docs/view/templates.rst`
- Test: `spec/View/Template/DelegatingTemplateRendererSpec.php` (rewritten), `spec/NetzmachtContaoToolkitBundleSpec.php` (`it_registers_template_renderer_pass` removed)

**Interfaces:**
- Consumes: nothing new.
- Produces: `DelegatingTemplateRenderer::__construct(Environment $twig)` (was `(TemplateFactory $templateFactory, Environment|null $twig = null)`) — any later task that constructs a `DelegatingTemplateRenderer` double must use this new signature. No later task in this plan does.

- [ ] **Step 1: Delete the removed files**

```bash
git rm src/View/Template.php \
  src/View/Template/TemplateFactory.php \
  src/View/Template/ToolkitTemplateFactory.php \
  src/View/Template/FrontendTemplate.php \
  src/View/Template/BackendTemplate.php \
  src/View/Template/TemplateTrait.php \
  src/View/Template/Exception/HelperNotFound.php \
  src/View/Template/Event/GetTemplateHelpersEvent.php \
  src/View/Template/Subscriber/GetTemplateHelpersListener.php \
  src/DependencyInjection/Compiler/TemplateRendererPass.php \
  src/Resources/contao/templates/be_wizard_picker.html5 \
  src/Resources/contao/templates/be_wizard_color_picker.html5 \
  src/Resources/contao/templates/be_wizard_popup.html5 \
  spec/View/Template/Event/GetTemplateHelpersEventSpec.php \
  spec/View/Template/Subscriber/GetTemplateHelpersListenerSpec.php
```

- [ ] **Step 2: Rewrite `src/View/Template/DelegatingTemplateRenderer.php`**

```php
<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template;

use Override;
use Twig\Environment;

/** Class DelegatingTemplateRenderer renders Twig templates. */
final class DelegatingTemplateRenderer implements TemplateRenderer
{
    public function __construct(private readonly Environment $twig)
    {
    }

    /** {@inheritDoc} */
    #[Override]
    public function render(string $name, array $parameters = []): string
    {
        return $this->twig->render($name, $parameters);
    }
}
```

- [ ] **Step 3: Update the `TemplateRenderer` interface docblock in `src/View/Template/TemplateRenderer.php`**

Replace the interface docblock (lines 7-19) with:

```php
/**
 * The template renderer abstracts the task of rendering Twig templates.
 *
 * Supported template names are:
 *  - twig/template.html.twig
 *  - @Bundle/twig/template.html.twig
 */
```

- [ ] **Step 4: Rewrite `spec/View/Template/DelegatingTemplateRendererSpec.php`**

```php
<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\View\Template;

use Netzmacht\Contao\Toolkit\View\Template\DelegatingTemplateRenderer;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use PhpSpec\ObjectBehavior;
use Twig\Environment;

final class DelegatingTemplateRendererSpec extends ObjectBehavior
{
    public function let(Environment $twig): void
    {
        $this->beConstructedWith($twig);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(DelegatingTemplateRenderer::class);
    }

    public function it_is_a_template_renderer(): void
    {
        $this->shouldBeAnInstanceOf(TemplateRenderer::class);
    }

    public function it_renders_a_twig_template(Environment $twig): void
    {
        $twig->render('foo.html.twig', ['bar' => 'baz'])->willReturn('foo_html');
        $this->render('foo.html.twig', ['bar' => 'baz'])->shouldReturn('foo_html');
    }
}
```

- [ ] **Step 5: `src/Resources/config/services.yml` — remove the template factory service, simplify the renderer's arguments**

Remove:
```yaml
  netzmacht.contao_toolkit.view.template_factory:
    class: Netzmacht\Contao\Toolkit\View\Template\ToolkitTemplateFactory
    arguments:
      - "@event_dispatcher"

```
Change:
```yaml
  netzmacht.contao_toolkit.template_renderer:
    class: Netzmacht\Contao\Toolkit\View\Template\DelegatingTemplateRenderer
    arguments:
      - '@netzmacht.contao_toolkit.view.template_factory'
      - ~
```
to:
```yaml
  netzmacht.contao_toolkit.template_renderer:
    class: Netzmacht\Contao\Toolkit\View\Template\DelegatingTemplateRenderer
    arguments:
      - '@twig'
```

- [ ] **Step 6: `src/Resources/config/listeners.yml` — remove the template-helpers listener**

Remove:
```yaml
  netzmacht.contao_toolkit.listeners.get_template_helpers:
    class: Netzmacht\Contao\Toolkit\View\Template\Subscriber\GetTemplateHelpersListener
    arguments:
      - '@netzmacht.contao_toolkit.assets_manager'
      - '@translator'
    tags:
      - { name: 'kernel.event_listener', event: 'netzmacht.contao_toolkit.view.get_template_helpers', method: 'handle' }

```

- [ ] **Step 7: `src/NetzmachtContaoToolkitBundle.php` — drop `TemplateRendererPass` registration**

Remove the `use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\TemplateRendererPass;` import and the line `$container->addCompilerPass(new TemplateRendererPass());`.

- [ ] **Step 8: `spec/NetzmachtContaoToolkitBundleSpec.php` — remove the `TemplateRendererPass` test**

Remove the `use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\TemplateRendererPass;` import and the entire `it_registers_template_renderer_pass()` method.

- [ ] **Step 9: Rewrite `docs/view/templates.rst`**

```rst
Templates
=========

Toolkit provides a template renderer service that delegates rendering to Twig.

Template renderer
------------------

.. code-block:: php

   <?php

   declare(strict_types=1);

   $renderer = $container->get('netzmacht.contao_toolkit.template_renderer');
   assert($renderer instanceof \Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer);

   echo $renderer->render('templates/views.html.twig', ['foo' => 'bar']);
   echo $renderer->render('@Bundle/templates/views.html.twig', ['foo' => 'bar']);
```

- [ ] **Step 10: Run the affected specs**

Run: `docker run --rm -v "$(pwd):/app" -w /app -u $(id -u):$(id -g) 3liz/liz-php-cli:8.4 vendor/bin/phpspec run spec/View spec/NetzmachtContaoToolkitBundleSpec.php`
Expected: all pass (0 failures beyond the pre-existing 42 DBAL baseline, which this task's specs don't touch).

- [ ] **Step 11: Commit**

```bash
git add src/View src/DependencyInjection/Compiler/TemplateRendererPass.php src/Resources/config/services.yml src/Resources/config/listeners.yml src/NetzmachtContaoToolkitBundle.php spec/View spec/NetzmachtContaoToolkitBundleSpec.php docs/view/templates.rst
git commit -m "Remove legacy View\\Template component (5.0.0)"
```

---

## Task 2: Remove `RequestScopeMatcher` (Point 2)

**Files:**
- Delete: `src/Routing/RequestScopeMatcher.php`, `spec/Routing/RequestScopeMatcherSpec.php`
- Modify: `src/Dca/Listener/SetOperationDataAttributeListener.php`, `src/Dca/Listener/RegisterFieldCallbacksListener.php`, `src/Resources/config/services.yml`, `src/Resources/config/listeners.yml`, `docs/routing/scope-matcher.rst`
- Test: `spec/Dca/Listener/SetOperationDataAttributeListenerSpec.php`, `spec/Dca/Listener/RegisterFieldCallbacksListenerSpec.php`

**Interfaces:**
- Consumes: nothing from Task 1.
- Produces: `SetOperationDataAttributeListener::__construct(DcaManager, Contao\CoreBundle\Routing\ScopeMatcher)` and `RegisterFieldCallbacksListener::__construct(DcaManager, Contao\CoreBundle\Routing\ScopeMatcher, array)` (both were `Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher` before). No later task constructs either class.

**Note:** the two spec files' 4 `RequestScopeMatcher $scopeMatcher` parameters (across both files) all only ever call `->isContaoRequest()` with no arguments — `Contao\CoreBundle\Routing\ScopeMatcher::isContaoRequest()` accepts the same optional-nullable-`Request`-defaulting-to-null signature, so the mocked call sites are unchanged; only the type-hint/import changes.

- [ ] **Step 1: Delete the removed files**

```bash
git rm src/Routing/RequestScopeMatcher.php spec/Routing/RequestScopeMatcherSpec.php
```

- [ ] **Step 2: `src/Dca/Listener/SetOperationDataAttributeListener.php` — switch to the native `ScopeMatcher`**

Change:
```php
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
```
to:
```php
use Contao\CoreBundle\Routing\ScopeMatcher;
```
Change every occurrence of `RequestScopeMatcher` in the class body (the `private RequestScopeMatcher $scopeMatcher;` property, and the `RequestScopeMatcher $scopeMatcher` constructor parameter) to `ScopeMatcher`. The docblock comment "Request scope matcher." / "The scope matcher." stays as-is (still accurate).

- [ ] **Step 3: `src/Dca/Listener/RegisterFieldCallbacksListener.php` — switch to the native `ScopeMatcher`**

Change:
```php
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
```
to:
```php
use Contao\CoreBundle\Routing\ScopeMatcher;
```
Change `private readonly RequestScopeMatcher $scopeMatcher,` (constructor promoted property) to `private readonly ScopeMatcher $scopeMatcher,`.

- [ ] **Step 4: `src/Resources/config/services.yml` — remove the scope-matcher service**

Remove:
```yaml
  netzmacht.contao_toolkit.routing.scope_matcher:
    class: Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher
    arguments:
      - '@contao.routing.scope_matcher'
      - '@request_stack'

```

- [ ] **Step 5: `src/Resources/config/listeners.yml` — repoint both listeners at the native service**

Change (in `netzmacht.contao_toolkit.listeners.set_operation_data_attribute`):
```yaml
    arguments:
      - '@netzmacht.contao_toolkit.dca.manager'
      - '@netzmacht.contao_toolkit.routing.scope_matcher'
```
to:
```yaml
    arguments:
      - '@netzmacht.contao_toolkit.dca.manager'
      - '@contao.routing.scope_matcher'
```

Change (in `Netzmacht\Contao\Toolkit\Dca\Listener\RegisterFieldCallbacksListener`):
```yaml
    arguments:
      - '@netzmacht.contao_toolkit.dca.manager'
      - '@netzmacht.contao_toolkit.routing.scope_matcher'
      - []
```
to:
```yaml
    arguments:
      - '@netzmacht.contao_toolkit.dca.manager'
      - '@contao.routing.scope_matcher'
      - []
```

- [ ] **Step 6: `spec/Dca/Listener/SetOperationDataAttributeListenerSpec.php` — update the double's type**

Change `use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;` to `use Contao\CoreBundle\Routing\ScopeMatcher;`, and replace every `RequestScopeMatcher $scopeMatcher` parameter (in `let()`, `it_does_nothing_outside_a_contao_request()`, `it_sets_the_data_operation_attribute_for_toolkit_operations()`) with `ScopeMatcher $scopeMatcher`. Method bodies (`$scopeMatcher->isContaoRequest()->willReturn(...)`) are unchanged.

- [ ] **Step 7: `spec/Dca/Listener/RegisterFieldCallbacksListenerSpec.php` — update the double's type**

Change `use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;` to `use Contao\CoreBundle\Routing\ScopeMatcher;`, and replace every `RequestScopeMatcher $scopeMatcher` parameter (in `let()` and the four `it_*` methods) with `ScopeMatcher $scopeMatcher`. Method bodies unchanged.

- [ ] **Step 8: Rewrite `docs/routing/scope-matcher.rst`**

```rst
ScopeMatcher
============

Use ``Contao\CoreBundle\Routing\ScopeMatcher`` directly to check the current request scope —
since Contao 5, its ``isFrontendRequest()``/``isBackendRequest()``/``isContaoRequest()`` methods
already accept an optional ``?Request`` argument and fall back to the current request from the
request stack themselves.

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
```

- [ ] **Step 9: Run the affected specs**

Run: `docker run --rm -v "$(pwd):/app" -w /app -u $(id -u):$(id -g) 3liz/liz-php-cli:8.4 vendor/bin/phpspec run spec/Dca/Listener/SetOperationDataAttributeListenerSpec.php spec/Dca/Listener/RegisterFieldCallbacksListenerSpec.php`
Expected: all pass.

- [ ] **Step 10: Commit**

```bash
git add src/Routing src/Dca/Listener/SetOperationDataAttributeListener.php src/Dca/Listener/RegisterFieldCallbacksListener.php src/Resources/config/services.yml src/Resources/config/listeners.yml spec/Routing spec/Dca/Listener/SetOperationDataAttributeListenerSpec.php spec/Dca/Listener/RegisterFieldCallbacksListenerSpec.php docs/routing/scope-matcher.rst
git commit -m "Remove RequestScopeMatcher, use Contao's native ScopeMatcher (5.0.0)"
```

---

## Task 3: Remove old Fragment-Controller hierarchy (Points 3 + 10)

**Files:**
- Delete: `src/Controller/AbstractFragmentController.php`, `src/Controller/ContentElement/AbstractContentElementController.php`, `src/Controller/ContentElement/IsHiddenTrait.php`, `src/Controller/ContentElement/RenderBackendViewTrait.php`, `src/Controller/FrontendModule/AbstractFrontendModuleController.php`, `src/Controller/FrontendModule/ModuleRenderBackendViewTrait.php`, `src/Controller/Hybrid/AbstractHybridController.php`
- Delete: `spec/Controller/ContentElement/AbstractContentElementControllerSpec.php`, `spec/Controller/ContentElement/ConcreteContentElementController.php`, `spec/Controller/FrontendModule/AbstractFrontendModuleControllerSpec.php`, `spec/Controller/FrontendModule/ConcreteFrontendModuleController.php`, `spec/Controller/Hybrid/AbstractHybridControllerSpec.php`, `spec/Controller/Hybrid/ConcreteHybridController.php`
- Modify: `docs/controller/fragment.rst`, `docs/controller/render-backend-wildcard.rst`

**Interfaces:**
- Consumes: nothing.
- Produces: nothing new — `Controller\Fragment\*` (the 4.1.0-introduced replacement classes) already exist and are untouched by this task.

**Note:** `Controller\ContentElement\RenderBackendViewTrait` and `Controller\FrontendModule\ModuleRenderBackendViewTrait` (point 10) live inside the same two directories being emptied by point 3's removal, so both points are handled by one task, as anticipated in the plan's Spec section.

- [ ] **Step 1: Delete the removed files**

```bash
git rm src/Controller/AbstractFragmentController.php \
  src/Controller/ContentElement/AbstractContentElementController.php \
  src/Controller/ContentElement/IsHiddenTrait.php \
  src/Controller/ContentElement/RenderBackendViewTrait.php \
  src/Controller/FrontendModule/AbstractFrontendModuleController.php \
  src/Controller/FrontendModule/ModuleRenderBackendViewTrait.php \
  src/Controller/Hybrid/AbstractHybridController.php \
  spec/Controller/ContentElement/AbstractContentElementControllerSpec.php \
  spec/Controller/ContentElement/ConcreteContentElementController.php \
  spec/Controller/FrontendModule/AbstractFrontendModuleControllerSpec.php \
  spec/Controller/FrontendModule/ConcreteFrontendModuleController.php \
  spec/Controller/Hybrid/AbstractHybridControllerSpec.php \
  spec/Controller/Hybrid/ConcreteHybridController.php
```

- [ ] **Step 2: Confirm the emptied directories are actually empty, remove them if git left them**

Run: `find src/Controller/ContentElement src/Controller/FrontendModule src/Controller/Hybrid spec/Controller/ContentElement spec/Controller/FrontendModule spec/Controller/Hybrid -type f 2>&1`
Expected: `src/Controller/ContentElement/` and `src/Controller/FrontendModule/` and `spec/Controller/ContentElement/` and `spec/Controller/FrontendModule/` are empty (git doesn't track empty dirs, they'll simply disappear after commit); `src/Controller/Hybrid/` and `spec/Controller/Hybrid/` no longer exist at all (single file each, both removed).

- [ ] **Step 3: Strip the deprecation notice from `docs/controller/fragment.rst`**

Remove the `.. important::` block (lines 4-8: the paragraph about the legacy `AbstractFragmentController` and its subclasses being deprecated). The page already documents only the new `Controller\Fragment\*` classes below that notice — no other change needed.

- [ ] **Step 4: Strip the deprecated-trait comparison from `docs/controller/render-backend-wildcard.rst`**

Change:
```rst
Unlike the deprecated ``Controller\ContentElement\RenderBackendViewTrait``, it is **not**
auto-invoked — call it explicitly from your own ``preGenerate()`` hook:
```
to:
```rst
It is **not** auto-invoked — call it explicitly from your own ``preGenerate()`` hook:
```
No other change needed on this page.

- [ ] **Step 5: Run the affected specs**

Run: `docker run --rm -v "$(pwd):/app" -w /app -u $(id -u):$(id -g) 3liz/liz-php-cli:8.4 vendor/bin/phpspec run spec/Controller`
Expected: only `spec/Controller/Fragment/*` specs remain and pass (the new-generation classes, untouched by this task).

- [ ] **Step 6: Commit**

```bash
git add src/Controller docs/controller/fragment.rst docs/controller/render-backend-wildcard.rst
git add -u spec/Controller
git commit -m "Remove old Fragment-Controller hierarchy and its backend-wildcard traits (5.0.0)"
```

---

## Task 4: Remove `InsertTag\*` (Point 4)

**Files:**
- Delete: `src/InsertTag/AbstractInsertTagParser.php`, `src/InsertTag/AbstractSingleInsertTagParser.php`, `src/InsertTag/ArgumentParser.php`, `src/InsertTag/ArgumentParserPlugin.php`
- Delete: `spec/InsertTag/AbstractInsertTagParserSpec.php`, `spec/InsertTag/ArgumentParserSpec.php`, `spec/InsertTag/ConcreteInsertTagParser.php`
- Modify: `docs/insert-tags/index.rst`

**Interfaces:**
- Consumes: nothing.
- Produces: nothing (no replacement abstraction — consumers migrate to Contao's native `#[AsInsertTag]`, already documented).

- [ ] **Step 1: Delete the removed files**

```bash
git rm -r src/InsertTag spec/InsertTag
```

- [ ] **Step 2: Strip the "Deprecated" section from `docs/insert-tags/index.rst`**

Remove the entire `.. _insert-tags-deprecated:` section (from that label through the end of the file — the "Deprecated: Toolkit's own `InsertTag` classes" heading, its `.. important::` box, and the closing paragraph). The file's remaining content (native `#[AsInsertTag]` registration + `ResolvedInsertTag`/`ResolvedParameters` usage) is unaffected and needs no other change.

- [ ] **Step 3: Confirm no source references remain**

Run: `grep -rn "InsertTag\\\\Abstract\|ArgumentParser\b" src/ spec/ 2>/dev/null`
Expected: no matches (the whole component and its test double are gone; nothing else in the toolkit used it).

- [ ] **Step 4: Commit**

```bash
git add docs/insert-tags/index.rst
git add -u src/InsertTag spec/InsertTag
git commit -m "Remove InsertTag\\* component (5.0.0)"
```

---

## Task 5: Remove deprecated DCA wizard/button listeners (Point 5)

**Files:**
- Delete: `src/Dca/Listener/Button/StateButtonCallbackListener.php`, `src/Dca/Listener/Wizard/ColorPickerListener.php`, `src/Dca/Listener/Wizard/FilePickerListener.php`, `src/Dca/Listener/Wizard/PagePickerListener.php`
- Delete: `spec/Dca/Listener/Button/StateButtonCallbackListenerSpec.php`, `spec/Dca/Listener/Wizard/ColorPickerListenerSpec.php`, `spec/Dca/Listener/Wizard/FilePickerListenerSpec.php`, `spec/Dca/Listener/Wizard/PagePickerListenerSpec.php`
- Modify: `src/Resources/config/listeners.yml`, `docs/dca/callbacks.rst`

**Interfaces:**
- Consumes: nothing.
- Produces: nothing (native field-evals `toggle`/`colorpicker`/`dcaPicker` replace them; `AbstractPickerListener`, `AbstractFieldPickerListener`, `AbstractWizardListener`, `PopupWizardListener`, `Data\Updater\Updater`/`DatabaseRowUpdater` are all unchanged and stay).

- [ ] **Step 1: Delete the removed files**

```bash
git rm src/Dca/Listener/Button/StateButtonCallbackListener.php \
  src/Dca/Listener/Wizard/ColorPickerListener.php \
  src/Dca/Listener/Wizard/FilePickerListener.php \
  src/Dca/Listener/Wizard/PagePickerListener.php \
  spec/Dca/Listener/Button/StateButtonCallbackListenerSpec.php \
  spec/Dca/Listener/Wizard/ColorPickerListenerSpec.php \
  spec/Dca/Listener/Wizard/FilePickerListenerSpec.php \
  spec/Dca/Listener/Wizard/PagePickerListenerSpec.php
```

- [ ] **Step 2: `src/Resources/config/listeners.yml` — remove the four service blocks + their aliases**

Remove:
```yaml
  Netzmacht\Contao\Toolkit\Dca\Listener\Button\StateButtonCallbackListener:
    public: true
    arguments:
      - '@netzmacht.contao_toolkit.contao.backend_adapter'
      - '@netzmacht.contao_toolkit.contao.input_adapter'
      - '@netzmacht.contao_toolkit.data.database_row_updater'
      - '@netzmacht.contao_toolkit.dca.manager'
      - '@?monolog.logger.contao.error'

  netzmacht.contao_toolkit.dca.listeners.state_button_callback:
    alias: Netzmacht\Contao\Toolkit\Dca\Listener\Button\StateButtonCallbackListener
    public: true

```
and:
```yaml
  Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\ColorPickerListener:
    public: true
    arguments:
      - '@netzmacht.contao_toolkit.template_renderer'
      - '@translator'
      - '@netzmacht.contao_toolkit.dca.manager'

  netzmacht.contao_toolkit.dca.listeners.color_picker:
    alias: Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\ColorPickerListener
    public: true

```
and:
```yaml
  Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\FilePickerListener:
    public: true
    arguments:
      - '@netzmacht.contao_toolkit.template_renderer'
      - '@translator'
      - '@netzmacht.contao_toolkit.dca.manager'
      - '@netzmacht.contao_toolkit.contao.input_adapter'
      - '@router'

  netzmacht.contao_toolkit.dca.listeners.file_picker:
    alias: Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\FilePickerListener
    public: true

```
and:
```yaml
  Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\PagePickerListener:
    public: true
    arguments:
      - '@netzmacht.contao_toolkit.template_renderer'
      - '@translator'
      - '@netzmacht.contao_toolkit.dca.manager'
      - '@netzmacht.contao_toolkit.contao.input_adapter'
      - '@router'

  netzmacht.contao_toolkit.dca.listeners.page_picker:
    alias: Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\PagePickerListener
    public: true

```
Leave `PopupWizardListener`'s block and every other block untouched.

- [ ] **Step 3: `docs/dca/callbacks.rst` — remove the four deprecated sections and their footnote links**

Remove the `State button callback`, `Color picker wizard`, `File picker wizard`, and `Page picker wizard` sections in full (each spans from its `~~~~~~` heading through its `For more details...` line, inclusive of the `.. important::` box). Remove the corresponding footnote link definitions at the bottom of the file:
```rst
.. _StateButtonCallbackListener: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Callback/Button/StateButtonCallbackListener.php
.. _ColorPickerListener: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Callback/Wizard/ColorPickerListener.php
.. _FilePickerListener: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Callback/Wizard/FilePickerListener.php
.. _PagePickerListener: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Callback/Wizard/PagePickerListener.php
```
Also remove the now-obsolete `.. important::` box from the "Alias generator callback" section (the `GenerateAliasListener` deprecation notice at lines 57-61) — `SlugAliasListener` is the sole option once `GenerateAliasListener` is removed (Task 6 removes it); update the intro line "`SlugAliasListener` uses the..." to read as the only/default option rather than "instead". The "Popup wizard" and "Get templates callback" sections, and the `Invoker` section, are unaffected.

- [ ] **Step 4: Run the affected specs**

Run: `docker run --rm -v "$(pwd):/app" -w /app -u $(id -u):$(id -g) 3liz/liz-php-cli:8.4 vendor/bin/phpspec run spec/Dca/Listener`
Expected: only `SlugAliasListenerSpec`, `TemplateOptionsListenerSpec`, `RegisterFieldCallbacksListenerSpec`, `SetOperationDataAttributeListenerSpec` remain in `spec/Dca/Listener/` (Wizard's `PopupWizardListener` has no spec file today — confirm with `find spec/Dca/Listener/Wizard -type f` before assuming otherwise) and pass.

- [ ] **Step 5: Commit**

```bash
git add src/Resources/config/listeners.yml docs/dca/callbacks.rst
git add -u src/Dca/Listener/Button src/Dca/Listener/Wizard spec/Dca/Listener/Button spec/Dca/Listener/Wizard
git commit -m "Remove deprecated StateButton/ColorPicker/File/PagePicker DCA listeners (5.0.0)"
```

---

## Task 6: Remove `GenerateAliasListener` and the filter/factory chain (Point 7)

**Files:**
- Delete: `src/Dca/Listener/Save/GenerateAliasListener.php`, `src/Data/Alias/FilterBasedAliasGenerator.php`, `src/Data/Alias/Filter.php`, `src/Data/Alias/Filter/AbstractFilter.php`, `src/Data/Alias/Filter/AbstractValueFilter.php`, `src/Data/Alias/Filter/SlugifyFilter.php`, `src/Data/Alias/Filter/SuffixFilter.php`, `src/Data/Alias/Filter/ExistingAliasFilter.php`, `src/Data/Alias/Filter/RawValueFilter.php`, `src/Data/Alias/Factory/AliasGeneratorFactory.php`, `src/Data/Alias/Factory/ToolkitAliasGeneratorFactory.php`
- Delete: `spec/Dca/Listener/Save/GenerateAliasListenerSpec.php`, `spec/Data/Alias/FilterBasedAliasGeneratorSpec.php`, `spec/Data/Alias/Filter/ExistingAliasFilterSpec.php`, `spec/Data/Alias/Filter/RawValueFilterSpec.php`, `spec/Data/Alias/Filter/SlugifyFilterSpec.php`, `spec/Data/Alias/Filter/SuffixFilterSpec.php`
- Modify: `src/Resources/config/services.yml`, `src/Resources/config/listeners.yml`, `docs/data/alias.rst`

**Interfaces:**
- Consumes: nothing.
- Produces: nothing (`Data\Alias\SlugAliasGenerator`, `Dca\Listener\Save\SlugAliasListener`, `Validator`, `UniqueDatabaseValueValidator`, `AliasGenerator` interface, `InvalidAliasException` all stay unchanged).

- [ ] **Step 1: Delete the removed files**

```bash
git rm src/Dca/Listener/Save/GenerateAliasListener.php \
  src/Data/Alias/FilterBasedAliasGenerator.php \
  src/Data/Alias/Filter.php \
  src/Data/Alias/Filter/AbstractFilter.php \
  src/Data/Alias/Filter/AbstractValueFilter.php \
  src/Data/Alias/Filter/SlugifyFilter.php \
  src/Data/Alias/Filter/SuffixFilter.php \
  src/Data/Alias/Filter/ExistingAliasFilter.php \
  src/Data/Alias/Filter/RawValueFilter.php \
  src/Data/Alias/Factory/AliasGeneratorFactory.php \
  src/Data/Alias/Factory/ToolkitAliasGeneratorFactory.php \
  spec/Dca/Listener/Save/GenerateAliasListenerSpec.php \
  spec/Data/Alias/FilterBasedAliasGeneratorSpec.php \
  spec/Data/Alias/Filter/ExistingAliasFilterSpec.php \
  spec/Data/Alias/Filter/RawValueFilterSpec.php \
  spec/Data/Alias/Filter/SlugifyFilterSpec.php \
  spec/Data/Alias/Filter/SuffixFilterSpec.php
```

- [ ] **Step 2: `src/Resources/config/services.yml` — remove the default alias-generator factory service**

Remove:
```yaml
  netzmacht.contao_toolkit.data.alias_generator.factory.default_factory:
    class: Netzmacht\Contao\Toolkit\Data\Alias\Factory\ToolkitAliasGeneratorFactory
    public: true
    arguments:
      - '@database_connection'

```

- [ ] **Step 3: `src/Resources/config/listeners.yml` — remove the parameter and the `GenerateAliasListener` service+alias**

Remove the top-of-file `parameters:` block entirely (it holds only this one now-orphaned parameter):
```yaml
parameters:
  netzmacht.contao_toolkit.alias_generator.default: 'netzmacht.contao_toolkit.data.alias_generator.factory.default_factory'

```
Remove:
```yaml
  Netzmacht\Contao\Toolkit\Dca\Listener\Save\GenerateAliasListener:
    public: true
    arguments:
      - '@service_container'
      - '@netzmacht.contao_toolkit.dca.manager'
      - '%netzmacht.contao_toolkit.alias_generator.default%'

  netzmacht.contao_toolkit.dca.listeners.alias_generator:
    alias: Netzmacht\Contao\Toolkit\Dca\Listener\Save\GenerateAliasListener
    public: true

```
Leave `SlugAliasListener`'s block and alias untouched.

- [ ] **Step 4: Rewrite `docs/data/alias.rst`**

```rst
Alias generator
===============

`Netzmacht\\Contao\\Toolkit\\Data\\Alias\\SlugAliasGenerator` implements the `AliasGenerator`_
interface and delegates to Contao's own `contao.slug` service (`Contao\\CoreBundle\\Slug\\Slug`,
backed by `ausi/slug-generator`). It uses the existing `Validator`_ (typically
`UniqueDatabaseValueValidator`_) to guard uniqueness — including for a manually entered,
non-unique value, which throws `InvalidAliasException`_ instead of being silently overwritten.

Use it via the `SlugAliasListener` callback — see :doc:`../dca/callbacks`.

.. _AliasGenerator: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Alias/AliasGenerator.php
.. _Validator: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Alias/Validator.php
.. _UniqueDatabaseValueValidator: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Alias/Validator/UniqueDatabaseValueValidator.php
.. _InvalidAliasException: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Alias/Exception/InvalidAliasException.php
```

- [ ] **Step 5: Run the affected specs**

Run: `docker run --rm -v "$(pwd):/app" -w /app -u $(id -u):$(id -g) 3liz/liz-php-cli:8.4 vendor/bin/phpspec run spec/Data/Alias spec/Dca/Listener/Save`
Expected: only `SlugAliasGeneratorSpec`, `Alias/Validator/UniqueDatabaseValueValidatorSpec`, `Dca/Listener/Save/SlugAliasListenerSpec` remain and pass.

- [ ] **Step 6: Commit**

```bash
git add src/Resources/config/services.yml src/Resources/config/listeners.yml docs/data/alias.rst
git add -u src/Dca/Listener/Save src/Data/Alias spec/Dca/Listener/Save spec/Data/Alias
git commit -m "Remove GenerateAliasListener and the filter/factory alias chain (5.0.0)"
```

---

## Task 7: Remove `ContaoServicesFactory` user-instance factories (Point 8)

**Files:**
- Modify: `src/DependencyInjection/ContaoServicesFactory.php`, `src/Resources/config/services.yml`, `docs/dependency-injection/contao-services-factory.rst`
- Test: `spec/DependencyInjection/ContaoServicesFactorySpec.php`

**Interfaces:**
- Consumes: nothing.
- Produces: nothing (all other `ContaoServicesFactory` `Adapter`-based methods are unaffected).

- [ ] **Step 1: `src/DependencyInjection/ContaoServicesFactory.php` — remove the two methods**

Remove the `createBackendUserInstance()` method (including its docblock) and the `createFrontendUserInstance()` method (including its docblock). Remove the now-unused imports `use Contao\BackendUser;` and `use Contao\FrontendUser;`. Check whether `use function trigger_deprecation;` is still needed elsewhere in the file — it is not (both call sites of `trigger_deprecation()` were inside the two removed methods), so remove that import too. `createInstance()` (the private helper both methods used) has no other caller in this class — remove it too, along with its docblock. Leave every `create*Adapter()` method and `createAdapter()` untouched.

- [ ] **Step 2: `src/Resources/config/services.yml` — remove the two user-instance services**

Remove:
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

- [ ] **Step 3: `spec/DependencyInjection/ContaoServicesFactorySpec.php` — remove the four now-obsolete examples and the now-unused helper**

Remove `it_creates_backend_user_instance()`, `it_creates_frontend_user_instance()`, `it_triggers_a_deprecation_warning_when_creating_a_backend_user_instance()`, `it_triggers_a_deprecation_warning_when_creating_a_frontend_user_instance()`, and the `expectInstanceWillBeCreated()` helper method (only used by the four removed examples). Remove the now-unused imports `use Contao\BackendUser;` and `use Contao\FrontendUser;`. Remove the `use DeprecationSpecHelper;` trait usage and its `use spec\Netzmacht\Contao\Toolkit\DeprecationSpecHelper;` import — no remaining example in this spec captures deprecations. Leave every `it_creates_*_adapter()` example and `expectAdapterWillBeReturned()` untouched.

- [ ] **Step 4: Rewrite `docs/dependency-injection/contao-services-factory.rst`**

Remove the `.. important::` block (from `.. important::` through the final bullet about `Security::getUser()`) — everything after the first code example. The page's remaining content (the adapter-based intro + example) needs no other change.

- [ ] **Step 5: Run the affected spec**

Run: `docker run --rm -v "$(pwd):/app" -w /app -u $(id -u):$(id -g) 3liz/liz-php-cli:8.4 vendor/bin/phpspec run spec/DependencyInjection/ContaoServicesFactorySpec.php`
Expected: all remaining examples pass.

- [ ] **Step 6: Commit**

```bash
git add src/DependencyInjection/ContaoServicesFactory.php src/Resources/config/services.yml spec/DependencyInjection/ContaoServicesFactorySpec.php docs/dependency-injection/contao-services-factory.rst
git commit -m "Remove ContaoServicesFactory backend/frontend user-instance factories (5.0.0)"
```

---

## Task 8: Remove `ResponseTagger` encapsulation (Point 9)

**Files:**
- Delete: `src/Response/ResponseTagger.php`, `src/Response/FosCacheResponseTagger.php`, `src/Response/NoOpResponseTagger.php`, `src/DependencyInjection/Compiler/FosCacheResponseTaggerPass.php`, `src/Exception/InvalidHttpResponseTagException.php`
- Delete: `spec/Response/FosCacheResponseTaggerSpec.php`, `spec/Response/NoOpResponseTaggerSpec.php`, `spec/DependencyInjection/Compiler/FosCacheResponseTaggerPassSpec.php`
- Modify: `src/Resources/config/services.yml`, `src/NetzmachtContaoToolkitBundle.php`, `composer.json`, `.composer-require-checker.json`, `docs/cache/response-tagger.rst`
- Test: `spec/NetzmachtContaoToolkitBundleSpec.php` (`it_registers_fos_cache_response_tagger_pass` removed)

**Interfaces:**
- Consumes: nothing (any consumer of `ResponseTagger` in the removed old Fragment-Controller hierarchy went away in Task 3 already).
- Produces: nothing (`Contao\CoreBundle\Cache\CacheTagManager` is the documented native replacement, no toolkit abstraction).

- [ ] **Step 1: Delete the removed files**

```bash
git rm -r src/Response \
  src/DependencyInjection/Compiler/FosCacheResponseTaggerPass.php \
  src/Exception/InvalidHttpResponseTagException.php \
  spec/Response \
  spec/DependencyInjection/Compiler/FosCacheResponseTaggerPassSpec.php
```

- [ ] **Step 2: `src/Resources/config/services.yml` — remove the response-tagger service**

Remove:
```yaml
  netzmacht.contao_toolkit.response_tagger:
    class: Netzmacht\Contao\Toolkit\Response\NoOpResponseTagger

```

- [ ] **Step 3: `src/NetzmachtContaoToolkitBundle.php` — drop `FosCacheResponseTaggerPass` registration**

Remove the `use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\FosCacheResponseTaggerPass;` import and the line `$container->addCompilerPass(new FosCacheResponseTaggerPass());`.

- [ ] **Step 4: `spec/NetzmachtContaoToolkitBundleSpec.php` — remove the `FosCacheResponseTaggerPass` test**

Remove the `use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\FosCacheResponseTaggerPass;` import and the entire `it_registers_fos_cache_response_tagger_pass()` method. (This file was already touched in Task 1, Step 8, for the `TemplateRendererPass` removal — both edits land in the same file across two different tasks, on different lines; that's expected.)

- [ ] **Step 5: `composer.json` — drop the `friendsofsymfony/http-cache` dependency**

Remove from `require-dev`:
```json
    "friendsofsymfony/http-cache": "^2.0 || ^3.0",
```
Remove the entire `conflict` block (it only ever held this one entry):
```json
  "conflict": {
    "contao/manager-plugin": "<2.1 || >= 3.0",
    "friendsofsymfony/http-cache": "<2.0 || >=4.0"
  },
```
becomes:
```json
  "conflict": {
    "contao/manager-plugin": "<2.1 || >= 3.0"
  },
```
(Keep the `contao/manager-plugin` conflict entry — unrelated to this removal.)

- [ ] **Step 6: `.composer-require-checker.json` — drop the now-unused FOS symbol whitelist entries**

Remove:
```json
    "FOS\\HttpCache\\ResponseTagger",
    "FOS\\HttpCache\\Exception\\InvalidTagException"
```
(Remove the trailing comma from the preceding `"TL_ERROR"` line so the JSON stays valid.)

- [ ] **Step 7: Rewrite `docs/cache/response-tagger.rst`**

```rst
CacheTagManager
================

Use ``Contao\CoreBundle\Cache\CacheTagManager`` (service ``contao.cache.tag_manager``) to tag
responses for cache invalidation.

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
           $this->cacheTagManager->tagWithModelInstance($model);
       }
   }

``CacheTagManager`` offers automatic tag derivation from Model/Entity instances and classes, and
tag invalidation via ``invalidateTagsFor()``/``invalidateTags()``. Every ``tagWith*()`` method
no-ops internally when FOS HttpCache isn't installed, so callers never need to check whether
caching is active.
```

- [ ] **Step 8: Run composer to confirm the dependency removal resolves cleanly**

Run: `docker run --rm -v "$(pwd):/app" -v "$HOME/.config/composer:/home/userphp/.config/composer:rw" -w /app -u $(id -u):$(id -g) -e COMPOSER_HOME='/home/userphp/.config/composer' 3liz/liz-php-cli:8.4 composer update --no-interaction`
Expected: succeeds, `friendsofsymfony/http-cache` and its own dependencies are removed from `vendor/` and `composer.lock`.

- [ ] **Step 9: Run the affected specs**

Run: `docker run --rm -v "$(pwd):/app" -w /app -u $(id -u):$(id -g) 3liz/liz-php-cli:8.4 vendor/bin/phpspec run spec/NetzmachtContaoToolkitBundleSpec.php`
Expected: `it_is_initializable`, `it_registers_repositories_pass`, `it_registers_contao_model_pass` pass; `it_registers_fos_cache_response_tagger_pass` and `it_registers_template_renderer_pass` no longer exist.

- [ ] **Step 10: Commit**

```bash
git add src/Resources/config/services.yml src/NetzmachtContaoToolkitBundle.php spec/NetzmachtContaoToolkitBundleSpec.php composer.json composer.lock .composer-require-checker.json docs/cache/response-tagger.rst
git add -u src/Response src/DependencyInjection/Compiler/FosCacheResponseTaggerPass.php src/Exception/InvalidHttpResponseTagException.php spec/Response spec/DependencyInjection/Compiler/FosCacheResponseTaggerPassSpec.php
git commit -m "Remove ResponseTagger encapsulation, use Contao's CacheTagManager (5.0.0)"
```

---

## Task 9: Verify no dangling references remain

**Files:**
- None modified unless a gap is found (then fix inline and re-run this task's grep before continuing).

**Interfaces:**
- Consumes: the full removal state from Tasks 1-8.
- Produces: confidence that Task 10-12 build on a self-consistent tree.

- [ ] **Step 1: Grep the whole tree for every removed class/interface/trait name**

Run each of these; every one must return **no matches** (only the spec-doc `docs/superpowers/specs/*.md` files and this plan itself are allowed to still mention the names — restrict the grep to `src/`, `spec/`, `docs/*.rst`, and the config/PHP files that could still wire them):

```bash
grep -rn "View\\\\Template\b\|TemplateFactory\|ToolkitTemplateFactory\|FrontendTemplate\|BackendTemplate\|TemplateTrait\|HelperNotFound\|GetTemplateHelpersEvent\|GetTemplateHelpersListener\|TemplateRendererPass" src/ spec/ docs/*.rst docs/*/*.rst 2>/dev/null
grep -rn "RequestScopeMatcher" src/ spec/ docs/*.rst docs/*/*.rst 2>/dev/null
grep -rn "Controller\\\\AbstractFragmentController\|ContentElement\\\\AbstractContentElementController\|ContentElement\\\\IsHiddenTrait\|ContentElement\\\\RenderBackendViewTrait\|FrontendModule\\\\AbstractFrontendModuleController\|ModuleRenderBackendViewTrait\|Hybrid\\\\AbstractHybridController" src/ spec/ docs/*.rst docs/*/*.rst 2>/dev/null
grep -rn "InsertTag\\\\Abstract\|ArgumentParser\b\|ArgumentParserPlugin" src/ spec/ docs/*.rst docs/*/*.rst 2>/dev/null
grep -rn "StateButtonCallbackListener\|Wizard\\\\ColorPickerListener\|Wizard\\\\FilePickerListener\|Wizard\\\\PagePickerListener" src/ spec/ docs/*.rst docs/*/*.rst 2>/dev/null
grep -rn "GenerateAliasListener\|FilterBasedAliasGenerator\|Data\\\\Alias\\\\Filter\b\|AliasGeneratorFactory\|ToolkitAliasGeneratorFactory" src/ spec/ docs/*.rst docs/*/*.rst 2>/dev/null
grep -rn "createBackendUserInstance\|createFrontendUserInstance" src/ spec/ docs/*.rst docs/*/*.rst 2>/dev/null
grep -rn "ResponseTagger\|FosCacheResponseTagger\|NoOpResponseTagger\|FosCacheResponseTaggerPass\|InvalidHttpResponseTagException" src/ spec/ docs/*.rst docs/*/*.rst 2>/dev/null
```

If any match turns up outside the expected exceptions (there should be none — every reference was accounted for task-by-task above), fix it inline now and re-run that one grep line before moving on.

- [ ] **Step 2: Confirm `services.yml`/`listeners.yml` are valid YAML and every referenced service class still exists**

Run:
```bash
docker run --rm -v "$(pwd):/app" -w /app -u $(id -u):$(id -g) 3liz/liz-php-cli:8.4 php -r "Symfony\Component\Yaml\Yaml::parseFile('/app/src/Resources/config/services.yml'); Symfony\Component\Yaml\Yaml::parseFile('/app/src/Resources/config/listeners.yml'); echo \"OK\n\";" 2>&1 || echo "adjust autoload path if this fails"
```
(If the inline `php -r` autoload path doesn't resolve inside the container, equivalently confirm by running the full phpspec suite in Step 3 below — a broken service definition referencing a removed class would fail loudly there via any spec that boots the container, or via `composer-require-checker`/`psalm` in Task 12's final CI-equivalent run.)

- [ ] **Step 3: Run the full phpspec suite**

Run: `docker run --rm -v "$(pwd):/app" -w /app -u $(id -u):$(id -g) 3liz/liz-php-cli:8.4 vendor/bin/phpspec run`
Expected: the same **42 broken** `createSchemaManager()` examples from Task 0's baseline (still pre-existing, still out of scope), and **zero** new failures. If the broken count differs from 42, something in Tasks 1-8 introduced a regression — find and fix it before continuing.

- [ ] **Step 4: No commit unless Step 1 found something to fix** (if it did, commit that fix with a message like `Fix dangling reference to <ClassName> found during 5.0.0 removal sweep`).

---

## Task 10: Add the `[5.0.0]` CHANGELOG.md entry

**Files:**
- Modify: `CHANGELOG.md`

**Interfaces:**
- Consumes: the full set of removals from Tasks 1-8.
- Produces: nothing (documentation only).

- [ ] **Step 1: Insert a new `[5.0.0]` section above `[4.1.0-beta1]`**

This repo's own precedent for a prior breaking major version (`[4.0.0]`, line 99) uses a `### Breaking` subsection header rather than Keep a Changelog's `### Removed` — follow that established local convention. Insert directly below the `[Unreleased]` heading (line 5) and above `[4.1.0-beta1]` (line 7):

```markdown
[5.0.0]

### Breaking

 - Remove `Netzmacht\Contao\Toolkit\View\Template` and all of its implementations
   (`FrontendTemplate`, `BackendTemplate`, `TemplateTrait`, `TemplateFactory`,
   `ToolkitTemplateFactory`, `GetTemplateHelpersEvent`/`GetTemplateHelpersListener`,
   `HelperNotFound`), the `netzmacht.contao_toolkit.view.template_factory` service, and
   `DependencyInjection\Compiler\TemplateRendererPass`. `DelegatingTemplateRenderer` now only
   renders Twig and requires a non-nullable `Twig\Environment`. Use native Twig templates instead.
 - Remove `Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher` and the
   `netzmacht.contao_toolkit.routing.scope_matcher` service. Use
   `Contao\CoreBundle\Routing\ScopeMatcher` directly.
 - Remove `Netzmacht\Contao\Toolkit\Controller\AbstractFragmentController` and its
   `ContentElement`/`FrontendModule`/`Hybrid` subclasses and traits
   (`ContentElement\IsHiddenTrait`, `ContentElement\RenderBackendViewTrait`,
   `FrontendModule\ModuleRenderBackendViewTrait`). Use
   `Controller\Fragment\AbstractContentElementController`/`AbstractFrontendModuleController`
   instead — there is no `Hybrid` successor, split a hybrid controller into a content-element
   and/or frontend-module controller.
 - Remove `Netzmacht\Contao\Toolkit\InsertTag\AbstractInsertTagParser`,
   `AbstractSingleInsertTagParser`, `ArgumentParser`, `ArgumentParserPlugin`. Use Contao's native
   `Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag` instead.
 - Remove `Dca\Listener\Button\StateButtonCallbackListener`, `Dca\Listener\Wizard\ColorPickerListener`,
   `FilePickerListener`, `PagePickerListener`. Use the native `toggle`, `colorpicker`, and
   `dcaPicker` field evals instead.
 - Remove `Dca\Listener\Save\GenerateAliasListener` and the filter-/factory-based alias generator
   (`Data\Alias\FilterBasedAliasGenerator`, `Data\Alias\Filter` and its implementations,
   `Data\Alias\Factory\AliasGeneratorFactory`, `ToolkitAliasGeneratorFactory`), the
   `netzmacht.contao_toolkit.data.alias_generator.factory.default_factory` service, and the
   `netzmacht.contao_toolkit.alias_generator.default` parameter. Use
   `Dca\Listener\Save\SlugAliasListener` instead.
 - Remove `DependencyInjection\ContaoServicesFactory::createBackendUserInstance()`/
   `createFrontendUserInstance()` and the `netzmacht.contao_toolkit.contao.backend_user`/
   `...frontend_user` services. Use `Symfony\Bundle\SecurityBundle\Security::isGranted()`/
   `::getUser()` instead.
 - Remove `Response\ResponseTagger`, `FosCacheResponseTagger`, `NoOpResponseTagger`,
   `DependencyInjection\Compiler\FosCacheResponseTaggerPass`,
   `Exception\InvalidHttpResponseTagException`, the `netzmacht.contao_toolkit.response_tagger`
   service, and the `friendsofsymfony/http-cache` `require-dev`/`conflict` dependency. Use
   `Contao\CoreBundle\Cache\CacheTagManager` instead.

See `UPGRADE-5.0.md` for the full migration guide.

```

- [ ] **Step 2: Commit**

```bash
git add CHANGELOG.md
git commit -m "Add CHANGELOG entry for 5.0.0"
```

---

## Task 11: Finalize and commit `UPGRADE-5.0.md`

**Files:**
- Modify: `UPGRADE-5.0.md` (currently untracked, drafted before this plan's detailed source research)

**Interfaces:**
- Consumes: the exact removal/migration detail confirmed in Tasks 1-8.
- Produces: nothing (documentation only).

**Corrections needed against the current draft** (cross-checked against the 9 specs' "Änderungen Version 5.0.0" sections and this plan's own research):

- [ ] **Step 1: Add the `SetOperationDataAttributeListener`/`RegisterFieldCallbacksListener` migration detail to the `RequestScopeMatcher` section**

The current draft's `RequestScopeMatcher` section (lines 25-33) only mentions the four Fragment-Controller base classes' constructor signature change. Since those old base classes are themselves removed in this same release (see the "Old Fragment-Controller base classes" section further down), that constructor-signature detail is redundant/confusing as written. Replace the section's second sentence — "The four Fragment-Controller base classes' constructor signatures change from `RequestScopeMatcher` to `Contao\CoreBundle\Routing\ScopeMatcher` (only relevant if you still use the deprecated old Fragment-Controller base classes at that point — see below)." — with a note about the toolkit's own internal listeners, which is the detail actually relevant to a consumer who only referenced `netzmacht.contao_toolkit.routing.scope_matcher` directly:

```markdown
Toolkit's own `SetOperationDataAttributeListener` and `RegisterFieldCallbacksListener` now consume
`Contao\CoreBundle\Routing\ScopeMatcher` (service `contao.routing.scope_matcher`) directly; this is
only relevant if you decorated or replaced either service.
```

- [ ] **Step 2: Add a note about the `friendsofsymfony/http-cache` dependency removal to the `ResponseTagger` section**

The current draft's `ResponseTagger` section (lines 93-103) doesn't mention the `composer.json` cleanup. Add a line after the existing "Migrate to:" sentence:

```markdown
If you only depended on `friendsofsymfony/http-cache` because this package's `require-dev`/
`conflict` entries pulled it in transitively for local testing against `FosCacheResponseTagger`,
those entries are gone too — add `friendsofsymfony/http-cache` to your own project directly if you
still need it independently of this package.
```

- [ ] **Step 3: Verify every other section against Tasks 1-8's actual file lists**

Re-read the current `UPGRADE-5.0.md` against the "Removed:" file lists in this plan's Tasks 1-8 (Step 1 of each task). Confirm every filename matches exactly (e.g. the "Legacy Contao templates" section's file list, the "Old Fragment-Controller base classes" section's file list, etc.). Fix any filename typos or omissions found — there should be none if Tasks 1-8 were followed exactly, but this is the final accuracy gate before the file is committed and published as the consumer-facing migration guide.

- [ ] **Step 4: Stage the file**

```bash
git add UPGRADE-5.0.md
git commit -m "Finalize UPGRADE-5.0.md migration guide"
```

---

## Task 12: Final full regression run and release-prep commit

**Files:**
- None modified (verification only), unless Step 1 finds a regression to fix.

**Interfaces:**
- Consumes: the complete removal + documentation state from Tasks 1-11.
- Produces: a ready-to-release working tree.

- [ ] **Step 1: Full phpspec run**

Run: `docker run --rm -v "$(pwd):/app" -w /app -u $(id -u):$(id -g) 3liz/liz-php-cli:8.4 vendor/bin/phpspec run`
Expected: same **42 pre-existing DBAL-baseline broken examples** as Task 0/Task 9, zero new failures, example count lower than the original 306 (every deleted spec file removes examples — do not treat a lower total as a problem).

- [ ] **Step 2: Full phpcq run (composer-normalize, composer-require-checker, phpcpd, phploc, phpmd, psalm, phpcs)**

Run: `docker run --rm -v "$(pwd):/app" -w /app -u $(id -u):$(id -g) 3liz/liz-php-cli:8.4 vendor/bin/phpcq run -v`
Expected: passes. Pay particular attention to `composer-require-checker` (confirms `.composer-require-checker.json`'s FOS whitelist cleanup from Task 8 was correct and no other removed-but-still-referenced symbol slipped through) and `psalm` (confirms no removed class is still type-referenced anywhere, including docblocks). Fix anything it flags and re-run before continuing.

- [ ] **Step 3: Confirm `git status` is clean and every task's commit is present**

Run: `git log --oneline -13` and `git status --short`
Expected: 11 commits from Tasks 1-2, 3-8, 10, 11 (Task 0 and Task 9 make no commit unless a fix was needed) sitting on top of this session's still-uncommitted `companion.json`/`composer.json`/`composer.lock` changes from Teil A — working tree clean.

- [ ] **Step 4: No further commit needed** — this task is pure verification. If the user wants Teil A's infra changes (companion.json, composer.json constraints/branch-alias, composer.lock) committed too, that's a separate decision outside this plan's scope (it was done before this plan started, at the user's explicit direction, not as one of this plan's tasks).

---

## Self-Review

**Spec coverage:** All 9 specs with a "Änderungen Version 5.0.0" section are covered — Task 1 (twig-template-compat), Task 2 (request-scope-matcher), Task 3 (fragment-controller-modernization + render-backend-view-trait), Task 4 (insert-tag), Task 5 (dca-wizard-listener), Task 6 (generate-alias-listener-slug), Task 7 (backend-frontend-user-factory), Task 8 (response-tagger). Points 6 (template-options-listener-finder) and 11 (auto-register-field-callbacks) correctly have no task — both are confirmed-unchanged 4.1.0 additions per their own specs. Cross-cutting scope (docs updates, CHANGELOG, UPGRADE-5.0.md, composer.json cleanup, no-dangling-references sweep) is covered by Tasks 1-11 inline plus the dedicated Tasks 9-11.

**Placeholder scan:** No "TBD"/"TODO" remains; every removal step lists exact file paths; every modification step shows the exact before/after YAML or PHP; every doc-strip step names the exact section/lines to remove. The one deliberately-soft step (Task 9, Step 2's YAML-validation command) explicitly names its fallback verification path (the full phpspec/phpcq runs) rather than leaving a bare TODO.

**Type consistency:** `DelegatingTemplateRenderer::__construct(Environment $twig)` (Task 1) is not reconstructed anywhere else in the plan. `SetOperationDataAttributeListener`/`RegisterFieldCallbacksListener`'s new `ScopeMatcher` parameter (Task 2) matches the `services.yml`/`listeners.yml` `@contao.routing.scope_matcher` wiring in the same task. No task references a class removed by an earlier task except to delete or update its remaining references (verified by Task 9's grep sweep, which the plan expects to find nothing — every cross-reference was already accounted for task-by-task above).

**Fixed during self-review:** initial draft of Task 5 omitted stripping the `GenerateAliasListener` deprecation notice from `docs/dca/callbacks.rst`'s "Alias generator callback" section (that box only makes sense while `GenerateAliasListener` still exists as a deprecated-but-present alternative) — added to Task 5, Step 3, cross-referencing that `GenerateAliasListener` itself is removed one task later (Task 6), which is fine since both land in the same overall plan before Task 12's final verification.
