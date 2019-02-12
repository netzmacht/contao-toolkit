Definition
==========

Contao uses data definition array `dca` to describe data containers like tables, files or the file system. Unfortunately
there is no API do address and manipulate the information. Toolkit provides a limited but simple API to access the
definitions. Accessing superglobals is not required anymore which makes your code more testable.


DCA Manager
-----------

The entry point to access a data container file is the dca manager. If you want to access information from a dca
definition, simply get the definition from the dca manager.

.. code-block:: php

   <?php

   /** @var Netzmacht\Contao\Toolkit\Dca\Manager $dcaManager */
   $dcaManger  = $container->get('netzmacht.contao_toolkit.dca.manager');

   /** @var Netzmacht\Contao\Toolkit\Dca\Definition $definition */
   $definition = $dcaManger->getDefinition('tl_content');


Access definition
-----------------

If you want to access some information from the definition there are two methods: `get` to retrieve information and `set`
to add information. To get the information path there are two syntax supported: Array notated syntax or a string separated
with dots.


.. code-block:: php

   <?php

   /** @var Netzmacht\Contao\Toolkit\Dca\Definition $definition */
   $definition = $dcaManger->getDefinition('tl_content');

   // Read from the definition
   $driver = $definition->get('config.dataContainer');
   $driver = $definition->get(['config', 'dataContainer']);

   // Write to the definition.
   $definition->set('fields.text.eval.tl_class', 'w50');
   $definition->set(['fields', 'text', 'eval', 'tl_class'], 'w50');
