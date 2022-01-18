<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Component;

use Contao\Model;

/**
 * The ComponentDecorator trait is designed to provide a decorator for content elements and frontend modules.
 *
 * @deprecated Since 3.5.0 and get removed in 4.0.0
 *
 * @psalm-suppress DeprecatedClass
 * @psalm-suppress DeprecatedInterface
 *
 * @phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
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
     * @param string $strKey
     * @param mixed  $varValue
     *
     * @return void
     */
    public function __set($strKey, $varValue)
    {
        $this->component->set($strKey, $varValue);
    }

    /**
     * @param string $strKey
     *
     * @return mixed
     */
    public function __get($strKey)
    {
        return $this->component->get($strKey);
    }

    /**
     * @param string $strKey
     *
     * @return bool
     */
    public function __isset($strKey)
    {
        return $this->component->has($strKey);
    }

    /**
     * @return Model|null
     *
     * @psalm-suppress ImplementedReturnTypeMismatch
     */
    public function getModel()
    {
        return $this->component->getModel();
    }

    /**
     * @return string
     */
    public function generate()
    {
        return $this->component->generate();
    }

    /**
     * @return void
     */
    protected function compile()
    {
        // Do nothing.
    }

    /**
     * Get the content element factory.
     *
     * @psalm-suppress DeprecatedClass
     */
    abstract protected function getFactory(): ComponentFactory;
}
