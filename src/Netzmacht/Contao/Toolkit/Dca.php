<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit;

use Netzmacht\Contao\Toolkit\Data\AliasGenerator;
use Netzmacht\Contao\Toolkit\Dca\Callback\ColorPickerCallback;
use Netzmacht\Contao\Toolkit\Dca\Callback\GenerateAliasCallback;
use Netzmacht\Contao\Toolkit\Dca\DcaLoader;
use Netzmacht\Contao\Toolkit\Dca\Callback\ToggleIconCallback;

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
     * @return \Netzmacht\Contao\Toolkit\Dca\Callback\ToggleIconCallback
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
     * @param bool   $replaceHex Should the hex '#' be replaced.
     * @param string $icon       Optional custom color picker icon.
     * @param string $alt        Optional color picker alt.
     * @param string $class      Optional color picker class.
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

    /**
     * Create a get templates callback.
     *
     * @param string $prefix  The template prefix.
     * @param array  $exclude Exclude following templates.
     *
     * @return callable
     */
    public static function createGetTemplatesCallback($prefix = '', array $exclude = array())
    {
        return function () use ($prefix, $exclude) {
            return array_diff(\Controller::getTemplateGroup($prefix), $exclude);
        };
    }
}
