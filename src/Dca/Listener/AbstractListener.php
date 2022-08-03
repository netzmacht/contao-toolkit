<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener;

use Netzmacht\Contao\Toolkit\Assertion\Assert;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Formatter;

/**
 * Base class for data container callback classes.
 */
abstract class AbstractListener
{
    /**
     * Name of the data container.
     */
    protected static string $name;

    /**
     * Data container manager.
     */
    private DcaManager $dcaManager;

    /**
     * @param DcaManager $dcaManager Data container manager.
     */
    public function __construct(DcaManager $dcaManager)
    {
        $this->dcaManager = $dcaManager;

        Assert::that(static::$name)
            ->notEmpty('Name property must not be empty')
            ->string('Name property must be a string.');
    }

    /**
     * Get data container name.
     */
    public static function getName(): string
    {
        return static::$name;
    }

    /**
     * Get a definition.
     *
     * @param string $name Data definition name. If empty the default name is used.
     */
    protected function getDefinition(string $name = ''): Definition
    {
        return $this->dcaManager->getDefinition($name ?: static::getName());
    }

    /**
     * Get a formatter.
     *
     * @param string $name Data definition name. If empty the default name is used.
     */
    protected function getFormatter(string $name = ''): Formatter
    {
        return $this->dcaManager->getFormatter($name ?: static::getName());
    }
}
