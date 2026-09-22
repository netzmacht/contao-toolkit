Templates
=========

Toolkit provides a template renderer service that delegates rendering to Twig.

Template renderer
------------------

.. code-block:: php

   <?php

   declare(strict_types=1);

   $renderer = $container->get('netzmacht.contao_toolkit.template_renderer');
   assert($renderer instanceof \Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer);

   echo $renderer->render('templates/views.html.twig', ['foo' => 'bar']);
   echo $renderer->render('@Bundle/templates/views.html.twig', ['foo' => 'bar']);
