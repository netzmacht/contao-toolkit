Formatter
=========

Contao uses the data container definition to describe stored data. Also some information about the data should be
presented are stored. Unfortunately there is no way to access the formatting using an API. Even more the formatting
differ between different places (e.g. show view and parent view).

Toolkit provides a formatter framework which allows you to easily access the default formatting which is used in Contao.
Furthermore it also allows you to customize the behaviour by registering custom formatter. This way it's much more
flexible then other helper libraries (e.g. `Contao Haste`_).

If you don't want customize the behaviour you don't have to worry about the cration of the formatter framework. Simply
get the formatter for your data container:

.. code-block:: php

   <?php

   use Netzmacht\Contao\Toolkit\DependencyInjection\Services;

   /** @var Netzmacht\Contao\Toolkit\Dca\Manager $dcaManager */
   $dcaManger  = $container->get(Services::DCA_MANAGER);

   /** @var Netzmacht\Contao\Toolkit\Dca\Formatter\Formatter $formatter */
   $formatter = $dcaManager->getFormatter('tl_content');


Format a value
--------------

To format a value use the formatter and pass field name and the value:

.. code-block:: php

   <?php

    echo $formatter->formatValue('singleSRC', $contentModel->singleSRC);

    // If you use it in the backend dca view you should pass the data container driver as the context object.
    // Some formatter requires it.

    echo $formatter->formatValue('singleSRC', $contentModel->singleSRC, $dc);


Format labels
-------------

Not only values can be formatted. Also field labels and descriptions get formatted (translated).

.. code-block:: php

   <?php

    echo $formatter->formatFieldLabel('singleSRC');
    echo $formatter->formatFieldDescription('singleSRC');


Format options
--------------

If your field contains some options the data container also stores information how to translate/format the options.

.. code-block:: php

   <?php

    echo $formatter->formatOptions('multiSRC', $contentModel->multiSRC);


Customize formatter
-------------------

Toolkit provides an event driven approach to customize the formatter. Each formatter contains a chain of formatter. The
chain contains three steps:

 * **Pre filters** (e.g. deserialize values)
 * **Formatter**
 * **Post filters** (e.g. flatten arrays)

To customize the behaviour you only have to subscribe the
:code:`Netzmacht\Contao\Toolkit\Dca\Formatter\Event\CreateFormatterEvent`:

.. code-block:: php

    // config/event_listeners.php
    use Netzmacht\Contao\Toolkit\Dca\Formatter\Event\CreateFormatterEvent;

    return [
        CreateFormatterEvent:NAME => [
            function (CreateFormatterEvent $event) {
                if ($event->getDefinition()->getName() != 'tl_my_example') {
                    return;
                }

                // CustomFilter has to be an instance of Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter
                $formatter = new CustomValueFormatter();
                $event->addFormatter($formatter);
            }
        ]
    ];


.. _Contao Haste: https://github.com/codefog/contao-haste/
