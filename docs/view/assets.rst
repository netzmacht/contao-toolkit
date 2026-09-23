Asset Management
================

Contao uses the superglobals :code:`$GLOBALS['TL_CSS']`, :code:`$GLOBALS['TL_JAVASCRIPT']`,
:code:`$GLOBALS['TL_HEAD']` and :code:`$GLOBALS['TL_BODY']` to register required assets on the fly. Toolkit provides a
simple wrapper API described by the ``Netzmacht\Contao\Toolkit\View\Assets\AssetsManager`` interface. The benefit of the
wrapper is that you can change the implementation.

For instance, then creating an ajax response the assets could get easily collected and returned as json array.

The default implementation ``GlobalsAssetsManager`` is provided as service ``netzmacht.contao_toolkit.assets_manager``.

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;

   final class ExampleController
   {
       public function __construct(private readonly AssetsManager $assetsManager)
       {
       }

       public function __invoke(): void
       {
           $this->assetsManager
               ->addJavascript('files/js/app.js')
               ->addStylesheet('files/css/app.css');
       }
   }

.. code-block:: yaml

   # config/services.yaml
   services:
       App\Controller\ExampleController:
           arguments:
               - '@netzmacht.contao_toolkit.assets_manager'


Stylesheets and javascripts
---------------------------

.. code-block:: php

   <?php

   // Set media type
   $assetsManager->addStylesheet('files/css/print.css', 'print');

   // Set media type and force the static flag
   $assetsManager->addStylesheet('files/css/print.css', 'print', true);

   // Set media type, static flag and an unique name. Registering an asset with the same name again replaces it.
   $assetsManager->addStylesheet('files/css/print.css', 'print', true, 'project-print-css');

   // Javascript files accept the static flag and a name
   $assetsManager->addJavascript('files/js/app.js', true, 'project-app-js');

   // Register multiple files at once. String keys are used as asset names, numeric keys are unnamed.
   // Registers "vendor" and "app"
   $assetsManager->addJavascripts(['vendor' => 'files/js/vendor.js', 'app' => 'files/js/app.js']);

   // The name is used as prefix for each asset. Registers "theme_0" and "theme_1"
   $assetsManager->addStylesheets(['files/css/a.css', 'files/css/b.css'], 'screen', true, 'theme');

Paths can reference a `Symfony asset package`_ by using the ``package::path`` notation. The url is then resolved using
the asset packages:

.. code-block:: php

   <?php

   $assetsManager->addJavascript('my_bundle::js/app.js');


Static assets
~~~~~~~~~~~~~

Toolkit also automates the handling of `static` assets. For debug reasons combining all static assets could be a pain.
To avoid combining all assets when debugging, the assets manager uses the :code:`AssetsManager::STATIC_PRODUCTION`
flag by default. This means that only in production mode the assets get the `static` flag. Pass ``true`` or ``false``
to force the behaviour.

.. hint:: The production mode is detected using the ``kernel.debug`` parameter.


Head and body
-------------

Beside files, you can add HTML to the head or the end of the body:

.. code-block:: php

   <?php

   // Named blocks. Adding a block with the same name again replaces it.
   $assetsManager->addToHead('project-meta', '<meta name="example" content="value">');
   $assetsManager->addToBody('project-init', '<script>initProject();</script>');

   // Unnamed blocks are appended.
   $assetsManager->appendToHead('<link rel="preconnect" href="https://example.org">');
   $assetsManager->appendToBody('<script>console.log("loaded");</script>');


.. _Symfony asset package: https://symfony.com/doc/current/components/asset.html#asset-packages
