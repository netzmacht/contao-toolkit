<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Component\Hybrid;

use Contao\ContentModel;
use Netzmacht\Contao\Toolkit\Component\AbstractComponent;
use Netzmacht\Contao\Toolkit\Component\Module\AbstractModule;

use function defined;
use function time;
use function trim;

/**
 * The abstract hybrid implementation allows to use a component as content element and/or module.
 *
 * It does not depend on an foreign element being loaded and merged as the Contao hybrid does.
 *
 * @deprecated Since 3.5.0 and get removed in 4.0.0
 *
 * @psalm-suppress DeprecatedClass
 * @psalm-suppress DeprecatedInterface
 */
abstract class AbstractHybrid extends AbstractModule implements Hybrid
{
    public function generate(): string
    {
        if ($this->isContentElement()) {
            if (! $this->isVisible()) {
                return '';
            }

            $this->renderInBackendMode = true;
        }

        return parent::generate();
    }

    /**
     * Check if content element is visible.
     */
    protected function isVisible(): bool
    {
        if ((! defined('TL_MODE') && TL_MODE !== 'FE') || (defined('BE_USER_LOGGED_IN') && BE_USER_LOGGED_IN)) {
            return true;
        }

        if ($this->get('invisible')) {
            return false;
        }

        $now   = time();
        $start = $this->get('start');
        $stop  = $this->get('stop');

        // phpcs:disable SlevomatCodingStandard.Operators.DisallowEqualOperators.DisallowedEqualOperator
        return ($start == '' || $start <= $now) && ($stop == '' || $stop >= $now);
        // phpcs:enable SlevomatCodingStandard.Operators.DisallowEqualOperators.DisallowedEqualOperator
    }

    protected function compileCssClass(): string
    {
        if ($this->isContentElement()) {
            /** @psalm-suppress DeprecatedClass */
            return trim('ce_' . $this->get('type') . ' ' . AbstractComponent::compileCssClass());
        }

        return parent::compileCssClass();
    }

    /**
     * Check if hybrid is used as content element.
     */
    protected function isContentElement(): bool
    {
        return $this->getModel() instanceof ContentModel;
    }
}
