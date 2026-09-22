ContaoServicesFactory
======================

``Netzmacht\Contao\Toolkit\DependencyInjection\ContaoServicesFactory`` provides
``Contao\CoreBundle\Framework\Adapter`` instances for a number of Contao framework classes
(``Backend``, ``Config``, ``Controller``, ``System``, ``Environment``, ``Frontend``, ``Image``,
``Model``, ``Message``, ``Dbafs``, ``Input``), each registered as its own autowireable service
(e.g. ``netzmacht.contao_toolkit.contao.backend_adapter``).

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

.. important::

   ``createBackendUserInstance()``/``createFrontendUserInstance()`` (and the corresponding
   ``netzmacht.contao_toolkit.contao.backend_user``/``...frontend_user`` services) are deprecated
   as of 4.1.0 and will be removed in 5.0.0. Unlike the adapter methods above, they resolve the
   legacy ``Contao\BackendUser::getInstance()``/``Contao\FrontendUser::getInstance()`` singleton.
   Migrate to Symfony's security component instead:

   - For a permission check, use
     ``Symfony\Bundle\SecurityBundle\Security::isGranted($permission, $subject)`` with the
     appropriate ``Contao\CoreBundle\Security\ContaoCorePermissions::*`` constant.
   - For the concrete user object, use ``Symfony\Bundle\SecurityBundle\Security::getUser()``
     (optionally combined with an ``instanceof BackendUser``/``FrontendUser`` check).
