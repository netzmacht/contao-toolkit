<?php

namespace spec\Netzmacht\Contao\Toolkit\Security\Csrf;

use Netzmacht\Contao\Toolkit\Security\Csrf\CsrfTokenProvider;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface as CsrfTokenManager;

class CsrfTokenProviderSpec extends ObjectBehavior
{
    private const TOKEN_NAME = 'secret-token';

    public function let(CsrfTokenManager $tokenManager): void
    {
        $this->beConstructedWith($tokenManager, self::TOKEN_NAME);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CsrfTokenProvider::class);
    }

    public function it_gets_token_from_token_manager(CsrfTokenManager $tokenManager, CsrfToken $csrfToken): void
    {
        $tokenManager->getToken('foo')
            ->shouldBeCalledOnce()
            ->willReturn($csrfToken);

        $this->getToken('foo')->shouldReturn($csrfToken);
    }

    public function it_gets_default_token_from_token_manager(CsrfTokenManager $tokenManager, CsrfToken $csrfToken): void
    {
        $tokenManager->getToken(self::TOKEN_NAME)
            ->shouldBeCalledOnce()
            ->willReturn($csrfToken);

        $this->getToken()->shouldReturn($csrfToken);
    }

    public function it_gets_token_value(CsrfTokenManager $tokenManager, CsrfToken $csrfToken): void
    {
        $tokenManager->getToken(self::TOKEN_NAME)
            ->shouldBeCalledOnce()
            ->willReturn($csrfToken);

        $csrfToken->getValue()->willReturn('val');

        $this->getTokenValue()->shouldReturn('val');
    }
}
