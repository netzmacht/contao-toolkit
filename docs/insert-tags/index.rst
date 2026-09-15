Insert tags
===========

Contao provides a native, attribute-based way to register insert tags since Contao 5.0. This is the recommended way
to implement custom insert tags — use it instead of Toolkit's own (deprecated) ``InsertTag`` classes.

Registering an insert tag
--------------------------

Mark an invokable service method with ``#[AsInsertTag('name')]``. The attribute is repeatable and can be placed on
the class or on individual methods.

.. code-block:: php

   <?php

   declare(strict_types=1);

   namespace App\InsertTag;

   use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag;
   use Contao\CoreBundle\InsertTag\InsertTagResult;
   use Contao\CoreBundle\InsertTag\OutputType;
   use Contao\CoreBundle\InsertTag\ResolvedInsertTag;

   #[AsInsertTag('smiley')]
   final class SmileyInsertTag
   {
       public function __invoke(ResolvedInsertTag $insertTag): InsertTagResult
       {
           $mood = $insertTag->getParameters()->get(0) ?? 'happy';

           return new InsertTagResult($this->renderSmiley($mood), OutputType::html);
       }

       private function renderSmiley(string $mood): string
       {
           // ...
       }
   }

Reliable parameter access
--------------------------

``ResolvedInsertTag::getParameters()`` returns a ``ResolvedParameters`` value object which replaces what Toolkit's
own ``ArgumentParser``/``AbstractSingleInsertTagParser`` tried to provide on top of the raw tag string:

.. code-block:: php

   <?php

   $parameters = $insertTag->getParameters();

   $parameters->get(0);            // First positional parameter.
   $parameters->all();             // All positional parameters as a list.
   $parameters->get('name');       // Named parameter ("name=value" convention), if present.
   $parameters->getScalar(0);      // Automatically cast to int/float where applicable.

Named parameters, nested insert-tag resolution and caching metadata (``InsertTagResult::withExpiresAt()``,
``withCacheTags()``) are all handled natively — no manual query parsing is required.

.. _insert-tags-deprecated:

Deprecated: Toolkit's own `InsertTag` classes
-----------------------------------------------

.. important::

   ``Netzmacht\Contao\Toolkit\InsertTag\AbstractInsertTagParser``, ``AbstractSingleInsertTagParser``,
   ``ArgumentParser`` and ``ArgumentParserPlugin`` are deprecated as of 4.1.0 and will be removed in 5.0.0.
   Instantiating ``AbstractInsertTagParser`` (directly or via a subclass) or calling ``ArgumentParser::create()``
   triggers a runtime deprecation warning. Migrate to ``#[AsInsertTag]`` as shown above.

These classes were originally built to provide reliable parameter access on top of Contao's historic raw
``replaceInsertTags`` hook string. Contao's native insert-tag system now covers this natively and more, so no
replacement abstraction is provided by Toolkit — register your insert tag as a native Contao service instead.
