Templates
=========

Toolkit provides a small ``Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer`` interface to render templates.
The default implementation ``DelegatingTemplateRenderer`` delegates rendering to Twig. It is provided as service
``netzmacht.contao_toolkit.template_renderer``.

Depending on the interface instead of Twig directly makes it easy to replace the renderer in tests.

Template renderer
-----------------

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;

   final class ExampleService
   {
       public function __construct(private readonly TemplateRenderer $renderer)
       {
       }

       public function render(): string
       {
           return $this->renderer->render('@Contao/example/view.html.twig', ['foo' => 'bar']);
       }
   }

.. code-block:: yaml

   # config/services.yaml
   services:
       App\ExampleService:
           arguments:
               - '@netzmacht.contao_toolkit.template_renderer'

The template name is passed to Twig as is, so every namespace known to Twig (e.g. ``@Contao``, ``@MyBundle``) can be
used.
