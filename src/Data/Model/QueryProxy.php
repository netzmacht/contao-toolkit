<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Model;

/**
 * Class QueryProxy is designed as extension for the ContaoRepository to delegate all method calls to the model class.
 */
trait QueryProxy
{
    /**
     * Call a method.
     *
     * @param string      $name      Method name.
     * @param list<mixed> $arguments Arguments.
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->call($name, $arguments);
    }
}
