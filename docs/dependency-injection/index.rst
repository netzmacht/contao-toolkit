Dependency Injection
====================

The container
-------------

Even though Toolkit currently is for Contao 3.5 only it's designed to work with Contao 4.x as well. Since Contao 4.x uses
Symfony there is already an container implementation. Contao 3.5 lacks of a dependency container support that's why
`contao-community-alliance/dependency-container`_ is used which based on `Pimple`_.

To overcome the API differences between both implementations Toolkit container bases on `container-interop`_.


ContainerAware
--------------

An easy way to access the container is to use the provided `ContainerAware`_ trait. In general you should avoid to use
this trait plugin and use dependency injection there possible instead of using the container in a service locator style!


Provided services
-----------------

Toolkit provides access to a set of list Contao services/singletons and to the services being shipped with Toolkit. For
an easy access to a service there is a `Services`_ class providing constants for the service names.

.. code-block:: php

    use Netzmacht\Contao\Toolkit\DependencyInjection\Services;
    use Netzmacht\Contao\Toolkit\DependencyInjection\ContainerAware;

    class MyHookListener
    {
        use Netzmacht\Contao\Toolkit\DependencyInjection\ContainerAware;

        private $database;

        public function __construct()
        {
            $this->database = $this->getContainer()->get(Services::DATABASE_CONNECTION);
        }
    }

Toolkit provides

.. _contao-community-alliance/dependency-container: https://github.com/contao-community-alliance/dependency-container
.. _container-interop: https://github.com/container-interop/container-interop
.. _Pimple: https://github.com/silexphp/Pimple/tree/1.1
.. _ContainerAware: https://github.com/netzmacht/contao-toolkit/blob/develop/src/DependencyInjection/ContainerAware.php
.. _Services: https://github.com/netzmacht/contao-toolkit/blob/develop/src/DependencyInjection/Services.php
