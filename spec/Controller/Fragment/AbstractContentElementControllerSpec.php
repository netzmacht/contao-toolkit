<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Controller\Fragment;

use Contao\ContentModel;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\System;
use Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractContentElementController;
use PhpSpec\ObjectBehavior;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function time;

class AbstractContentElementControllerSpec extends ObjectBehavior
{
    public function let(ContainerInterface $container, Container $symfonyContainer): void
    {
        // ContentModel::__set() casts values via Contao\System::getContainer(), so a global
        // Contao container needs to be available for the duration of each example.
        System::setContainer($symfonyContainer->getWrappedObject());
        $symfonyContainer->getParameter('kernel.cache_dir')->willReturn(__DIR__ . '/../../fixtures');
        $symfonyContainer->getParameter('kernel.debug')->willReturn(false);

        $this->beAnInstanceOf(ConcreteContentElementController::class);
        $this->beConstructedWith();
        $this->setContainer($container->getWrappedObject());
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

    public function it_returns_an_empty_response_for_an_invisible_element(
        ContainerInterface $container,
        TokenChecker $tokenChecker,
        ScopeMatcher $scopeMatcher,
    ): void {
        $model            = (new ReflectionClass(ContentModel::class))->newInstanceWithoutConstructor();
        $model->invisible = true;

        $container->get('token_checker')->willReturn($tokenChecker->getWrappedObject());
        $tokenChecker->hasBackendUser()->willReturn(false);

        $request = Request::create('/');

        // isHidden()'s final line falls through to Contao Core's real, inherited
        // isBackendScope($request), which in turn resolves the scope matcher from the
        // container - stub it so a real (frontend) Request correctly resolves to "false".
        $container->get('contao.routing.scope_matcher')->willReturn($scopeMatcher->getWrappedObject());
        $scopeMatcher->isBackendRequest($request)->willReturn(false);

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
