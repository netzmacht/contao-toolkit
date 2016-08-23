<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Component\Hybrid;

use Netzmacht\Contao\Toolkit\Component\Module\AbstractModule;

/**
 * The abstract hybrid implementation allows to use a component as content element and/or module.
 *
 * It does not depend on an foreign element being loaded and merged as the Contao hybrid does.
 *
 * @package Netzmacht\Contao\Toolkit\Component\Hybrid
 */
class AbstractHybrid extends AbstractModule implements Hybrid
{
    /**
     * {@inheritDoc}
     */
    public function generate()
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
    protected function isVisible()
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
     * Check if hybrid is used as content element.
     *
     * @return bool
     */
    private function isContentElement()
    {
        return $this->getModel() instanceof \ContentModel;
    }
}
