Formatter
=========

Contao uses the data container definition to describe stored data. Also some information about the data should be
presented are stored. Unfortunately there is no way to access the formatting using an API. Even more the formatting
differ between different places (e.g. show view and parent view).

Toolkit provides a formatter framework which allows you to easily access the default formatting which is used in Contao.
Furthermore it also allows you to customize the behaviour by registering custom formatter. This way it's much more
flexible then other helper libraries (e.g. `Contao Haste`_).

If you don't want customize the behaviour you don't have to worry about the creation of the formatter framework. Simply
get the formatter for your data container from the :doc:`dca manager <definition>`:

.. code-block:: php

   <?php

   /** @var Netzmacht\Contao\Toolkit\Dca\DcaManager $dcaManager */
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
``formatOptions`` expects a list of option values and returns the formatted options.

.. code-block:: php

   <?php

   $labels = $formatter->formatOptions('type', ['text', 'headline']);


How values are formatted
------------------------

Each formatter uses a chain of value formatters. The chain contains three steps:

#. **Pre filters** are applied one after another (e.g. deserialize values).
#. **Formatter** — the first formatter which accepts the field formats the value.
#. **Post filters** are applied one after another (e.g. flatten arrays).

Toolkit ships following value formatters:

==========================  ===========  ===========================================================================
Class                       Step         Accepts
==========================  ===========  ===========================================================================
``HiddenValueFormatter``    pre filter   ``inputType`` password, ``eval.doNotShow`` or ``eval.hideInput``
``DeserializeFormatter``    pre filter   all fields
``ForeignKeyFormatter``     formatter    fields with a ``foreignKey``
``FileUuidFormatter``       formatter    ``inputType`` fileTree
``DateFormatter``           formatter    ``tstamp`` and ``eval.rgxp`` date, datim or time
``YesNoFormatter``          formatter    single checkboxes
``ReferenceFormatter``      formatter    fields with a ``reference``
``OptionsFormatter``        formatter    fields with ``options``, ``options_callback`` or ``eval.isAssociative``
``FlattenFormatter``        post filter  fields with ``eval.multiple``
==========================  ===========  ===========================================================================

``OptionsFormatter`` is also used as options formatter for ``formatOptions()``. The ``HtmlFormatter`` is available but
not registered by default.


Customize formatter
-------------------

Write a custom value formatter
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

A value formatter implements ``Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter``:

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;

   final class PriceFormatter implements ValueFormatter
   {
       public function accepts(string $fieldName, array $fieldDefinition): bool
       {
           return ($fieldDefinition['eval']['rgxp'] ?? null) === 'price';
       }

       public function format(mixed $value, string $fieldName, array $fieldDefinition, mixed $context = null): mixed
       {
           return number_format((float) $value, 2, ',', '.') . ' €';
       }
   }


Register it for all data containers
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Tag the service to add it to the formatter chain of every data container. Use the tag which matches the step:

* ``netzmacht.contao_toolkit.dca.formatter``
* ``netzmacht.contao_toolkit.dca.formatter.pre_filter``
* ``netzmacht.contao_toolkit.dca.formatter.post_filter``

Since the first accepting formatter wins, use the ``priority`` attribute to run your formatter before the built-in
ones.

.. code-block:: yaml

   # config/services.yaml
   services:
       App\Formatter\PriceFormatter:
           tags:
               - { name: 'netzmacht.contao_toolkit.dca.formatter', priority: 10 }


Register it for a specific data container
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The formatter is created by dispatching the ``Netzmacht\Contao\Toolkit\Dca\Formatter\Event\CreateFormatterEvent``.
Listen to it to customize the formatter of a specific data container. Besides ``addFormatter()`` the event provides
``addPreFilter()``, ``addPostFilter()`` and ``setOptionsFormatter()``.

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Netzmacht\Contao\Toolkit\Dca\Formatter\Event\CreateFormatterEvent;
   use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

   #[AsEventListener(CreateFormatterEvent::NAME, priority: 10)]
   final class CreateFormatterListener
   {
       public function __invoke(CreateFormatterEvent $event): void
       {
           if ($event->getDefinition()->getName() !== 'tl_my_example') {
               return;
           }

           $event->addFormatter(new PriceFormatter());
       }
   }

.. note:: Toolkit registers its default formatters in a listener with priority ``0``. Formatters are tried in the order
   they were added, so use a higher priority to add yours before the default ones.


.. _Contao Haste: https://github.com/codefog/contao-haste/
