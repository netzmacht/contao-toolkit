<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Security\Csrf;

use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Class CsrfTokenProvider is a wrapper of the Csrf token manager to provide access to a default token
 *
 * The default token in the contao context is the REQUEST_TOKEN.
 */
final class CsrfTokenProvider
{
    /**
     * The csrf token manager.
     */
    private CsrfTokenManagerInterface $tokenManager;

    /**
     * The name of the default token.
     */
    private string $defaultTokenName;

    /**
     * @param CsrfTokenManagerInterface $tokenManager     The csrf token manager.
     * @param string                    $defaultTokenName The name of the default token.
     */
    public function __construct(CsrfTokenManagerInterface $tokenManager, string $defaultTokenName)
    {
        $this->tokenManager     = $tokenManager;
        $this->defaultTokenName = $defaultTokenName;
    }

    /**
     * Get the token from the token manager.
     *
     * @param string|null $name Optional name of the token. If empty default name is used.
     */
    public function getToken(string|null $name = null): CsrfToken
    {
        return $this->tokenManager->getToken($name ?? $this->defaultTokenName);
    }

    /**
     * Get the token value from a token from the token manager.
     *
     * @param string|null $name Optional name of the token. If empty default name is used.
     */
    public function getTokenValue(string|null $name = null): string
    {
        return $this->getToken($name)->getValue();
    }
}
