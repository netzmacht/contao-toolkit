Templates
=========

Toolkit provides some improvements to the default Contao template. It provides a common interface for templates, supports
template helpers and a simple to use template factory.


Template renderer
----------------

The toolkit provides a template renderer service which wraps the rendering of the Contao templates and twig templates.


.. code-block:: php

   <?php

   declare(strict_types=1);

   $renderer = $container->get('netzmacht.contao_toolkit.template_renderer');
   assert($renderer instanceof \Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer);

   echo $renderer->render('toolkit:be:be_main', ['foo' => 'bar']);
   echo $renderer->render('toolkit:fe:fe_pgae', ['foo' => 'bar']);
   echo $renderer->render('templates/views.html.twig', ['foo' => 'bar']);
   echo $renderer->render('@Bundle/templates/views.html.twig', ['foo' => 'bar']);

Since toolkit supports template helpers creating a template would require to pass all registered helpers to a template.
Using the template renderer makes it easy. You don't have to worry about any helpers.

.. _template-helpers:

Helpers
-------

Some templates requires helpers to improve template development and code quality by reusing helper codes. Instead of
working with singletons or static helpers, Toolkit encourages you to use view helpers. Every object could be registered
as a view helper for your templates.

Default helpers
~~~~~~~~~~~~~~~

Toolkit provides with `assets` and `translator` two helpers by default which are registered to every template. The
assets helper is an instance of `Netzmacht\\Contao\\Toolkit\\View\\Assets\\AssetsManager`. The translator helper is an
instance of `Symfony\\Contracts\\Translation\\TranslatorInterface`.

.. code-block:: php

    <?php

    // Inside of your template
    $this->helper('translator')->trans('MSC.foo', [], 'contao_default');

    $this->helper('assets')->addJavascript('foo/bar.js');


Register helpers
~~~~~~~~~~~~~~~~

Toolkit uses an event driven approach to register template helpers. This approach allows you to register different
helpers for different templates.

.. code-block:: php

    <?php

    // MyCustomTemplateHelperListener.php

    class MyCustomTemplateHelperListener
    {
        public function onGetTemplateHelpers(Netzmacht\Contao\Toolkit\View\Template\Event\GetTemplateHelpersEvent $event)
        {
            if ($event->getTemplateName() === 'foo' && $event->getContentType() === 'bar') {
                $event->addHelper('foo', new FooBarHelper())
            }
        }
    }

.. code-block:: yaml

    // src/Resources/config/listeners.yml in your bundle

    my.custom.template-helpers-listener:
      class: MyCustomTemplateHelperListener
      tags:
        - { name: 'kernel.event_listener', event: 'netzmacht.contao_toolkit.view.get_template_helpers'}

