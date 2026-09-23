Definition
==========

Contao uses data definition array `dca` to describe data containers like tables, files or the file system. Unfortunately
there is no API do address and manipulate the information. Toolkit provides a limited but simple API to access the
definitions. Accessing superglobals is not required anymore which makes your code more testable.


DCA Manager
-----------

The entry point to access a data container definition is the dca manager described by the
``Netzmacht\Contao\Toolkit\Dca\DcaManager`` interface. It's provided as service ``netzmacht.contao_toolkit.dca.manager``.
The manager loads the data container (including language files) if required and caches the definition and the
formatter per request.

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Netzmacht\Contao\Toolkit\Dca\DcaManager;

   final class ExampleService
   {
       public function __construct(private readonly DcaManager $dcaManager)
       {
       }

       public function example(): void
       {
           $definition = $this->dcaManager->getDefinition('tl_content');

           // Pass true as second argument to bypass the cache and reload the definition.
           $definition = $this->dcaManager->getDefinition('tl_content', true);
       }
   }

If the data container does not exist, an ``Netzmacht\Contao\Toolkit\Assertion\AssertionFailed`` exception is thrown.


Access definition
-----------------

The ``Netzmacht\Contao\Toolkit\Dca\Definition`` provides following methods. Each method accepts the path either as an
array or as a string separated with slashes (``/``).

.. glossary::

   get($path, $default = null, $createIfNotExists = false)
    Get a value. Returns ``$default`` if the path does not exist. If ``$createIfNotExists`` is true, the default
    value is written to the definition. The value is returned by reference.

   has($path)
    Check if a path exists.

   set($path, $value)
    Set a value. Missing parent keys are created.

   modify($path, callable $handler)
    Modify a value. The handler gets the current value passed and has to return the new value.

   getName()
    Get the name of the data container.

.. code-block:: php

   <?php

   $definition = $this->dcaManager->getDefinition('tl_content');

   // Read from the definition
   $driver = $definition->get('config/dataContainer');
   $driver = $definition->get(['config', 'dataContainer']);

   // Check if a field exists
   if ($definition->has(['fields', 'headline'])) {
       // ...
   }

   // Write to the definition.
   $definition->set('fields/text/eval/tl_class', 'w50');
   $definition->set(['fields', 'text', 'eval', 'tl_class'], 'w50');

   // Modify an existing value.
   $definition->modify(
       ['fields', 'text', 'eval', 'tl_class'],
       static fn (string|null $value): string => trim($value . ' clr'),
   );
