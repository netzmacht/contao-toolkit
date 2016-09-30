Templates
=========

Toolkit provides some improvements to the default Contao template. It provides a common interface for templates, supports
template helpers and a simple to use template factory.


Template factory
----------------

Since toolkit supports template helpers creating a template would require to pass all registered helpers to a template.
Using the template factory makes it easy. You don't have to worry about any helpers.

Instead of magic properties the template interface provides `set` and `get` methods.

.. code-block:: php

   <?php

   use Netzmacht\Contao\Toolkit\DependencyInjection\Services;

   /** @var Netzmacht\Contao\Toolkit\View\Template\TemplateFactory $templateFactory */
   $templateFactory = $container->get(Services::TEMPLATE_FACTORY);

   /** @var Netzmacht\Contao\Toolkit\View\Template\Template $frontendTemplate */
   $frontendTemplate = $templateFactory->createFrontendTemplate('mod_test');
   echo $frontendTemplate->parse();

   /** @var Netzmacht\Contao\Toolkit\View\Template\Template $frontendTemplate */
   $backendTemplate = $templateFactory->createBackendTemplate('be_test');
   echo $backendTemplate->parse();

.. hint:: Internal the Contao classes BackendTemplate and FrontendTemplate are used. So inside your templates you can
   access all methods and magic properties as you are used to.


Helpers
-------

Some templates requires helpers to improve template development and code quality by reusing helper codes. Instead of
working with singletons or static helpers, Toolkit encourages you to use view helpers. Every object could be registered
as a view helper for your templates.

Default helpers
~~~~~~~~~~~~~~~

Toolkit provides with `assets` and `translator` two helpers by default which are registered to every template. The
assets helper is an instance of `Netzmacht\\Contao\\Toolkit\\View\\Assets\\AssetsManager`. The translator helper is an
instance of `ContaoCommunityAlliance\\Translator\\TranslatorInterface`.

.. code-block:: php

    <?php

    // Inside of your template
    $this->helper('translator')->translate('foo', 'bar');

    $this->helper('assets')->addJavascript('foo/bar.js');


Register helpers
~~~~~~~~~~~~~~~~

Toolkit uses an event driven approach to register template helpers. You can register different helpers for different
templates.

.. code-block:: php

    <?php

    // config/event_listeners.php
    return [
        Netzmacht\Contao\Toolkit\View\Template\Event\GetTemplateHelpersEvent::NAME => [
            function (Netzmacht\Contao\Toolkit\View\Template\Event\GetTemplateHelpersEvent $event) {
                if ($event->getTemplateName() === 'foo' && $event->getContentType() === 'bar') {
                    $event->addHelper('foo', new FooBarHelper())
                }
            }
        ]
    ];

