Callbacks
=========

Data definition containers are driven by callbacks which extends the configuration, provides options and handles
actions. There are some useful tools which are provided by Toolkit.


Developing own callbacks
------------------------

If you want to create a callback class for your data container *tl_example* Toolkit provides a base callback class for
you. The idea behind the callback class is to support dependency injection so that you can create a clean callback
class.

To use the callback simply create the class, register it as a service and use the :code:`callback($methodName)` method
to register the callback in the data container array.

.. code-block:: php

   <?php

    // ExampleCallbacks.php
    class ExampleCallbacks extends Netzmacht\Contao\Toolkit\Dca\Callback\Callbacks
    {
        /**
         * Name of the data container.
         *
         * @var string
         */
        protected static $name = 'tl_example';

        /**
         * Name of the callback service. If not set, the default name would be contao.dca.tl_example
         *
         * @var string
         */
        protected static $serviceName = 'custom.dca.example-callbacks';

        public function onSubmit()
        {
        }
    }

    // config/services.php
    $GLOBALS['container']['custom.dca.example-callbacks'] = $GLOBALS['container']->share(
        function ($container) {
            return new ExampleCallbacks(
                $container[Netzmacht\Contao\Toolkit\DependencyInjection\Services::DCA_MANAGER]
            );
        }
    );

    // dca/tl_example.php
    $GLOBALS['TL_DCA']['tl_example']['config']['onsubmit_callback'][] = ExampleCallbacks::callback('onSubmit');


As you can see in the example above you can't register the callback as usual using the array syntax of class name and
method name. The `callback` method is a shortcut which accesses the registered service and calls the callback when
triggered.

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


Callback factory
----------------

Toolkit provides a set of often required callbacks. To simplify callback creation a callback factory is shipped. It
allows to register any service as callback as well.

.. code-block:: php

   <?php

    // dca/tl_example.php
    $GLOBALS['TL_DCA']['tl_example']['config']['onsubmit_callback'][] = Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::service(
        'service.name',
        'methodName'
    );

The default callbacks are explained below.


Provided callbacks
------------------


.. _callbacks-alias:

Alias generator callback
~~~~~~~~~~~~~~~~~~~~~~~~

The alias generator uses the :doc:`../data/alias` to create an alias callback. By default a predefined alias generator
is used. You are able to pass a custom factory as well.

.. code-block:: php

   <?php

    $GLOBALS['TL_DCA']['tl_example']['fields']['alias']['save_callback'][] = Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::aliasGenerator(
        'tl_example',
        'alias',
        ['title']
    );

For more details please have a look at the `GenerateAliasCallback`_.


State button callback
~~~~~~~~~~~~~~~~~~~~~

The state button callback is used to generate the state toggle button to toggle the active state of an entry.

.. code-block:: php

   <?php

    $GLOBALS['TL_DCA']['tl_example']['list']['operations']['toggle']['button_callback'][] = Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::stateButton(
        'tl_example',
        'published',
        'custom-invisible-icon.png'
    );

For more details please have a look at the `StateButtonCallback`_.


Color picker wizard
~~~~~~~~~~~~~~~~~~~

The color picker wizard provides a wizard to choose a rgb color.

.. code-block:: php

   <?php

    $GLOBALS['TL_DCA']['tl_example']['fields']['color']['wizard'][] = Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::colorPicker();

For more details please have a look at the `ColorPicker`_ wizard.


File picker wizard
~~~~~~~~~~~~~~~~~~

The file picker wizard provides a popup wizard to choose a file.

.. code-block:: php

   <?php

    $GLOBALS['TL_DCA']['tl_example']['fields']['file']['wizard'][] = Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::filePicker();

For more details please have a look at the `FilePicker`_ wizard.


Page picker wizard
~~~~~~~~~~~~~~~~~~

The page picker wizard provides a popup wizard to choose a page.

.. code-block:: php

   <?php

    $GLOBALS['TL_DCA']['tl_example']['fields']['page']['wizard'][] = Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::pagePicker();

For more details please have a look at the `PagePicker`_ wizard.


Popup wizard
~~~~~~~~~~~~

The popup wizard opens a link in a popup overlay.

.. code-block:: php

   <?php

    $GLOBALS['TL_DCA']['tl_example']['fields']['article']['wizard'][] = Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::popupWizard(
        'do=article',
        'Edit article',
        'Open selected article',
        'icon.png'
    );

For more details please have a look at the `PopupWizard`_ wizard.


Get templates callback
~~~~~~~~~~~~~~~~~~~~~~

The get templates callback get all available templates.

.. code-block:: php

   <?php

    $GLOBALS['TL_DCA']['tl_example']['fields']['template']['options_callback'] = Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::getTemplates(
        'ce_',      // Prefix
        ['ce_text'] // Exclude these templates.
    );


Invoker
-------

If you want to trigger a callback form your code you don't have to worry about the different supported callback formats.
For this case toolkit provides an invoker which is registered as a service.

.. code-block:: php

   <?php

    /** @var Netzmacht\Contao\Toolkit\Dca\Callback\Invoker $invoker */
    $invoker = $container->get(Netzmacht\Contao\Toolkit\DependencyInjection\Services::CALLBACK_INVOKER);

    // Invoke the callback and get the return values.
    $options = $invoker->invoke($GLOBALS['TL_DCA']['tl_example']['fields']['template']['options_callback'], [$dc]);

    // Invoke a list of callbacks and define which value should changed after invoking a callback.
    // The last argument indicates that the first argument of the arguments array ($value) should be changed
    $value = $invoker->invokeAll($GLOBALS['TL_DCA']['tl_example']['fields']['save_callback'], [$value, $dc], 0);


.. _GenerateAliasCallback: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Callback/Save/GenerateAliasCallback.php
.. _StateButtonCallback: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Callback/Button/StateButtonCallback.php
.. _ColorPicker: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Callback/Wizard/ColorPicker.php
.. _FilePicker: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Callback/Wizard/FilePicker.php
.. _PagePicker: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Callback/Wizard/PagePicker.php
.. _PopupWizard: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Dca/Callback/Wizard/PopupWizard.php
