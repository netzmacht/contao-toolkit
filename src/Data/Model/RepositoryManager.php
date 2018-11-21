<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Model;

use Doctrine\DBAL\Connection;

/**
 * Class RepositoryManager
 *
 * @package Netzmacht\Contao\Toolkit\Data\Model
 */
interface RepositoryManager
{
    /**
     * Get a repository.
     *
     * @param string $modelClass Model class.
     *
     * @return Repository
     */
    public function getRepository(string $modelClass): Repository;

    /**
     * Get the connection.
     *
     * @return Connection
     */
    public function getConnection(): Connection;
}
