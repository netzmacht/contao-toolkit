RenderBackendWildcardTrait
=============================

``Netzmacht\Contao\Toolkit\Controller\Fragment\RenderBackendWildcardTrait`` renders the classic
"###Wildcard###" backend-editor placeholder for content elements that need one — structural or
side-effecting elements (sliders, accordions, forms) where showing the real frontend markup in
the backend editor would not make sense. Contao Core itself still renders this placeholder for
such elements even in its own modern base classes, just not automatically for custom ones.

It is **not** auto-invoked — call it explicitly from your own ``preGenerate()`` hook:

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\ContentModel;
   use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
   use Contao\CoreBundle\Twig\FragmentTemplate;
   use Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractContentElementController;
   use Netzmacht\Contao\Toolkit\Controller\Fragment\RenderBackendWildcardTrait;
   use Symfony\Component\HttpFoundation\Request;
   use Symfony\Component\HttpFoundation\Response;

   #[AsContentElement(type: 'slider_start', category: 'slider')]
   final class SliderStartController extends AbstractContentElementController
   {
       use RenderBackendWildcardTrait;

       protected function preGenerate(FragmentTemplate $template, ContentModel $model, Request $request): Response|null
       {
           if ($this->isBackendScope($request)) {
               return $this->renderBackendWildcard($model, $request);
           }

           return null;
       }
   }

The wildcard uses the translated name of the element type (``$GLOBALS['TL_LANG']['CTE'][<type>][0]``) and links to
the edit view of the element.

For frontend modules, no equivalent trait is needed:
``Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController::__invoke()``
already renders the backend wildcard automatically before ``getResponse()`` is even called.

.. important::

   The toolkit's ``AbstractContentElementController`` already uses ``IsHiddenTrait``. Using
   ``RenderBackendWildcardTrait`` in a subclass is fine, because both ``getSubscribedServices()`` implementations call
   their parent. If you use both traits in the **same** class (e.g. in a controller extending Contao's base class
   directly), PHP does not merge same-named trait methods automatically — resolve the conflict with ``insteadof`` and
   declare your own ``getSubscribedServices()`` that merges both.
