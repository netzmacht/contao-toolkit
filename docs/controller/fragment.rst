Fragment controllers
=====================

``Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractContentElementController`` and
``AbstractFrontendModuleController`` are slim base classes built directly on top of
``Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController`` and
``Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController``. They implement ``getResponse()`` and
split it into small hooks you can override.


Lifecycle
---------

``getResponse()`` is final and runs following steps:

#. **Visibility check** (content elements only): if the element is hidden, an empty response is returned. See
   `Hidden elements`_.
#. ``preGenerate(FragmentTemplate $template, $model, Request $request): ?Response`` — return a response to
   short-circuit the rendering, e.g. a redirect or a file download.
#. ``prepareTemplateData(array $data, Request $request, $model): array`` — modify and return the template data.
#. The template is rendered.
#. ``postGenerate(Response $response, FragmentTemplate $template, $model, Request $request): ?Response`` — return a
   response to replace the rendered one, e.g. to add cache headers, or ``null`` to keep it.

All hooks are optional. ``$model`` is a ``ContentModel`` or a ``ModuleModel``.


Content elements
----------------

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\ContentModel;
   use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
   use Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractContentElementController;
   use Symfony\Component\HttpFoundation\Request;

   #[AsContentElement(category: 'texts')]
   final class TextController extends AbstractContentElementController
   {
       protected function prepareTemplateData(array $data, Request $request, ContentModel $model): array
       {
           $data['text'] = $model->text;

           return $data;
       }
   }


Frontend modules
----------------

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
   use Contao\CoreBundle\Twig\FragmentTemplate;
   use Contao\ModuleModel;
   use Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractFrontendModuleController;
   use Symfony\Component\HttpFoundation\RedirectResponse;
   use Symfony\Component\HttpFoundation\Request;
   use Symfony\Component\HttpFoundation\Response;

   #[AsFrontendModule(category: 'miscellaneous')]
   final class ExampleModuleController extends AbstractFrontendModuleController
   {
       protected function preGenerate(FragmentTemplate $template, ModuleModel $model, Request $request): Response|null
       {
           if ($request->query->has('legacy')) {
               return new RedirectResponse('/new-location');
           }

           return null;
       }

       protected function postGenerate(
           Response $response,
           FragmentTemplate $template,
           ModuleModel $model,
           Request $request,
       ): Response|null {
           $response->setPrivate();

           return $response;
       }
   }


Dependencies
------------

Neither class requires a constructor — additional dependencies are declared via Symfony's
``ServiceSubscriberInterface`` (``getSubscribedServices()``), the same mechanism Contao Core's own
base classes use. Constructor injection works as well.


Hidden elements
---------------

``AbstractContentElementController`` uses the ``Netzmacht\Contao\Toolkit\Controller\Fragment\IsHiddenTrait``. A
content element is considered hidden if it is ``invisible`` or outside its ``start``/``stop`` period. Hidden elements
are still rendered

* in the backend scope and
* in the frontend preview if a backend user is logged in.

The trait subscribes the ``token_checker`` service. You can use it in your own controllers as well.


Backend wildcard
----------------

For frontend modules, the backend-preview "wildcard" placeholder is rendered automatically by
Contao Core before ``getResponse()`` is even called — no extra code needed. For content elements,
use the opt-in ``Netzmacht\Contao\Toolkit\Controller\Fragment\RenderBackendWildcardTrait`` (see
:doc:`render-backend-wildcard`) if your element needs the same placeholder.
