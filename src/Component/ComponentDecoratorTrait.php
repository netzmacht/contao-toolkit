<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Component;

use Contao\Model;

/**
 * The ComponentDecorator trait is designed to provide a decorator for content elements and frontend modules.
 *
 * @package Netzmacht\Contao\Toolkit\Component
 */
trait ComponentDecoratorTrait
{
    /**
     * Inner component.
     *
     * @var Component
     */
    protected $component;

    /**
     * {@inheritDoc}
     */
    public function __set($strKey, $varValue)
    {
        $this->component->set($strKey, $varValue);
    }

    /**
     * {@inheritDoc}
     */
    public function __get($strKey)
    {
        return $this->component->get($strKey);
    }

    /**
     * {@inheritDoc}
     */
    public function __isset($strKey)
    {
        return $this->component->has($strKey);
    }

    /**
     * {@inheritDoc}
     */
    public function getModel()
    {
        return $this->component->getModel();
    }

    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        return $this->component->generate();
    }

    /**
     * {@inheritDoc}
     */
    protected function compile()
    {
        // Do nothing.
    }

    /**
     * Get the content element factory.
     *
     * @return ComponentFactory
     */
    abstract protected function getFactory(): ComponentFactory;
}
