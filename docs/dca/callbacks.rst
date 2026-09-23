Callbacks
=========

Data definition containers are driven by callbacks which extends the configuration, provides options and handles
actions. There are some useful tools which are provided by Toolkit.


Developing own callbacks
------------------------

If you want to create a callback class for your data container *tl_example* Toolkit provides an abstract listener
which gets the data container manager injected.

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
   use Contao\DataContainer;
   use Netzmacht\Contao\Toolkit\Dca\Listener\AbstractListener;

   final class ExampleListener extends AbstractListener
   {
       public static function getName(): string
       {
           return 'tl_example';
       }

       #[AsCallback(table: 'tl_example', target: 'config.onsubmit')]
       public function onSubmit(DataContainer $dataContainer): void
       {
           $label = $this->getFormatter()->formatFieldLabel('title');
       }
   }

The constructor of ``AbstractListener`` expects a ``Netzmacht\Contao\Toolkit\Dca\DcaManager`` which gets autowired.

The ``AbstractListener`` class provides following helpers:

.. glossary::

   getDefinition()
    Gets the definition of the current data container *(tl_example)* See :doc:`definition` section for more details.

   getDefinition('tl_custom')
    Gets the definition of a specific data container. See :doc:`definition` section for more details.

   getFormatter()
    Gets the formatter of the current data container *(tl_example)*. See :doc:`formatter` section for more details.

   getFormatter('tl_custom')
    Gets the formatter of a specific data container. See :doc:`formatter` section for more details.


Provided callbacks
------------------

Toolkit ships three ready to use callbacks. Each of them is configured in the ``toolkit`` section of the field
definition. Defining the configuration is enough — the matching Contao callback is registered automatically, see
:doc:`auto-callbacks`.


.. _callbacks-alias:

Alias generator callback
~~~~~~~~~~~~~~~~~~~~~~~~

``SlugAliasListener`` uses the :doc:`../data/alias` to create an alias based on Contao's own ``contao.slug``
service.

.. code-block:: php

   <?php

   $GLOBALS['TL_DCA']['tl_example']['fields']['alias']['toolkit']['alias_generator'] = [
       // Fields of the record used to generate the alias. Defaults to ['id'].
       'fields'            => ['title'],
       // Additional columns which have to match to consider a value as duplicate (e.g. ['pid']).
       'unique_key_fields' => [],
       // Allow an empty alias.
       'allow_empty'       => false,
   ];

For more details please have a look at the `SlugAliasListener`_.


Popup wizard
~~~~~~~~~~~~

The popup wizard renders a link which opens a backend view in a popup overlay. It is only rendered if the field has a
value unless ``always`` is set.

.. code-block:: php

   <?php

   $GLOBALS['TL_DCA']['tl_example']['fields']['article']['toolkit']['popup_wizard'] = [
       // Query string of the backend url. The current value is passed as "id".
       'href'   => 'do=article&table=tl_content',
       'label'  => 'Edit article',
       'title'  => 'Edit the selected article',
       'icon'   => 'alias.svg',
       // Render the wizard even if the field is empty.
       'always' => false,
   ];

For more details please have a look at the `PopupWizardListener`_.


Template options callback
~~~~~~~~~~~~~~~~~~~~~~~~~

The template options callback provides all available templates for a given prefix.

.. code-block:: php

   <?php

   $GLOBALS['TL_DCA']['tl_example']['fields']['template']['toolkit']['template_options'] = [
       'prefix'  => 'ce_example',
       'exclude' => null,
   ];

``prefix`` supports modern, namespaced fragment-template identifiers (e.g. ``content_element/text``) in addition to
classic prefixes (``ce_``, ``mod_``, …) — the listener automatically uses Contao's ``contao.twig.finder_factory``
service for the former. ``exclude`` accepts a list of template names which should be removed from the options.

For more details please have a look at the `TemplateOptionsListener`_.


Registering the callbacks manually
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You can still register the callbacks explicitly. This is only required if you need to control the position of the
callback within the list of callbacks. See :doc:`auto-callbacks` how manually registered callbacks and the automatic
registration work together.

.. code-block:: php

   <?php

   use Netzmacht\Contao\Toolkit\Dca\Listener\Options\TemplateOptionsListener;
   use Netzmacht\Contao\Toolkit\Dca\Listener\Save\SlugAliasListener;
   use Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\PopupWizardListener;

   $GLOBALS['TL_DCA']['tl_example']['fields']['alias']['save_callback'][] = [
       SlugAliasListener::class,
       'onSaveCallback',
   ];

   $GLOBALS['TL_DCA']['tl_example']['fields']['article']['wizard'][] = [
       PopupWizardListener::class,
       'onWizardCallback',
   ];

   $GLOBALS['TL_DCA']['tl_example']['fields']['template']['options_callback'] = [
       TemplateOptionsListener::class,
       'onOptionsCallback',
   ];


Invoker
-------

If you want to trigger a callback from your code you don't have to worry about the different supported callback
formats. For this case Toolkit provides an invoker which is registered as service
``netzmacht.contao_toolkit.callback_invoker``.

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\DataContainer;
   use Netzmacht\Contao\Toolkit\Callback\Invoker;

   final class ExampleService
   {
       public function __construct(private readonly Invoker $invoker)
       {
       }

       public function example(DataContainer $dc, mixed $value): mixed
       {
           // Invoke the callback and get the return values.
           $options = $this->invoker->invoke(
               $GLOBALS['TL_DCA']['tl_example']['fields']['template']['options_callback'],
               [$dc],
           );

           // Invoke a list of callbacks. The last argument defines which argument is replaced by the return value
           // of each callback - here the first argument ($value).
           return $this->invoker->invokeAll(
               $GLOBALS['TL_DCA']['tl_example']['fields']['alias']['save_callback'],
               [$value, $dc],
               0,
           );
       }
   }


.. _SlugAliasListener: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Listener/Save/SlugAliasListener.php
.. _PopupWizardListener: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Listener/Wizard/PopupWizardListener.php
.. _TemplateOptionsListener: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Listener/Options/TemplateOptionsListener.php
