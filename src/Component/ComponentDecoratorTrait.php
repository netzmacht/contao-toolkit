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
    public function __set(string $strKey, $varValue): void
    {
        $this->component->set($strKey, $varValue);
    }

    /**
     * {@inheritDoc}
     */
    public function __get(string $strKey)
    {
        return $this->component->get($strKey);
    }

    /**
     * {@inheritDoc}
     */
    public function __isset(string $strKey): bool
    {
        return $this->component->has($strKey);
    }

    /**
     * {@inheritDoc}
     */
    public function getModel(): ?Model
    {
        return $this->component->getModel();
    }

    /**
     * {@inheritDoc}
     */
    public function generate(): string
    {
        return $this->component->generate();
    }

    /**
     * {@inheritDoc}
     */
    protected function compile(): void
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
