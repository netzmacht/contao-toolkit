<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Component\ContentElement;

use Netzmacht\Contao\Toolkit\Component\AbstractComponent;

/**
 * Class AbstractContentElement.
 *
 * @package Netzmacht\Contao\Toolkit\Component\ContentElement
 */
abstract class AbstractContentElement extends AbstractComponent implements ContentElement
{
    /**
     * {@inheritDoc}
     */
    public function generate(): string
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
    protected function isVisible(): bool
    {
        if (TL_MODE === 'FE' && BE_USER_LOGGED_IN) {
            return true;
        }

        if ($this->get('invisible')) {
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
