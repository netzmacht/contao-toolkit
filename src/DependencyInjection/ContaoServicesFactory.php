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
use Contao\CoreBundle\Framework\ContaoFrameworkInterface as ContaoFramework;
use Contao\Encryption;

/**
 * Class ContaoServicesFactory.
 *
 * @package Netzmacht\Contao\Toolkit\DependencyInjection
 */
class ContaoServicesFactory
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
     * Create the config service.
     *
     * Do not create the adapter as we want to rely on the interface.
     *
     * @return Config
     */
    public function createConfigService(): Config
    {
        $this->framework->initialize();

        return Config::getInstance();
    }

    /**
     * Create the encryption service.
     *
     * Do not create the adapter as we want to rely on the interface.
     *
     * @return Encryption
     */
    public function createEncryptionService(): Encryption
    {
        $this->framework->initialize();

        return Encryption::getInstance();
    }
}
