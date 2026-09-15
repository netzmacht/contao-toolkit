<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Controller\Fragment;

use Contao\ContentModel;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Symfony\Component\HttpFoundation\Request;

use function time;

/**
 * The IsHiddenTrait provides the isHidden() method to check if a content element must be
 * hidden (invisible, not yet started, already stopped) unless previewed by a backend user.
 */
trait IsHiddenTrait
{
    /**
     * Check if a content element is hidden.
     *
     * @param ContentModel $model   The content element.
     * @param Request      $request The current request.
     */
    protected function isHidden(ContentModel $model, Request $request): bool
    {
        /** @psalm-suppress RiskyTruthyFalsyComparison */
        $isInvisible = $model->invisible
            || ($model->start && $model->start > time())
            || ($model->stop && $model->stop <= time());

        if (! $isInvisible) {
            return false;
        }

        $tokenChecker = $this->container->get('token_checker');

        if ($tokenChecker->hasBackendUser() && $tokenChecker->isPreviewMode()) {
            return false;
        }

        return ! $this->isBackendScope($request);
    }

    /** @return array<string,string> */
    public static function getSubscribedServices(): array
    {
        return [...parent::getSubscribedServices(), 'token_checker' => TokenChecker::class];
    }
}
