<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Routing;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestScopeMatcherSpec extends ObjectBehavior
{
    public function let(ScopeMatcher $scopeMatcher, RequestStack $requestStack): void
    {
        $this->beConstructedWith($scopeMatcher, $requestStack);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RequestScopeMatcher::class);
    }

    public function it_detects_frontend_request_from_argument(
        ScopeMatcher $scopeMatcher,
        Request $request,
        Request $request2
    ): void {
        $scopeMatcher
            ->isFrontendRequest($request)
            ->shouldBeCalled()
            ->willReturn(true);

        $scopeMatcher
            ->isFrontendRequest($request2)
            ->shouldBeCalled()
            ->willReturn(false);

        $this->isFrontendRequest($request)->shouldReturn(true);
        $this->isFrontendRequest($request2)->shouldReturn(false);
    }

    public function it_detects_frontend_request_from_the_request_stack(
        ScopeMatcher $scopeMatcher,
        RequestStack $requestStack,
        Request $request
    ): void {
        $requestStack->getCurrentRequest()->willReturn($request);

        $scopeMatcher
            ->isFrontendRequest($request)
            ->shouldBeCalled()
            ->willReturn(true);

        $this->isFrontendRequest()->shouldReturn(true);
    }

    public function it_detects_backend_request_from_argument(
        ScopeMatcher $scopeMatcher,
        Request $request,
        Request $request2
    ): void {
        $scopeMatcher
            ->isBackendRequest($request)
            ->shouldBeCalled()
            ->willReturn(true);

        $scopeMatcher
            ->isBackendRequest($request2)
            ->shouldBeCalled()
            ->willReturn(false);

        $this->isBackendRequest($request)->shouldReturn(true);
        $this->isBackendRequest($request2)->shouldReturn(false);
    }

    public function it_detects_backend_request_from_the_request_stack(
        ScopeMatcher $scopeMatcher,
        RequestStack $requestStack,
        Request $request
    ): void {
        $requestStack->getCurrentRequest()->willReturn($request);

        $scopeMatcher
            ->isBackendRequest($request)
            ->shouldBeCalled()
            ->willReturn(true);

        $this->isBackendRequest()->shouldReturn(true);
    }

    public function it_detects_contao_request_from_argument(
        ScopeMatcher $scopeMatcher,
        Request $request,
        Request $request2
    ): void {
        $scopeMatcher
            ->isContaoRequest($request)
            ->shouldBeCalled()
            ->willReturn(true);

        $scopeMatcher
            ->isContaoRequest($request2)
            ->shouldBeCalled()
            ->willReturn(false);

        $this->isContaoRequest($request)->shouldReturn(true);
        $this->isContaoRequest($request2)->shouldReturn(false);
    }

    public function it_detects_contao_request_from_the_request_stack(
        ScopeMatcher $scopeMatcher,
        RequestStack $requestStack,
        Request $request
    ): void {
        $requestStack->getCurrentRequest()->willReturn($request);

        $scopeMatcher
            ->isContaoRequest($request)
            ->shouldBeCalled()
            ->willReturn(true);

        $this->isContaoRequest()->shouldReturn(true);
    }
}
