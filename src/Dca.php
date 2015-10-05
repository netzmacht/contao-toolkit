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
 * @deprecated
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
     * @deprecated Use Netzmacht\Contao\Toolkit\Dca\Manager::get instead.
     */
    public static function &load($name, $ignoreCache = false)
    {
        if (!isset($GLOBALS['TL_DCA'][$name])) {
            $loader = new DcaLoader();
            $loader->loadDataContainer($name, $ignoreCache);
        }

        return $GLOBALS['TL_DCA'][$name];
    }

    /**
     * Get from the dca.
     *
     * @param string       $name        The data container name.
     * @param array|string $path        The path as array or "/" separated string.
     * @param bool|false   $ignoreCache Ignore the contao cache.
     *
     * @return array|null
     * @deprecated Use Netzmacht\Contao\Toolkit\Dca\Definition instead.
     */
    public static function &get($name, $path = null, $ignoreCache = false)
    {
        $dca  = &static::load($name, $ignoreCache);
        $path = is_array($path) ? $path : explode('/', $path);

        foreach ($path as $key) {
            if (!is_array($dca) || !array_key_exists($key, $dca)) {
                return null;
            }

            $dca =& $dca[$key];
        }

        return $dca;
    }

    /**
     * Set a value in the dca.
     *
     * @param string       $name        The data container name.
     * @param array|string $path        The path as array or "/" separated string.
     * @param mixed        $value       The value.
     * @param bool|false   $ignoreCache Ignore the contao cache.
     *
     * @return bool
     * @deprecated Use Netzmacht\Contao\Toolkit\Dca\Definition instead.
     */
    public static function set($name, $path, $value, $ignoreCache = false)
    {
        $dca     =& static::load($name, $ignoreCache);
        $path    = is_array($path) ? $path : explode('/', $path);
        $current =& $dca;

        foreach ($path as $key) {
            if (!is_array($current)) {
                return false;
            }

            if (!isset($current[$key])) {
                $current[$key] = array();
            }

            unset($tmp);
            $tmp =& $current;

            unset($current);
            $current =& $tmp[$key];
        }

        $current = $value;

        return true;
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
            static::getServiceContainer()->getUser(),
            static::getServiceContainer()->getInput(),
            static::getServiceContainer()->getDatabaseConnection(),
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
        $database  = static::getServiceContainer()->getDatabaseConnection();
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

    /**
     * Create an edit target callback.
     *
     * @param string      $href         The href.
     * @param string      $label        The label.
     * @param string      $icon         The icon.
     * @param bool        $always       Always.
     * @param string|null $linkTemplate Optional link template pattern.
     *
     * @return callable
     */
    public static function createPopupWizardCallback($href, $label, $icon, $always = false, $linkTemplate = null)
    {
        return function ($dataContainer) use ($href, $label, $icon, $always, $linkTemplate) {
            if (!$always && !$dataContainer->value) {
                return '';
            }

            if (!$linkTemplate) {
                $linkTemplate = 'contao/main.php?%s&amp;id=%s&amp;popup=1&amp;nb=1&amp;rt=%s';
            }

            $link    = sprintf($linkTemplate, $href, $dataContainer->value, REQUEST_TOKEN);
            $onClick = sprintf(
                'Backend.openModalIframe({\'width\':768,\'title\':\'%s\',\'url\':this.href});return false',
                specialchars(str_replace("'", "\\'", sprintf($label[0], $dataContainer->value)))
            );

            return sprintf(
                '<a href="%s" title="%s" style="padding-left: 3px" onclick="%s">%s</a>',
                $link,
                specialchars(sprintf($label[1], $dataContainer->value)),
                $onClick,
                \Image::getHtml($icon, $label[0], 'style="vertical-align:top"')
            );
        };
    }
}
