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
`netzmacht.contao_toolkit.routing.scope_matcher` service. Toolkit's own `SetOperationDataAttributeListener` and `RegisterFieldCallbacksListener` now consume
`Contao\CoreBundle\Routing\ScopeMatcher` (service `contao.routing.scope_matcher`) directly; this is
only relevant if you decorated or replaced either service.

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

If you only depended on `friendsofsymfony/http-cache` because this package's `require-dev`/
`conflict` entries pulled it in transitively for local testing against `FosCacheResponseTagger`,
those entries are gone too — add `friendsofsymfony/http-cache` to your own project directly if you
still need it independently of this package.

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
