<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Routing;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestScopeMatcher
{
    /**
     * Contao request scope matcher.
     */
    private ScopeMatcher $scopeMatcher;

    /**
     * Request stack.
     */
    private RequestStack $requestStack;

    /**
     * @param ScopeMatcher $scopeMatcher Contao request scope matcher.
     * @param RequestStack $requestStack Request stack.
     */
    public function __construct(ScopeMatcher $scopeMatcher, RequestStack $requestStack)
    {
        $this->scopeMatcher = $scopeMatcher;
        $this->requestStack = $requestStack;
    }

    /**
     * Check if the scope of the request is the frontend.
     *
     * If no request is given the current request from the request scope is used.
     *
     * @param Request|null $request Request which should be checked.
     */
    public function isFrontendRequest(Request|null $request = null): bool
    {
        $request = $request ?: $this->getCurrentRequest();
        if ($request) {
            return $this->scopeMatcher->isFrontendRequest($request);
        }

        return false;
    }

    /**
     * Check if the scope of the request is the backend.
     *
     * If no request is given the current request from the request scope is used.
     *
     * @param Request|null $request Request which should be checked.
     */
    public function isBackendRequest(Request|null $request = null): bool
    {
        $request = $request ?: $this->getCurrentRequest();
        if ($request) {
            return $this->scopeMatcher->isBackendRequest($request);
        }

        return false;
    }

    /**
     * Check if the scope of the request is a contao scope.
     *
     * If no request is given the current request from the request scope is used.
     *
     * @param Request|null $request Request which should be checked.
     */
    public function isContaoRequest(Request|null $request = null): bool
    {
        $request = $request ?: $this->getCurrentRequest();
        if ($request) {
            return $this->scopeMatcher->isContaoRequest($request);
        }

        return false;
    }

    /**
     * Check if the route of the request is to the install route.
     *
     * If no request is given the current request from the request scope is used.
     *
     * @param Request|null $request Request which should be checked.
     */
    public function isInstallRequest(Request|null $request = null): bool
    {
        $request = $request ?: $this->getCurrentRequest();

        if ($request) {
            return $request->attributes->get('_route') === 'contao_install';
        }

        return false;
    }

    /**
     * Get the current request.
     */
    private function getCurrentRequest(): Request|null
    {
        return $this->requestStack->getCurrentRequest();
    }
}
