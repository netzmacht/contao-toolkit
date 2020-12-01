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

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Symfony\Component\HttpFoundation\Request;

/**
 * The IsHiddenTrait provides the isHidden() method to check is
 */
trait IsHiddenTrait
{
    /**
     * The token checker.
     *
     * Has to be set in the implementing class.
     *
     * @var TokenChecker
     */
    protected $tokenChecker;

    /**
     * Check if a content element is hidden.
     *
     * @param ContentModel $model   The content element.
     * @param Request      $request The current request.
     *
     * @return bool
     */
    protected function isHidden(ContentModel $model, Request $request): bool
    {
        $isInvisible = $model->invisible
            || ($model->start && $model->start > time())
            || ($model->stop && $model->stop <= time());

        // The element is visible, so show it
        if (!$isInvisible) {
            return false;
        }

        // Preview mode is enabled, so show the element
        if ($this->tokenChecker->hasBackendUser() && $this->tokenChecker->isPreviewMode()) {
            return false;
        }

        // We are in the back end, so show the element
        if ($this->isBackendRequest($request)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the request is a backend request.
     *
     * @param Request $request The current request.
     *
     * @return bool
     */
    abstract protected function isBackendRequest(Request $request): bool;
}
