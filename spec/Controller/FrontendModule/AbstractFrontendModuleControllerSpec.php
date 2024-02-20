<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Controller\FrontendModule;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\ModuleModel;
use Contao\System;
use Netzmacht\Contao\Toolkit\Controller\FrontendModule\AbstractFrontendModuleController;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function serialize;

class AbstractFrontendModuleControllerSpec extends ObjectBehavior
{
    public function let(
        TemplateRenderer $templateRenderer,
        ScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        RouterInterface $router,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        Container $container,
    ): void {
        System::setContainer($container->getWrappedObject());

        $router->generate(Argument::cetera())->willReturn('https://example.org');
        $translator->trans(Argument::cetera())->willReturn('TRANSLATED');

        $container->getParameter('kernel.cache_dir')->willReturn(__DIR__ . '/../../fixtures');
        $container->getParameter('kernel.debug')->willReturn(false);

        $this->beAnInstanceOf(ConcreteFrontendModuleController::class);
        $this->beConstructedWith(
            $templateRenderer,
            new RequestScopeMatcher($scopeMatcher->getWrappedObject(), $requestStack->getWrappedObject()),
            $responseTagger,
            $router,
            $translator,
        );
    }

    public function letGo(): void
    {
        $property = new ReflectionProperty(System::class, 'objContainer');
        $property->setAccessible(true);
        $property->setValue(null);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AbstractFrontendModuleController::class);
    }

    public function it_parses_css_id(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TemplateRenderer $templateRenderer,
    ): void {
        $model           = (new ReflectionClass(ModuleModel::class))->newInstanceWithoutConstructor();
        $model->cssID    = serialize(['foo', 'bar']);
        $model->headline = serialize(['value' => 'Headline', 'unit' => 'h1']);

        $scopeMatcher->isBackendRequest($request)->willReturn(false);

        $templateRenderer
            ->render(
                'fe:mod_concrete_frontend_module',
                Argument::allOf(
                    Argument::withEntry('cssID', ' id="foo"'),
                    Argument::withEntry('class', 'mod_concrete_frontend_module bar'),
                ),
            )
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->__invoke($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_parses_headline(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TemplateRenderer $templateRenderer,
    ): void {
        $model           = (new ReflectionClass(ModuleModel::class))->newInstanceWithoutConstructor();
        $model->cssID    = serialize(['foo', 'bar']);
        $model->headline = serialize(['value' => 'Headline', 'unit' => 'h1']);

        $scopeMatcher->isBackendRequest($request)->willReturn(false);

        $templateRenderer
            ->render(
                'fe:mod_concrete_frontend_module',
                Argument::allOf(
                    Argument::withEntry('headline', 'Headline'),
                    Argument::withEntry('hl', 'h1'),
                ),
            )
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->__invoke($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_passes_template_data(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TemplateRenderer $templateRenderer,
    ): void {
        $model           = (new ReflectionClass(ModuleModel::class))->newInstanceWithoutConstructor();
        $model->cssID    = serialize(['foo', 'bar']);
        $model->headline = serialize(['value' => 'Headline', 'unit' => 'h1']);
        $model->foo      = 'bar';
        $model->baz      = true;

        $scopeMatcher->isBackendRequest($request)->willReturn(false);

        $templateRenderer
            ->render(
                'fe:mod_concrete_frontend_module',
                Argument::allOf(
                    Argument::withEntry('inColumn', 'main'),
                    Argument::withEntry('foo', 'bar'),
                    Argument::withEntry('baz', true),
                ),
            )
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->__invoke($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_uses_fragment_option_custom_template(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TemplateRenderer $templateRenderer,
    ): void {
        $model           = (new ReflectionClass(ModuleModel::class))->newInstanceWithoutConstructor();
        $model->cssID    = serialize(['foo', 'bar']);
        $model->headline = serialize(['value' => 'Headline', 'unit' => 'h1']);

        $this->setFragmentOptions(['template' => 'mod_baz']);

        $scopeMatcher->isBackendRequest($request)->willReturn(false);

        $templateRenderer
            ->render('fe:mod_baz', Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->__invoke($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_prefers_custom_template_before_fragment_options(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TemplateRenderer $templateRenderer,
    ): void {
        $model            = (new ReflectionClass(ModuleModel::class))->newInstanceWithoutConstructor();
        $model->cssID     = serialize(['foo', 'bar']);
        $model->headline  = serialize(['value' => 'Headline', 'unit' => 'h1']);
        $model->customTpl = 'mod_foo';

        $this->setFragmentOptions(['template' => 'mod_baz']);

        $scopeMatcher->isBackendRequest($request)->willReturn(false);

        $templateRenderer
            ->render('fe:mod_foo', Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->__invoke($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_tags_response(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TemplateRenderer $templateRenderer,
        ResponseTagger $responseTagger,
    ): void {
        $model            = (new ReflectionClass(ModuleModel::class))->newInstanceWithoutConstructor();
        $model->id        = 1;
        $model->cssID     = serialize(['foo', 'bar']);
        $model->headline  = serialize(['value' => 'Headline', 'unit' => 'h1']);
        $model->customTpl = 'mod_foo';

        $this->setFragmentOptions(['template' => 'mod_baz']);

        $scopeMatcher->isBackendRequest($request)->willReturn(false);

        $responseTagger->addTags(['contao.db.tl_module.1'])->shouldBeCalled();

        $templateRenderer
            ->render('fe:mod_foo', Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->__invoke($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_parses_backend_view(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TemplateRenderer $templateRenderer,
    ): void {
        $model           = (new ReflectionClass(ModuleModel::class))->newInstanceWithoutConstructor();
        $model->cssID    = serialize(['foo', 'bar']);
        $model->headline = serialize(['value' => 'Headline', 'unit' => 'h1']);

        $scopeMatcher->isBackendRequest($request)->willReturn(true);

        $templateRenderer
            ->render(
                'be:be_wildcard',
                Argument::type('array'),
            )
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->__invoke($request, $model, 'main')->getContent()->shouldBe('HTML');
    }
}
