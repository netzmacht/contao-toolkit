Dependency Injection
====================

Toolkit 3.0 doesn't provide any further going dependency injection tools. The `container-interop`_ implementation is
dropped as `container-interop`_ itself is *deprecated* now in favour of PSR 11.

Have a look at `Symfony container documentation`_ and the `contao-community-alliance/dependency-container`_ to get more
information.

Provided services
-----------------

To get an overview over all provided services use the built in Contao console (managed edition) or it's equivalent in
the standard edition.

.. code-block:: bash

    php vendor/bin/contao-console debug:container

.. _Symfony container documentation: https://symfony.com/doc/current/service_container.html
.. _contao-community-alliance/dependency-container: https://github.com/contao-community-alliance/dependency-container
.. _container-interop: https://github.com/container-interop/container-interop
