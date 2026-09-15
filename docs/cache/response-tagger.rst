ResponseTagger
===============

.. important::

   ``Netzmacht\Contao\Toolkit\Response\ResponseTagger`` (and its ``FosCacheResponseTagger``/
   ``NoOpResponseTagger`` implementations) is deprecated as of 4.1.0 and will be removed in 5.0.0.
   It was introduced "as a backward compatibility layer for Contao < 4.6". Contao's own
   ``Contao\CoreBundle\Cache\CacheTagManager`` (service ``contao.cache.tag_manager``) now covers
   the same need natively — every ``tagWith*()`` method already no-ops internally when FOS
   HttpCache isn't installed, exactly what the toolkit's own encapsulation was for.

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\CoreBundle\Cache\CacheTagManager;

   final class MyService
   {
       public function __construct(private readonly CacheTagManager $cacheTagManager)
       {
       }

       public function example(object $model): void
       {
           // Instead of: $this->responseTagger->addTags(['contao.db.tl_example.1']);
           $this->cacheTagManager->tagWithModelInstance($model);
       }
   }

``CacheTagManager`` additionally offers automatic tag derivation from Model/Entity instances and
classes, and tag invalidation via ``invalidateTagsFor()``/``invalidateTags()`` — capabilities the
toolkit's own ``addTags()``-only interface never provided.
