<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Model;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Model;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Toolkit\Assertion\Assertion;
use Netzmacht\Contao\Toolkit\Exception\InvalidArgumentException;

use function is_subclass_of;
use function sprintf;

final class ToolkitRepositoryManager implements RepositoryManager
{
    /**
     * Repositories.
     *
     * @var array<class-string<Model>,Repository>
     */
    private $repositories;

    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * The contao framework.
     *
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @param Connection                            $connection   Database connection.
     * @param array<class-string<Model>,Repository> $repositories List of repositories.
     * @param ContaoFramework                       $framework    Contao framework.
     */
    public function __construct(Connection $connection, array $repositories, ContaoFramework $framework)
    {
        Assertion::allImplementsInterface($repositories, Repository::class);

        foreach ($repositories as $repository) {
            if (! ($repository instanceof RepositoryManagerAware)) {
                continue;
            }

            $repository->setRepositoryManager($this);
        }

        $this->repositories = $repositories;
        $this->connection   = $connection;
        $this->framework    = $framework;
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

        /** @psalm-suppress RedundantConditionGivenDocblockType */
        if (is_subclass_of($modelClass, Model::class, true)) {
            $this->framework->initialize();
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

    public function getConnection(): Connection
    {
        return $this->connection;
    }
}
