Fragment controllers
=====================

``Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractContentElementController`` and
``AbstractFrontendModuleController`` are slim base classes built directly on top of
``Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController`` and
``Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController``. They combine
visibility checks, an optional pre-generate short-circuit, template-data preparation and an
optional post-generate hook into the familiar sequence:

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\ContentModel;
   use Contao\CoreBundle\Twig\FragmentTemplate;
   use Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractContentElementController;
   use Symfony\Component\HttpFoundation\Request;
   use Symfony\Component\HttpFoundation\Response;

   final class TextController extends AbstractContentElementController
   {
       protected function prepareTemplateData(array $data, Request $request, ContentModel $model): array
       {
           $data['text'] = $model->text;

           return $data;
       }
   }

Neither class requires a constructor — additional dependencies are declared via Symfony's
``ServiceSubscriberInterface`` (``getSubscribedServices()``), the same mechanism Contao Core's own
base classes use.

For frontend modules, the backend-preview "wildcard" placeholder is rendered automatically by
Contao Core before ``getResponse()`` is even called — no extra code needed. For content elements,
use the opt-in ``Netzmacht\Contao\Toolkit\Controller\Fragment\RenderBackendWildcardTrait`` (see
:doc:`render-backend-wildcard`) if your element needs the same placeholder.
