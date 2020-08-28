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

namespace Netzmacht\Contao\Toolkit\Component\Hybrid;

use Contao\ContentModel;
use Netzmacht\Contao\Toolkit\Component\AbstractComponent;
use Netzmacht\Contao\Toolkit\Component\Module\AbstractModule;

/**
 * The abstract hybrid implementation allows to use a component as content element and/or module.
 *
 * It does not depend on an foreign element being loaded and merged as the Contao hybrid does.
 *
 * @package Netzmacht\Contao\Toolkit\Component\Hybrid
 */
abstract class AbstractHybrid extends AbstractModule implements Hybrid
{
    /**
     * {@inheritDoc}
     */
    public function generate(): string
    {
        if ($this->isContentElement()) {
            if (!$this->isVisible()) {
                return '';
            }

            $this->renderInBackendMode = true;
        }

        return parent::generate();
    }

    /**
     * Check if content element is visible.
     *
     * @return bool
     */
    protected function isVisible(): bool
    {
        if (TL_MODE !== 'FE' || !BE_USER_LOGGED_IN) {
            return true;
        }

        if (!$this->get('invisible')) {
            return false;
        }

        $now   = time();
        $start = $this->get('start');
        $stop  = $this->get('stop');

        if (($start != '' && $start > $now) || ($stop != '' && $stop < $now)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function compileCssClass(): string
    {
        if ($this->isContentElement()) {
            return trim('ce_' . $this->get('type') . ' ' . AbstractComponent::compileCssClass());
        }

        return parent::compileCssClass();
    }

    /**
     * Check if hybrid is used as content element.
     *
     * @return bool
     */
    protected function isContentElement(): bool
    {
        return $this->getModel() instanceof ContentModel;
    }
}
