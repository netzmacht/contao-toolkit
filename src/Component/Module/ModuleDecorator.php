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

namespace Netzmacht\Contao\Toolkit\Component\Module;

use Contao\Module;
use Netzmacht\Contao\Toolkit\Component\ComponentDecoratorTrait;
use Netzmacht\Contao\Toolkit\Component\ComponentFactory;

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
        $this->component = $this->getFactory()->create($contentModel, $column);
    }

    /**
     * {@inheritDoc}
     */
    protected function getFactory(): ComponentFactory
    {
        return $this->getContainer()->get('netzmacht.contao_toolkit.component.frontend_module_factory');
    }
}
