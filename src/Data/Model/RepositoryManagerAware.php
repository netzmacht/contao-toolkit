<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Toolkit\Data\Model;

/**
 * Interface RepositoryManagerAware.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Model
 */
interface RepositoryManagerAware
{
    /**
     * Register the repository manager.
     *
     * @param RepositoryManager $repositoryManager Repository manager.
     *
     * @return void
     */
    public function setRepositoryManager(RepositoryManager $repositoryManager);
}
