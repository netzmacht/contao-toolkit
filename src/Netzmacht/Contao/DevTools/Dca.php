<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\DevTools;

use Netzmacht\Contao\DevTools\Data\AliasGenerator;
use Netzmacht\Contao\DevTools\Dca\Callback\ColorPickerCallback;
use Netzmacht\Contao\DevTools\Dca\Callback\GenerateAliasCallback;
use Netzmacht\Contao\DevTools\Dca\DcaLoader;
use Netzmacht\Contao\DevTools\Dca\Callback\ToggleIconCallback;

/**
 * Class Dca simplifies DCA access.
 *
 * @package Netzmacht\Contao\DevTools
 */
class Dca
{
    use ServiceContainerTrait;

    /**
     * Load the data container.
     *
     * @param string $name        The data container name.
     * @param bool   $ignoreCache Ignore the Contao cache.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function &load($name, $ignoreCache = false)
    {
        $loader = new DcaLoader();
        $loader->loadDataContainer($name, $ignoreCache);

        return $GLOBALS['TL_DCA'][$name];
    }

    /**
     * Create an toggle icon callback.
     *
     * @param string      $table        The table name.
     * @param string      $stateColumn  The column name.
     * @param bool        $inversed     State is inversed.
     * @param string|null $disabledIcon Custom disabled icon.
     *
     * @return \Netzmacht\Contao\DevTools\Dca\Callback\ToggleIconCallback
     */
    public static function createToggleIconCallback($table, $stateColumn, $inversed = false, $disabledIcon = null)
    {
        return new ToggleIconCallback(
            static::getService('user'),
            static::getService('input'),
            static::getService('database.connection'),
            $table,
            $stateColumn,
            $inversed,
            $disabledIcon
        );
    }

    /**
     * Create a color picker callback.
     *
     * The callback
     *
     * @param bool $replaceHex
     * @param null $icon
     * @param null $alt
     * @param null $class
     *
     * @return ColorPickerCallback
     */
    public static function createColorPickerCallback($replaceHex = false, $icon = null, $alt = null, $class = null)
    {
        return new ColorPickerCallback($replaceHex, $icon, $alt, $class);
    }

    /**
     * Create Generate alias callback.
     *
     * @param string       $tableName   The table name.
     * @param array|string $valueColumn The value columns.
     * @param string       $aliasColumn The alias column.
     * @param null         $strategy    Alias generator strategy flags.
     *
     * @return GenerateAliasCallback
     */
    public static function createGenerateAliasCallback(
        $tableName,
        $valueColumn,
        $aliasColumn = 'alias',
        $strategy = null
    ) {
        $database  = static::getService('database.connection');
        $generator = new AliasGenerator($database, $tableName, $aliasColumn, (array) $valueColumn);

        if ($strategy) {
            $generator->setStrategy($strategy);
        }

        return new GenerateAliasCallback($generator);
    }
}
