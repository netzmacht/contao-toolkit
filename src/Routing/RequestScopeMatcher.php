<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Routing;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class RequestScopeMatcher
 *
 * @package Netzmacht\Contao\Toolkit\Routing
 */
class RequestScopeMatcher
{
    /**
     * Contao request scope matcher.
     *
     * @var ScopeMatcher
     */
    private $scopeMatcher;

    /**
     * Request stack.
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * RequestScopeMatcher constructor.
     *
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
     *
     * @return bool
     */
    public function isFrontendRequest(Request $request = null): bool
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
     *
     * @return bool
     */
    public function isBackendRequest(Request $request = null): bool
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
     *
     * @return bool
     */
    public function isContaoRequest(Request $request = null): bool
    {
        $request = $request ?: $this->getCurrentRequest();
        if ($request) {
            return $this->scopeMatcher->isContaoRequest($request);
        }

        return false;
    }

    /**
     * Get the current request.
     *
     * @return Request|null
     */
    private function getCurrentRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }
}
