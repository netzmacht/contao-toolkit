<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Model;

use Contao\Model;
use Netzmacht\Contao\Toolkit\Assertion\Assertion;
use Netzmacht\Contao\Toolkit\Exception\InvalidArgumentException;

/**
 * Class ToolkitRepositoryManager
 *
 * @package Netzmacht\Contao\Toolkit\Data\Model
 */
final class ToolkitRepositoryManager implements RepositoryManager
{
    /**
     * Repositories.
     *
     * @var Repository[]
     */
    private $repositories;

    /**
     * ToolkitRepositoryManager constructor.
     *
     * @param $repositories
     */
    public function __construct(array $repositories)
    {
        Assertion::allImplementsInterface($repositories, Repository::class);

        $this->repositories = $repositories;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException When no repository was registered and not a model class is given.
     */
    public function getRepository(string $modelClass): Repository
    {
        if (isset($this->repositories[$modelClass])) {
            return $this->repositories[$modelClass];
        }

        if (is_subclass_of($modelClass, Model::class, true)) {
            $this->repositories[$modelClass] = new ContaoRepository($modelClass);

            return $this->repositories[$modelClass];
        }

        throw new InvalidArgumentException(
            sprintf(
                'Neighter a repository was registered nor the class "%s" is a model class.',
                $modelClass
            )
        );
    }
}
