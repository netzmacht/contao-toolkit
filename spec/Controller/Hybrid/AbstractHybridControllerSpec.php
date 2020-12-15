<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

namespace spec\Netzmacht\Contao\Toolkit\Controller\Hybrid;

use Contao\ContentModel;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\ModuleModel;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Netzmacht\Contao\Toolkit\Controller\Hybrid\AbstractHybridController;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use function serialize;
use function time;

class AbstractHybridControllerSpec extends ObjectBehavior
{
    public function let(
        TemplateRenderer $templateRenderer,
        ScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        RouterInterface $router,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        TokenChecker $tokenChecker
    ): void {
        $this->beAnInstanceOf(ConcreteHybridController::class);
        $this->beConstructedWith(
            $templateRenderer,
            new RequestScopeMatcher($scopeMatcher->getWrappedObject(), $requestStack->getWrappedObject()),
            $responseTagger,
            $router,
            $translator,
            $tokenChecker
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AbstractHybridController::class);
    }

    public function it_parses_module_css_id(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TemplateRenderer $templateRenderer
    ): void {
        $model           = (new ReflectionClass(ModuleModel::class))->newInstanceWithoutConstructor();
        $model->cssID    = serialize(['foo', 'bar']);
        $model->headline = serialize(['value' => 'Headline', 'unit' => 'h1']);

        $scopeMatcher->isBackendRequest($request)->willReturn(false);

        $templateRenderer
            ->render(
                'fe:mod_concrete_hybrid',
                Argument::allOf(
                    Argument::withEntry('cssID', ' id="foo"'),
                    Argument::withEntry('class', 'mod_concrete_hybrid bar')
                )
            )
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->renderAsFrontendModule($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_parses_module_headline(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TemplateRenderer $templateRenderer
    ): void {
        $model           = (new ReflectionClass(ModuleModel::class))->newInstanceWithoutConstructor();
        $model->cssID    = serialize(['foo', 'bar']);
        $model->headline = serialize(['value' => 'Headline', 'unit' => 'h1']);

        $scopeMatcher->isBackendRequest($request)->willReturn(false);

        $templateRenderer
            ->render(
                'fe:mod_concrete_hybrid',
                Argument::allOf(
                    Argument::withEntry('headline', 'Headline'),
                    Argument::withEntry('hl', 'h1')
                )
            )
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->renderAsFrontendModule($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_passes_module_template_data(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TemplateRenderer $templateRenderer
    ): void {
        $model           = (new ReflectionClass(ModuleModel::class))->newInstanceWithoutConstructor();
        $model->cssID    = serialize(['foo', 'bar']);
        $model->headline = serialize(['value' => 'Headline', 'unit' => 'h1']);
        $model->foo      = 'bar';
        $model->baz      = true;

        $scopeMatcher->isBackendRequest($request)->willReturn(false);

        $templateRenderer
            ->render(
                'fe:mod_concrete_hybrid',
                Argument::allOf(
                    Argument::withEntry('inColumn', 'main'),
                    Argument::withEntry('foo', 'bar'),
                    Argument::withEntry('baz', true)
                )
            )
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->renderAsFrontendModule($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_uses_module_fragment_option_custom_template(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TemplateRenderer $templateRenderer
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

        $this->renderAsFrontendModule($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_prefers_module_custom_template_before_fragment_options(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TemplateRenderer $templateRenderer
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

        $this->renderAsFrontendModule($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_tags_module_response(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TemplateRenderer $templateRenderer,
        ResponseTagger $responseTagger
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

        $this->renderAsFrontendModule($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_parses_module_backend_view(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TemplateRenderer $templateRenderer
    ): void {
        $model           = (new ReflectionClass(ModuleModel::class))->newInstanceWithoutConstructor();
        $model->cssID    = serialize(['foo', 'bar']);
        $model->headline = serialize(['value' => 'Headline', 'unit' => 'h1']);

        $scopeMatcher->isBackendRequest($request)->willReturn(true);

        $templateRenderer
            ->render(
                'be:be_wildcard',
                Argument::type('array')
            )
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->renderAsFrontendModule($request, $model, 'main')->getContent()->shouldBe('HTML');
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

        $this->renderAsContentElement($request, $model, 'main')->getContent()->shouldBe('');
    }

    public function it_doesnt_render_an_element_with_future_start_date(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TokenChecker $tokenChecker,
        TemplateRenderer $templateRenderer
    ): void {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->invisible = false;
        $model->start     = (time() + 3600);

        $scopeMatcher->isBackendRequest($request)->willReturn(false);
        $tokenChecker->hasBackendUser()->willReturn(false);
        $templateRenderer->render(Argument::any())->shouldNotBeCalled();

        $this->renderAsContentElement($request, $model, 'main')->getContent()->shouldBe('');
    }

    public function it_doesnt_render_an_element_with_past_end_date(
        Request $request,
        ScopeMatcher $scopeMatcher,
        TokenChecker $tokenChecker,
        TemplateRenderer $templateRenderer
    ): void {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->invisible = false;
        $model->stop      = (time() - 3600);

        $scopeMatcher->isBackendRequest($request)->willReturn(false);
        $tokenChecker->hasBackendUser()->willReturn(false);
        $templateRenderer->render(Argument::any())->shouldNotBeCalled();

        $this->renderAsContentElement($request, $model, 'main')->getContent()->shouldBe('');
    }

    public function it_parses_element_css_id(
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
                'fe:ce_concrete_hybrid',
                Argument::allOf(
                    Argument::withEntry('cssID', ' id="foo"'),
                    Argument::withEntry('class', 'ce_concrete_hybrid bar')
                )
            )
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->renderAsContentElement($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_parses_element_headline(
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
                'fe:ce_concrete_hybrid',
                Argument::allOf(
                    Argument::withEntry('headline', 'Headline'),
                    Argument::withEntry('hl', 'h1')
                )
            )
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->renderAsContentElement($request, $model, 'main')->getContent()->shouldBe('HTML');
    }


    public function it_passes_element_template_data(
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
                'fe:ce_concrete_hybrid',
                Argument::allOf(
                    Argument::withEntry('inColumn', 'main'),
                    Argument::withEntry('foo', 'bar'),
                    Argument::withEntry('baz', true)
                )
            )
            ->shouldBeCalled()
            ->willReturn('HTML');

        $this->renderAsContentElement($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_uses_element_fragment_option_custom_template(
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

        $this->renderAsContentElement($request, $model, 'main')->getContent()->shouldBe('HTML');
    }

    public function it_prefers_element_custom_template_before_fragment_options(
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

        $this->renderAsContentElement($request, $model, 'main')->getContent()->shouldBe('HTML');
    }


    public function it_tags_element_response(
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

        $this->renderAsContentElement($request, $model, 'main')->getContent()->shouldBe('HTML');
    }
}
