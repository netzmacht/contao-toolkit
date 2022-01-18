<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Component;

use Contao\Model;

/**
 * The component is the interface describing content elements and modules.
 *
 * @deprecated Since 3.5.0 and get removed in 4.0.0
 *
 * phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
 */
interface Component
{
    /**
     * Set the value of a parameter.
     *
     * @param string $name  Name of the parameter.
     * @param mixed  $value Value of the parameter.
     *
     * @return $this
     */
    public function set(string $name, $value): self;

    /**
     * Get the value of the parameter.
     *
     * @param string $name Parameter name.
     *
     * @return mixed
     */
    public function get(string $name);

    /**
     * Check if parameter exists.
     *
     * @param string $name Parameter name.
     */
    public function has(string $name): bool;

    /**
     * Get the assigned model.
     *
     * @return Model|null
     */
    public function getModel();

    /**
     * Generate the component.
     */
    public function generate(): string;
}
