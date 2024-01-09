<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca;

use Netzmacht\Contao\Toolkit\Assertion\Assertion;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Formatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\FormatterFactory;

/**
 * Data container definition manager.
 */
final class Manager implements DcaManager
{
    /**
     * Data definition cache.
     *
     * @var Definition[]
     */
    private array $definitions = [];

    /**
     * Data definition formatter cache.
     *
     * @var Formatter[]
     */
    private array $formatter = [];

    /**
     * The data definition array loader.
     */
    private DcaLoader $loader;

    /**
     * FormatterFactory.
     */
    private FormatterFactory $formatterFactory;

    /**
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
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getDefinition(string $name, bool $noCache = false): Definition
    {
        if ($noCache) {
            $this->loader->loadLanguageFile($name, null, $noCache);
            /** @psalm-suppress TooManyArguments - Contao 5 removed 2nd argument */
            $this->loader->loadDataContainer($name, $noCache);

            $this->assertValidDca($name);

            return new Definition($name, $GLOBALS['TL_DCA'][$name]);
        }

        if (! isset($this->definitions[$name])) {
            $this->loader->loadLanguageFile($name);
            $this->loader->loadDataContainer($name);

            $this->assertValidDca($name);

            $this->definitions[$name] = new Definition($name, $GLOBALS['TL_DCA'][$name]);
        }

        return $this->definitions[$name];
    }

    /**
     * Get a formatter for a definition.
     *
     * @param string $name Definition or name.
     */
    public function getFormatter(string $name): Formatter
    {
        if (! isset($this->formatter[$name])) {
            $definition             = $this->getDefinition($name);
            $this->formatter[$name] = $this->formatterFactory->createFormatterFor($definition);
        }

        return $this->formatter[$name];
    }

    /**
     * Assert that a valid dca is loaded.
     *
     * @param string $name Dca name.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function assertValidDca(string $name): void
    {
        Assertion::keyExists($GLOBALS['TL_DCA'], $name);
        Assertion::isArray($GLOBALS['TL_DCA'][$name]);
    }
}
