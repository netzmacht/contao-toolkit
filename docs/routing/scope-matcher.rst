ScopeMatcher
============

Use ``Contao\CoreBundle\Routing\ScopeMatcher`` directly to check the current request scope —
since Contao 5, its ``isFrontendRequest()``/``isBackendRequest()``/``isContaoRequest()`` methods
already accept an optional ``?Request`` argument and fall back to the current request from the
request stack themselves.

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\CoreBundle\Routing\ScopeMatcher;

   final class MyService
   {
       public function __construct(private readonly ScopeMatcher $scopeMatcher)
       {
       }

       public function example(): bool
       {
           // No $request argument needed - falls back to the request stack internally.
           return $this->scopeMatcher->isBackendRequest();
       }
   }
