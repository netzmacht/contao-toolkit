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
