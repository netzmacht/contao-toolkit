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

namespace spec\Netzmacht\Contao\Toolkit\Component;

use Netzmacht\Contao\Toolkit\Component\AbstractComponent;

final class ConcreteComponent extends AbstractComponent
{
    /**
     * {@inheritDoc}
     *
     * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod.Found
     */
    public function getTemplateName(): string
    {
        return parent::getTemplateName();
    }
}
