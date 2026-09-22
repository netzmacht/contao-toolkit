CacheTagManager
================

Use ``Contao\CoreBundle\Cache\CacheTagManager`` (service ``contao.cache.tag_manager``) to tag
responses for cache invalidation.

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
           $this->cacheTagManager->tagWithModelInstance($model);
       }
   }

``CacheTagManager`` offers automatic tag derivation from Model/Entity instances and classes, and
tag invalidation via ``invalidateTagsFor()``/``invalidateTags()``. Every ``tagWith*()`` method
no-ops internally when FOS HttpCache isn't installed, so callers never need to check whether
caching is active.
