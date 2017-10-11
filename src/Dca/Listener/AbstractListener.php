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

namespace Netzmacht\Contao\Toolkit\Dca\Listener;

use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Formatter;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Webmozart\Assert\Assert;

/**
 * Base class for data container callback classes.
 *
 * @package Netzmacht\Contao\Toolkit\Dca
 */
abstract class AbstractListener
{
    /**
     * Name of the data container.
     *
     * @var string
     */
    protected static $name;

    /**
     * Data container manager.
     *
     * @var Manager
     */
    private $dcaManager;

    /**
     * Callbacks constructor.
     *
     * @param Manager $dcaManager Data container manager.
     */
    public function __construct(Manager $dcaManager)
    {
        $this->dcaManager = $dcaManager;

        Assert::notEmpty(static::$name, 'Name property must not be empty');
        Assert::string(static::$name, 'Name property must be a string.');
    }

    /**
     * Get data container name.
     *
     * @return string
     */
    public static function getName(): string
    {
        return static::$name;
    }

    /**
     * Get a definition.
     *
     * @param string $name Data definition name. If empty the default name is used.
     *
     * @return Definition
     */
    protected function getDefinition(string $name = ''): Definition
    {
        return $this->dcaManager->getDefinition($name ?: static::getName());
    }

    /**
     * Get a formatter.
     *
     * @param string $name Data definition name. If empty the default name is used.
     *
     * @return Formatter
     */
    protected function getFormatter(string $name = ''): Formatter
    {
        return $this->dcaManager->getFormatter($name ?: static::getName());
    }
}
