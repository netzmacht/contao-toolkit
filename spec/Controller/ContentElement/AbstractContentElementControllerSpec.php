<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\System;
use Netzmacht\Contao\Toolkit\Controller\ContentElement\AbstractContentElementController;
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

use function serialize;
use function time;

class AbstractContentElementControllerSpec extends ObjectBehavior
{
    public function let(
        TemplateRenderer $templateRenderer,
        ScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        TokenChecker $tokenChecker,
        RequestStack $requestStack,
        Container $container
    ): void {
        System::setContainer($container->getWrappedObject());

        $container->getParameter('kernel.cache_dir')->willReturn(__DIR__ . '/../../fixtures');
        $container->getParameter('kernel.debug')->willReturn(false);

        $this->beAnInstanceOf(ConcreteContentElementController::class);
        $this->beConstructedWith(
            $templateRenderer,
            new RequestScopeMatcher($scopeMatcher->getWrappedObject(), $requestStack->getWrappedObject()),
            $responseTagger,
            $tokenChecker
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
        $this->shouldHaveType(AbstractContentElementController::class);
    }

    public function it_doesnt_render_an_invisible_element(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TokenChecker $tokenChecker,
        TemplateRenderer $templateRenderer
    ): void {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->invisible = true;

        $scopeMatcher->isBackendRequest($request)->willReturn(false);
        $tokenChecker->hasBackendUser()->willReturn(false);
        $templateRenderer->render(Argument::any())->shouldNotBeCalled();

        $this->__invoke($request, $model, 'main')->getContent()->shouldBe('');
    }

    public function it_doesnt_render_an_element_with_future_start_date(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TokenChecker $tokenChecker,
        TemplateRenderer $templateRenderer
    ): void {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->invisible = false;
        $model->start     = time() + 3600;

        $scopeMatcher->isBackendRequest($request)->willReturn(false);
        $tokenChecker->hasBackendUser()->willReturn(false);
        $templateRenderer->render(Argument::any())->shouldNotBeCalled();

        $this->__invoke($request, $model, 'main')->getContent()->shouldBe('');
    }

    public function it_doesnt_render_an_element_with_past_end_date(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TokenChecker $tokenChecker,
        TemplateRenderer $templateRenderer
    ): void {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->invisible = false;
        $model->stop      = time() - 3600;

        $scopeMatcher->isBackendRequest($request)->willReturn(false);
        $tokenChecker->hasBackendUser()->willReturn(false);
        $templateRenderer->render(Argument::any())->shouldNotBeCalled();

        $this->__invoke($request, $model, 'main')->getContent()->shouldBe('');
    }

    public function it_parses_css_id(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TokenChecker $tokenChecker,
        TemplateRenderer $templateRenderer
    ): void {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->invisible = false;
        $model->cssID     = serialize(['foo', 'bar']);
        $model->headline  = serialize(['value' => 'Headline', 'unit' => 'h1']);

        $scopeMatcher->isBackendRequest($request)->willReturn(false);
        $tokenChecker->hasBackendUser()->willReturn(false);

        $templateRenderer
            ->render(
                'fe:ce_concrete_content_element',
                Argument::allOf(
                    Argument::withEntry('cssID', ' id="foo"'),
                    Argument::withEntry('class', 'ce_concrete_content_element bar')
                )
            )
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->__invoke($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_parses_headline(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TokenChecker $tokenChecker,
        TemplateRenderer $templateRenderer
    ): void {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->invisible = false;
        $model->cssID     = serialize(['foo', 'bar']);
        $model->headline  = serialize(['value' => 'Headline', 'unit' => 'h1']);

        $scopeMatcher->isBackendRequest($request)->willReturn(false);
        $tokenChecker->hasBackendUser()->willReturn(false);

        $templateRenderer
            ->render(
                'fe:ce_concrete_content_element',
                Argument::allOf(
                    Argument::withEntry('headline', 'Headline'),
                    Argument::withEntry('hl', 'h1')
                )
            )
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->__invoke($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_passes_template_data(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TokenChecker $tokenChecker,
        TemplateRenderer $templateRenderer
    ): void {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->invisible = false;
        $model->cssID     = serialize(['foo', 'bar']);
        $model->headline  = serialize(['value' => 'Headline', 'unit' => 'h1']);
        $model->foo       = 'bar';
        $model->baz       = true;

        $scopeMatcher->isBackendRequest($request)->willReturn(false);
        $tokenChecker->hasBackendUser()->willReturn(false);

        $templateRenderer
            ->render(
                'fe:ce_concrete_content_element',
                Argument::allOf(
                    Argument::withEntry('inColumn', 'main'),
                    Argument::withEntry('foo', 'bar'),
                    Argument::withEntry('baz', true)
                )
            )
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->__invoke($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_uses_fragment_option_custom_template(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TokenChecker $tokenChecker,
        TemplateRenderer $templateRenderer
    ): void {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->invisible = false;
        $model->cssID     = serialize(['foo', 'bar']);
        $model->headline  = serialize(['value' => 'Headline', 'unit' => 'h1']);

        $this->setFragmentOptions(['template' => 'ce_baz']);

        $scopeMatcher->isBackendRequest($request)->willReturn(false);
        $tokenChecker->hasBackendUser()->willReturn(false);

        $templateRenderer
            ->render('fe:ce_baz', Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->__invoke($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_prefers_custom_template_before_fragment_options(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TokenChecker $tokenChecker,
        TemplateRenderer $templateRenderer
    ): void {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->invisible = false;
        $model->cssID     = serialize(['foo', 'bar']);
        $model->headline  = serialize(['value' => 'Headline', 'unit' => 'h1']);
        $model->customTpl = 'ce_foo';

        $this->setFragmentOptions(['template' => 'ce_baz']);

        $scopeMatcher->isBackendRequest($request)->willReturn(false);
        $tokenChecker->hasBackendUser()->willReturn(false);

        $templateRenderer
            ->render('fe:ce_foo', Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->__invoke($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_tags_response(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TokenChecker $tokenChecker,
        TemplateRenderer $templateRenderer,
        ResponseTagger $responseTagger
    ): void {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->id        = 1;
        $model->invisible = false;
        $model->cssID     = serialize(['foo', 'bar']);
        $model->headline  = serialize(['value' => 'Headline', 'unit' => 'h1']);
        $model->customTpl = 'ce_foo';

        $this->setFragmentOptions(['template' => 'ce_baz']);

        $scopeMatcher->isBackendRequest($request)->willReturn(false);
        $tokenChecker->hasBackendUser()->willReturn(false);

        $responseTagger->addTags(['contao.db.tl_content.1'])->shouldBeCalled();

        $templateRenderer
            ->render('fe:ce_foo', Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->__invoke($request, $model, 'main')->getContent()->shouldBe('HTML');
    }
}
