Repositories
============

Contao uses it's own implementation for Models. Even though it's called models the pattern behind it is rather the
`active record pattern`_. The main issue of this implementation is the hidden access to the database connection.
Implementing rich models and writing tests is really painful.

To overcome this issue I recommend to use the `repository pattern`_. Toolkit provides some tools to ease using
repositories within Contao. I recommend to provide interfaces for each repository you have to create. The real
implementation can be easily switched now.


Repository manager
------------------

The repository manager is designed to provide a simple access to all model repositories. If you want to use a custom
repository for your model, you have to register it. If no repository is registered, a default ``ContaoRepository`` is
returned for every Contao model class. For any other class an ``InvalidArgumentException`` is thrown.


Getting a repository from the manager
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The repository manager implements ``Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager`` and is provided as service
``netzmacht.contao_toolkit.repository_manager``.

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\ContentModel;
   use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;

   final class ExampleService
   {
       public function __construct(private readonly RepositoryManager $repositoryManager)
       {
       }

       public function example(): void
       {
           $repository = $this->repositoryManager->getRepository(ContentModel::class);
           $element    = $repository->find(1);

           // The manager also provides the database connection
           $connection = $this->repositoryManager->getConnection();
       }
   }

.. code-block:: yaml

   # config/services.yaml
   services:
       App\ExampleService:
           arguments:
               - '@netzmacht.contao_toolkit.repository_manager'


Register a custom repository
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You can provide a custom repository by simply tagging a service with the tag *netzmacht.contao_toolkit.repository*. The
service has to implement the :code:`Netzmacht\Contao\Toolkit\Data\Model\Repository` interface. The ``model`` attribute
is required.

.. code-block:: yaml

   # config/services.yaml
   services:
      custom.repository.example_model:
         class: Custom\Model\ExampleRepository
         arguments:
            - 'Custom\Model\ExampleModel'
         tags:
            - { name: 'netzmacht.contao_toolkit.repository', model: 'Custom\Model\ExampleModel' }

If you register a custom repository the model you specified in the model tag is automatically added to Contao's
:code:`$GLOBALS['TL_MODELS']`.

If your repository implements ``Netzmacht\Contao\Toolkit\Data\Model\RepositoryManagerAware`` the repository manager is
injected automatically. Use the ``RepositoryManagerAwareTrait`` to implement the interface. This way a repository can
access other repositories.

.. warning:: You should **never** define repositories for 3rd party models unless you are really sure what you're
   doing.


Default repository
------------------

Toolkit includes a base `Repository`_ interface. It simply provide an interface for Contao common find* and
count* methods as well as ``save`` and ``delete``. Furthermore it introduces Specifications. The implementation of the
interface is `ContaoRepository`_ which simply delegates all methods to the model class.

.. hint:: Instead of implementing the `Repository`_ interface it's recommend to introduce own interfaces describing each
   required method. It doesn't matter if the provides ContaoRepository is used as the implementation as your code only
   trust the interface.

A custom repository usually extends the ``ContaoRepository``:

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\Model\Collection;
   use Netzmacht\Contao\Toolkit\Data\Model\ContaoRepository;

   /** @extends ContaoRepository<ExampleModel> */
   final class ExampleRepository extends ContaoRepository implements ExampleRepositoryInterface
   {
       public function findPublishedByPid(int $pid): Collection|null
       {
           return $this->findBy(['.pid=?', '.published=?'], [$pid, '1'], ['order' => '.sorting']);
       }
   }


Table prefix placeholder
~~~~~~~~~~~~~~~~~~~~~~~~

Column conditions and the ``order`` option of the ``ContaoRepository`` support a placeholder for the table name. A
column starting with a dot (``.pid``) gets prefixed with the table name (``tl_example.pid``). The same applies to a dot
after a space, comma, opening bracket or comparison operator (e.g. ``FIND_IN_SET(?, .groups)``).


QueryProxy
----------

If you want to access all magic finders and callers of your model class from the repository, you can add the
`QueryProxy`_ trait to your repository. The default repository already uses it.

.. code-block:: php

   <?php

   // Delegated to ExampleModel::findByAlias('example')
   $model = $repository->findByAlias('example');


Specification
-------------

Toolkit provides a common `Specification`_ interface implementing the `specification pattern`_. It can be used with the
``ContaoRepository`` to ``findBySpecification`` or ``countBySpecification``. A specification can check a model in
memory (``isSatisfiedBy``) and build the query conditions (``buildQuery``).

If a specification also has to define query options like ``order`` or ``limit``, implement the `QuerySpecification`_
interface. Its ``buildQueryWithOptions`` method is used by ``findBySpecification``. ``countBySpecification`` still
uses ``buildQuery``.

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\Model;
   use Netzmacht\Contao\Toolkit\Data\Model\QuerySpecification;

   final class PublishedSpecification implements QuerySpecification
   {
       public function isSatisfiedBy(Model $model): bool
       {
           return (bool) $model->published;
       }

       public function buildQuery(array &$columns, array &$values): void
       {
           $columns[] = '.published=?';
           $values[]  = '1';
       }

       public function buildQueryWithOptions(array &$columns, array &$values, array &$options): void
       {
           $this->buildQuery($columns, $values);

           $options['order'] = '.sorting';
       }
   }

   $models = $repository->findBySpecification(new PublishedSpecification());
   $count  = $repository->countBySpecification(new PublishedSpecification());


ModelArrayAccess
----------------

``Netzmacht\Contao\Toolkit\Data\Model\ModelArrayAccess`` decorates a Contao model or database result to provide array
access, e.g. to pass it to components expecting an array.

.. code-block:: php

   <?php

   use Netzmacht\Contao\Toolkit\Data\Model\ModelArrayAccess;

   $row = new ModelArrayAccess($model);
   echo $row['title'];


.. _active record pattern: https://en.wikipedia.org/wiki/Active_record_pattern
.. _repository pattern: http://martinfowler.com/eaaCatalog/repository.html
.. _Repository: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Model/Repository.php
.. _ContaoRepository: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Model/ContaoRepository.php
.. _QueryProxy: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Model/QueryProxy.php
.. _Specification: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Model/Specification.php
.. _QuerySpecification: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Model/QuerySpecification.php
.. _specification pattern: https://en.wikipedia.org/wiki/Specification_pattern
