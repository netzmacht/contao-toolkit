Installation
============

Requirements
------------

* PHP 8.3 or higher
* Contao 5.7 or Contao 6
* Symfony 6.4, 7.4 or 8


Install
-------

Install the toolkit using Composer:

.. code-block:: bash

   $ composer require netzmacht/contao-toolkit:^5.0

The bundle ships a Contao Manager plugin, so it is registered automatically in a Contao managed edition. It is loaded
after the ``ContaoCoreBundle``.

If you don't use the managed edition, register the bundle in your kernel:

.. code-block:: php

   <?php

   // config/bundles.php
   return [
       // ...
       Netzmacht\Contao\Toolkit\NetzmachtContaoToolkitBundle::class => ['all' => true],
   ];


Using the services
------------------

All services of the toolkit are private and are registered with service ids like
``netzmacht.contao_toolkit.dca.manager``. Toolkit does not register autowiring aliases for its interfaces, so you have
to reference the service id explicitly when injecting a service:

.. code-block:: yaml

   # config/services.yaml
   services:
       App\ExampleService:
           arguments:
               $dcaManager: '@netzmacht.contao_toolkit.dca.manager'

See :doc:`reference/services` for a list of all services and tags.


Upgrade
-------

Toolkit 5.0 removes several components in favour of Contao's native features. If you upgrade from an older version,
please read the `upgrade guide`_ for the removed classes and their replacements. All changes are listed in the
`changelog`_.

.. _upgrade guide: https://github.com/netzmacht/contao-toolkit/blob/develop/UPGRADE-5.0.md
.. _changelog: https://github.com/netzmacht/contao-toolkit/blob/develop/CHANGELOG.md
