Asset Management
================

Contao uses the superglobals :code:`$GLOBALS['TL_CSS']` and :code:`$GLOBALS['TL_JAVASCRIPT']` to register required
assets on the fly. Toolkit provides a simple wrapper API to register assets. The benefit of the wrapper is that you
can change the implementation.

For instance, then creating an ajax response the assets could get easily collected and returned as json array.

.. code-block:: php

   <?php

   /** @var Netzmacht\Contao\Toolkit\View\AssetsManager\AssetsManager $assetsManager */
   $assetsManager = $container->get('netzmacht.contao_toolkit.assets_manager');

   // Set media type
   $assetsManager->addStylesheet('files/css/print.css', 'print');

   // Set media type and static flag
   $assetsManager->addStylesheet('files/css/print.css', 'print', 'static');

   // Set media type, static flag and an unique name
   $assetsManager->addStylesheet('files/css/print.css', 'print', 'static', 'project-print-css');


Toolkit also automates the handling of `static` assets. For debug reasons combining all static assets could be a pain.
To avoid combining all assets when debugging a pain, the assets manager uses the :code:`AssetsManager::STATIC_PRODUCTION`
flag by default. This means that only in production mode the assets get the `static` flag.

.. hint:: The production mode is set by the dependency container using the symfony debug environment setting.


Template helper
---------------

The assets manager is also registered as template helper named `assets`. Read the :ref:`template-helpers` section of
the templates documentation.
