<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Component\ContentElement;

use Netzmacht\Contao\Toolkit\Component\ComponentDecoratorTrait;
use Netzmacht\Contao\Toolkit\DependencyInjection\Services;

/**
 * Class ModuleDecorator.
 *
 * @package Netzmacht\Contao\Toolkit\Component\ContentElement
 */
class ModuleDecorator extends \ContentElement
{
    use ComponentDecoratorTrait;

    /**
     * {@inheritDoc}
     */
    protected function getFactory()
    {
        return $this->getContainer()->get(Services::MODULE_FACTORY);
    }
}
