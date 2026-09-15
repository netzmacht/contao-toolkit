RequestScopeMatcher
=====================

.. important::

   ``Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher`` is deprecated as of 4.1.0 and will be
   removed in 5.0.0. Use ``Contao\CoreBundle\Routing\ScopeMatcher`` directly instead — since
   Contao 5, its ``isFrontendRequest()``/``isBackendRequest()``/``isContaoRequest()`` methods
   already accept an optional ``?Request`` argument and fall back to the current request from the
   request stack themselves, making the toolkit's own wrapper redundant.

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

``RequestScopeMatcher::isInstallRequest()`` has been removed outright (not just deprecated) — the
``contao_install`` route it checked for no longer exists since Contao 5, so the method was already
permanently returning ``false`` under this package's ``^5.7`` floor.
