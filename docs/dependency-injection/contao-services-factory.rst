ContaoServicesFactory
======================

``Netzmacht\Contao\Toolkit\DependencyInjection\ContaoServicesFactory`` creates ``Contao\CoreBundle\Framework\Adapter``
instances for a number of Contao framework classes. The framework is initialized before the adapter is created.

Following adapters are registered as services:

=================  =================================================
Class              Service id
=================  =================================================
``Backend``        ``netzmacht.contao_toolkit.contao.backend_adapter``
``Config``         ``netzmacht.contao_toolkit.contao.config_adapter``
``Controller``     ``netzmacht.contao_toolkit.contao.controller_adapter``
``Environment``    ``netzmacht.contao_toolkit.contao.environment_adapter``
``Image``          ``netzmacht.contao_toolkit.contao.image_adapter``
``Input``          ``netzmacht.contao_toolkit.contao.input_adapter``
``Message``        ``netzmacht.contao_toolkit.contao.message_adapter``
``Model``          ``netzmacht.contao_toolkit.contao.model_adapter``
``System``         ``netzmacht.contao_toolkit.contao.system_adapter``
=================  =================================================

The factory also provides ``createFrontendAdapter()`` and ``createDbafsAdapter()`` which are not registered as
services. Use the factory service ``netzmacht.contao_toolkit.contao_services_factory`` to define your own service if
you need them.

All adapter services share the same class ``Adapter``, so they can't be autowired by type. Reference the service id
explicitly:

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\Backend;
   use Contao\CoreBundle\Framework\Adapter;

   final class MyService
   {
       /** @param Adapter<Backend> $backendAdapter */
       public function __construct(private readonly Adapter $backendAdapter)
       {
       }
   }

.. code-block:: yaml

   # config/services.yaml
   services:
       App\MyService:
           arguments:
               - '@netzmacht.contao_toolkit.contao.backend_adapter'

       # Register an adapter which is not provided by default
       app.contao.frontend_adapter:
           class: Contao\CoreBundle\Framework\Adapter
           factory: ['@netzmacht.contao_toolkit.contao_services_factory', 'createFrontendAdapter']

.. hint:: In many cases Contao's ``contao.framework`` service (``ContaoFramework::getAdapter()``) is an alternative
   which doesn't require an extra service definition.
