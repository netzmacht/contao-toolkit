<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Formatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\FormatterFactory;
use Webmozart\Assert\Assert;

/**
 * Data container definition manager.
 *
 * @package Netzmacht\Contao\Toolkit\Dca
 */
final class Manager
{
    /**
     * Data definition cache.
     *
     * @var Definition[]
     */
    private $definitions = array();

    /**
     * Data definition formatter cache.
     *
     * @var Formatter[]
     */
    private $formatter = array();

    /**
     * The data definition array loader.
     *
     * @var DcaLoader
     */
    private $loader;

    /**
     * FormatterFactory.
     *
     * @var FormatterFactory
     */
    private $formatterFactory;

    /**
     * Manager constructor.
     *
     * @param DcaLoader        $loader           The data definition array loader.
     * @param FormatterFactory $formatterFactory Formatter factory.
     */
    public function __construct(DcaLoader $loader, FormatterFactory $formatterFactory)
    {
        $this->loader           = $loader;
        $this->formatterFactory = $formatterFactory;
    }

    /**
     * Get a data container definition.
     *
     * @param string $name    The data definition name.
     * @param bool   $noCache If true not the cached version is loaded.
     *
     * @return Definition
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getDefinition($name, $noCache = false)
    {
        if ($noCache) {
            $this->loader->loadDataContainer($name, $noCache);

            $this->assertValidDca($name);

            return new Definition($name, $GLOBALS['TL_DCA'][$name]);
        }

        if (!isset($this->definitions[$name])) {
            $this->loader->loadDataContainer($name);

            $this->definitions[$name] = new Definition($name, $GLOBALS['TL_DCA'][$name]);
        }

        return $this->definitions[$name];
    }

    /**
     * Get a formatter for a definition.
     *
     * @param Definition|string $definition Definition or name.
     *
     * @return Formatter
     */
    public function getFormatter($definition)
    {
        if (!$definition instanceof Definition) {
            if (isset($this->formatter[$definition])) {
                return $this->formatter[$definition];
            }

            $definition = $this->getDefinition($definition);
        }

        if (!isset($this->formatter[$definition->getName()])) {
            $this->formatter[$definition->getName()] = $this->formatterFactory->createFormatterFor($definition);
        }

        return $this->formatter[$definition->getName()];
    }

    /**
     * Assert that an valid dca is loaded.
     *
     * @param string $name Dca name.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function assertValidDca($name)
    {
        Assert::keyExists($GLOBALS['TL_DCA'], $name);
        Assert::isArray($GLOBALS['TL_DCA'][$name]);
    }
}
