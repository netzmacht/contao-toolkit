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

namespace Netzmacht\Contao\Toolkit\DependencyInjection;

use Contao\Config;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface as ContaoFramework;
use Contao\Encryption;
use Contao\Environment;
use Contao\Input;

/**
 * Class ContaoServicesFactory.
 *
 * @package Netzmacht\Contao\Toolkit\DependencyInjection
 */
final class ContaoServicesFactory
{
    /**
     * Contao framework.
     *
     * @var ContaoFramework
     */
    private $framework;

    /**
     * ContaoServicesFactory constructor.
     *
     * @param ContaoFramework $framework Contao framework.
     */
    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Create input adapter.
     *
     * @return Adapter|Input
     */
    public function createInputAdapter(): Adapter
    {
        return $this->createAdapter(Input::class);
    }

    /**
     * Create the config adapter.
     *
     * @return Adapter|Config
     */
    public function createConfigAdapter(): Adapter
    {
        return $this->createAdapter(Config::class);
    }

    /**
     * Create an environment adapter.
     *
     * @return Adapter|Environment
     */
    public function createEnvironmentAdapter(): Adapter
    {
        return $this->createAdapter(Environment::class);
    }

    /**
     * Create an environment adapter.
     *
     * @return Adapter|Encryption
     */
    public function createEncryptionAdapter(): Adapter
    {
        return $this->createAdapter(Encryption::class);
    }

    /**
     * Create an adapter for a specific class.
     *
     * @param string $class Class name.
     *
     * @return Adapter
     */
    private function createAdapter(string $class): Adapter
    {
        $this->framework->initialize();

        return $this->framework->getAdapter($class);
    }
}
