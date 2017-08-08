<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Component\ContentElement;

use Contao\Module;
use Netzmacht\Contao\Toolkit\Component\ComponentDecoratorTrait;

/**
 * Class ModuleDecorator.
 *
 * @package Netzmacht\Contao\Toolkit\Component\ContentElement
 */
final class ModuleDecorator extends Module
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
        return $this->getContainer()->get('netzmacht.contao_toolkit.component.frontend_module_factory');
    }
}
