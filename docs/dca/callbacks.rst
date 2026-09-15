Callbacks
=========

Data definition containers are driven by callbacks which extends the configuration, provides options and handles
actions. There are some useful tools which are provided by Toolkit.


Developing own callbacks
------------------------

If you want to create a callback class for your data container *tl_example* Toolkit provides an abstract listener
containing the Data container manager.

.. code-block:: php

   <?php

    // ExampleCallbacks.php
    class ExampleCallbacks extends Netzmacht\Contao\Toolkit\Dca\Listener\AbstractListener
    {
        public static function getName(): string
        {
            return 'tl_example';
        }

        public function onSubmit()
        {
        }
    }

The base `Callbacks` class provides even more helpers:

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


.. _callbacks-alias:

Alias generator callback
~~~~~~~~~~~~~~~~~~~~~~~~

.. important::

   `GenerateAliasListener` (and the filter-/factory-based alias generator it drives) is deprecated
   as of 4.1.0 and will be removed in 5.0.0. Use `SlugAliasListener` below instead, which is
   built directly on Contao's native `contao.slug` service.

`SlugAliasListener` uses the :doc:`../data/alias` to create an alias callback based on Contao's own
`contao.slug` service. The `fields` configuration is required.

.. code-block:: php

   <?php

    $GLOBALS['TL_DCA']['tl_example']['fields']['alias']['save_callback'][] = [
        Netzmacht\Contao\Toolkit\Dca\Listener\Save\SlugAliasListener::class,
        'onSaveCallback'
    ];

    $GLOBALS['TL_DCA']['tl_example']['fields']['alias']['toolkit']['alias_generator'] = [
        'fields' => ['title'],
        'unique_key_fields' => [],
        'allow_empty' => false,
    ];

For more details please have a look at the `SlugAliasListener`_.


State button callback
~~~~~~~~~~~~~~~~~~~~~

.. important::

   This listener is deprecated as of 4.1.0 and will be removed in 5.0.0.

The state button callback is used to generate the state toggle button to toggle the active state of an entry. The
`stateColumn` configuration is required.

.. code-block:: php

   <?php

    $GLOBALS['TL_DCA']['tl_example']['list']['operations']['toggle']['button_callback'][] = [
        Netzmacht\Contao\Toolkit\Dca\Listener\Button\SaveButtonCallbackListener::class,
        'onButtonCallback'
    ];

    $GLOBALS['TL_DCA']['tl_example']['list']['operations']['toggle']['toolkit']['state_button'] = [
        'disabledIcon' => 'custom-invisible-icon.png,
        'stateColumn'  => 'published',
        'inverse'      => false
    ];

For more details please have a look at the `StateButtonCallbackListener`_.


Color picker wizard
~~~~~~~~~~~~~~~~~~~

.. important::

   This listener is deprecated as of 4.1.0 and will be removed in 5.0.0.

The color picker wizard provides a wizard to choose a rgb color. Every configuration is optional.

.. code-block:: php

   <?php

    $GLOBALS['TL_DCA']['tl_example']['fields']['color']['wizard'][] = [
        Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\ColorPickerListener::class,
        'onWizardCallback'
    ];

    $GLOBALS['TL_DCA']['tl_example']['fields']['color']['toolkit']['alias_generator'] = [
        'title'      => null,
        'template'   => null,
        'icon'       => null,
        'replaceHex' => null,
    ];

For more details please have a look at the `ColorPickerListener`_ wizard.


File picker wizard
~~~~~~~~~~~~~~~~~~

.. important::

   This listener is deprecated as of 4.1.0 and will be removed in 5.0.0.

The file picker wizard provides a popup wizard to choose a file.

.. code-block:: php

   <?php

    $GLOBALS['TL_DCA']['tl_example']['fields']['file']['wizard'][] = [
        Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\FilePickerListener::class,
        'onWizardCallback'
    ];

For more details please have a look at the `FilePickerListener`_ wizard.


Page picker wizard
~~~~~~~~~~~~~~~~~~

.. important::

   This listener is deprecated as of 4.1.0 and will be removed in 5.0.0.

The page picker wizard provides a popup wizard to choose a page.

.. code-block:: php

   <?php

    $GLOBALS['TL_DCA']['tl_example']['fields']['page']['wizard'][] = [
        Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\PagePickerListener::class,
        'onWizardCallback'
    ];

For more details please have a look at the `PagePickerListener`_ wizard.


Popup wizard
~~~~~~~~~~~~

The popup wizard opens a link in a popup overlay. The `href`, `title` and `icon` configuration is required.

.. code-block:: php

   <?php

    $GLOBALS['TL_DCA']['tl_example']['fields']['article']['wizard'][] = [
        Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\PopupWizardListener::class,
        'onWizardCallback'
    ];

    $GLOBALS['TL_DCA']['tl_example']['fields']['article']['toolkit']['popup_wizard'] = [
        'href'        => null,
        'title'       => null,
        'linkPattern' => null,
        'icon'        => null,
        'always'      => false,
    ];

For more details please have a look at the `PopupWizardListener`_ wizard.


Get templates callback
~~~~~~~~~~~~~~~~~~~~~~

The get templates callback get all available templates.

.. code-block:: php

   <?php

    $GLOBALS['TL_DCA']['tl_example']['fields']['template']['options_callback'] = [
        Netzmacht\Contao\Toolkit\Dca\Listener\Options\TemplateOptionsListener::class,
        'onWizardCallback'
    ];

    $GLOBALS['TL_DCA']['tl_example']['fields']['template']['toolkit']['template_options'] = [
        'prefix' => '',
        'exclude' => null,
    ];

As of 4.1.0, `prefix` also supports modern, namespaced fragment-template identifiers
(e.g. `content_element/text`) in addition to classic prefixes (`ce_`, `mod_`, …) — the listener
automatically uses Contao's `contao.twig.finder_factory` service for the former.

For more details please have a look at the `TemplateOptionsListener`_ wizard.

Invoker
-------

If you want to trigger a callback form your code you don't have to worry about the different supported callback formats.
For this case toolkit provides an invoker which is registered as a service.

.. code-block:: php

   <?php

    /** @var Netzmacht\Contao\Toolkit\Dca\Callback\Invoker $invoker */
    $invoker = $container->get('netzmacht.contao_toolkit.callback_invoker);

    // Invoke the callback and get the return values.
    $options = $invoker->invoke($GLOBALS['TL_DCA']['tl_example']['fields']['template']['options_callback'], [$dc]);

    // Invoke a list of callbacks and define which value should changed after invoking a callback.
    // The last argument indicates that the first argument of the arguments array ($value) should be changed
    $value = $invoker->invokeAll($GLOBALS['TL_DCA']['tl_example']['fields']['save_callback'], [$value, $dc], 0);


.. _SlugAliasListener: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Listener/Save/SlugAliasListener.php
.. _StateButtonCallbackListener: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Callback/Button/StateButtonCallbackListener.php
.. _ColorPickerListener: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Callback/Wizard/ColorPickerListener.php
.. _FilePickerListener: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Callback/Wizard/FilePickerListener.php
.. _PagePickerListener: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Callback/Wizard/PagePickerListener.php
.. _PopupWizardListener: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Callback/Wizard/PopupWizardListener.php
.. _TemplateOptionsListener: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Callback/Wizard/TemplateOptionsListener.php
