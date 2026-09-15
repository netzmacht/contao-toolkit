<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Controller\Fragment;

use Contao\ContentModel;
use Contao\System;
use Netzmacht\Contao\Toolkit\Controller\Fragment\RenderBackendWildcardTrait;
use PhpSpec\ObjectBehavior;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

use function in_array;

class RenderBackendWildcardTraitSpec extends ObjectBehavior
{
    public function let(ContainerInterface $container, Container $symfonyContainer): void
    {
        // ContentModel::__set() casts values via Contao\System::getContainer(), so a global
        // Contao container needs to be available for the duration of each example.
        System::setContainer($symfonyContainer->getWrappedObject());
        $symfonyContainer->getParameter('kernel.cache_dir')->willReturn(__DIR__ . '/../../fixtures');
        $symfonyContainer->getParameter('kernel.debug')->willReturn(false);

        $this->beAnInstanceOf(ConcreteRenderBackendWildcardController::class);
        $this->beConstructedWith();
        $this->setContainer($container->getWrappedObject());
    }

    public function letGo(): void
    {
        $property = new ReflectionProperty(System::class, 'objContainer');
        $property->setAccessible(true);
        $property->setValue(null);
    }

    public function it_uses_render_backend_wildcard_trait(): void
    {
        $traits = (new ReflectionClass(ConcreteRenderBackendWildcardController::class))->getTraitNames();

        if (! in_array(RenderBackendWildcardTrait::class, $traits, true)) {
            throw new RuntimeException('Expected ConcreteRenderBackendWildcardController to use RenderBackendWildcardTrait.');
        }
    }

    public function it_renders_the_backend_wildcard(
        ContainerInterface $container,
        TranslatorInterface $translator,
        RouterInterface $router,
        Environment $twig,
    ): void {
        $model     = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->id = 42;

        $request = Request::create('/contao?do=article');

        $container->get('translator')->willReturn($translator->getWrappedObject());
        $container->get('router')->willReturn($router->getWrappedObject());
        $container->has('twig')->willReturn(true);
        $container->get('twig')->willReturn($twig->getWrappedObject());

        $translator->trans('CTE.text.0', [], 'contao_tl_content')->willReturn('Text');
        $router
            ->generate('contao_backend', ['do' => 'article', 'table' => 'tl_content', 'act' => 'edit', 'id' => 42])
            ->willReturn('/contao?do=article&table=tl_content&act=edit&id=42');

        $twig->render('@Contao/be_wildcard.html.twig', [
            'wildcard' => '###Text###',
            'id' => 42,
            'link' => 'Text',
            'href' => '/contao?do=article&table=tl_content&act=edit&id=42',
        ])->willReturn('###Text### markup');

        $this->callRenderBackendWildcard($model, $request)->getContent()->shouldContain('###Text###');
    }
}
