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
repository for your model, you have to register it. If no repository is registered, a default repository is returned.


Getting a repository from the manager
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

   <?php

   // Provided as service "netzmacht.contao_toolkit.repository_manager"
   $repository = $repositoryManger->getRepository(\Contao\ContentModel::class);


Register a custom repository
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You can provide a custom repository by simply tagging a service with the tag *netzmacht.contao_toolkit.repository*. The
service has to implement the :code:`Netzmacht\Contao\Toolkit\Data\Model\Repository` interface.

.. code-block:: yaml

   // your services.yml
   services:
      custom.repository.example_model:
         class: Custom\Model\ExampleRepository
         tags:
            { name: "netzmacht.contao_toolkit.repository" model: "Custom\Model\ExampleModel" }

If you register a custom repository the model you specified in the model tag is automatically added to Contao's
:code:`$GLOBALS['TL_MODELS']`.

.. warning:: You should **never** define repositories for 3rd party models unless you are not really sure what you're
   doing.

Default repository
------------------

Toolkit includes an base `Repository`_ interface. It simply provide an interface for Contao common find* and
count* methods. Furthermore it introduces Specifications. The implementation of the interface is `ContaoRepository`_
which simply delegates all methods to the model class.

.. hint:: Instead of implementing the `Repository`_ interface it's recommend to introduce own interfaces describing each
   required method. It doesn't matter if the provides ContaoRepository is used as the implementation as your code only
   trust the interface.


QueryProxy
----------

If you want to access all magic finders and callers of your model class from the repository, you can add the
`QueryProxy`_ trait to your repository. The default repository already uses it.


Specification
-------------

Toolkit provides a common `Specification`_ interface implementing the `specification pattern`_. It can be used with the
`ContaoRepository` to `findBySpecification` or `countBySpecification`.


.. _active record pattern: https://en.wikipedia.org/wiki/Active_record_pattern
.. _repository pattern: http://martinfowler.com/eaaCatalog/repository.html
.. _Repository: https://github.com/netzmacht/contao-toolkit/blob/master/src/Data/Model/Repository.php
.. _ContaoRepository: https://github.com/netzmacht/contao-toolkit/blob/master/src/Data/Model/ContaoRepository.php
.. _QueryProxy: https://github.com/netzmacht/contao-toolkit/blob/master/src/Data/Model/QueryProxy.php
.. _Specification: https://github.com/netzmacht/contao-toolkit/blob/master/src/Data/Model/Specification.php
.. _specification pattern: https://en.wikipedia.org/wiki/Specification_pattern
