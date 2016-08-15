<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Component\ContentElement;

use Netzmacht\Contao\Toolkit\Component\AbstractComponent;

/**
 * Class AbstractContentElement.
 *
 * @package Netzmacht\Contao\Toolkit\Component\ContentElement
 */
class AbstractContentElement extends AbstractComponent implements ContentElement
{
    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        if (!$this->isVisible()) {
            return '';
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
}
