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
