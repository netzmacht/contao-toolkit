<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus.
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
final class ModuleDecorator extends \ContentElement
{
    use ComponentDecoratorTrait;

    /**
     * {@inheritDoc}
     */
    public function __construct($contentModel, $column = 'main')
    {
        $factory         = $this->getFactory();
        $this->component = $factory->create($contentModel, $column);
    }

    /**
     * {@inheritDoc}
     */
    protected function getFactory()
    {
        return $this->getContainer()->get(Services::MODULE_FACTORY);
    }
}
