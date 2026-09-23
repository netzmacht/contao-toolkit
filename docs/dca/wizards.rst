Wizards and operations
======================

Beside the ready to use :doc:`popup wizard <callbacks>`, Toolkit provides base classes to create own field wizards.


Custom wizards
--------------

``Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\AbstractWizardListener`` is the base class of all wizards. It provides:

* ``render(?string $name = null, array $parameters = [])`` renders the configured template (or a given one) using the
  :doc:`template renderer <../view/templates>`.
* ``getDefinition(DataContainer $dataContainer)`` returns the :doc:`definition <definition>` of the current data
  container.
* The protected properties ``$translator`` and ``$dcaManager``.

The constructor expects the template renderer, the translator, the dca manager and optionally a template name which
overrides the ``$template`` property.

``AbstractPickerListener`` extends it and uses the template ``@NetzmachtContaoToolkit/backend/wizard_picker.html.twig``
which renders an icon opening Contao's modal selector. The template expects the parameters ``url``, ``id``, ``field``,
``icon``, ``title`` and ``jsTitle``.

``AbstractFieldPickerListener`` implements ``onWizardCallback()`` and passes table name, field name, row id and the
current value to an abstract ``generate()`` method:

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
   use Contao\DataContainer;
   use Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\AbstractFieldPickerListener;

   #[AsCallback(table: 'tl_example', target: 'fields.icon.wizard')]
   final class IconPickerListener extends AbstractFieldPickerListener
   {
       public function __invoke(DataContainer $dataContainer): string
       {
           return $this->onWizardCallback($dataContainer);
       }

       public function generate(string $tableName, string $fieldName, int $rowId, mixed $value): string
       {
           $title = $this->translator->trans('tl_example.icon_picker', [], 'contao_tl_example');

           return $this->render(
               null,
               [
                   // Url of your own picker view
                   'url'     => '/contao/example-icon-picker?value=' . rawurlencode((string) $value),
                   'id'      => $fieldName,
                   'field'   => $fieldName,
                   'icon'    => 'pickfile.svg',
                   'title'   => $title,
                   'jsTitle' => $title,
               ],
           );
       }
   }

.. note:: Custom wizards are not registered automatically by the ``toolkit`` configuration section. Register them
   as callback yourself, see :doc:`auto-callbacks`.


Operation data attribute
------------------------

Contao's button callbacks don't know which operation they are rendering. To identify an operation in the backend
(e.g. for JavaScript or tests), Toolkit adds a ``data-operation="<name>"`` attribute to every operation which has a
``toolkit`` section in its configuration. Existing ``attributes`` are kept.

.. code-block:: php

   <?php

   $GLOBALS['TL_DCA']['tl_example']['list']['operations']['toggle'] = [
       'href'       => 'act=toggle&amp;field=published',
       'icon'       => 'visible.svg',
       'attributes' => 'class="toggle"',
       'toolkit'    => [],
   ];

   // Results in 'class="toggle" data-operation="toggle"'

The attribute is only added for Contao requests (backend or frontend) by the ``loadDataContainer`` hook.
