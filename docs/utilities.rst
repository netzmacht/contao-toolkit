Utilities
=========

CSRF token provider
-------------------

``Netzmacht\Contao\Toolkit\Security\Csrf\CsrfTokenProvider`` wraps the Contao CSRF token manager and provides easy
access to the default Contao request token. It's provided as service ``netzmacht.contao_toolkit.csrf.token_provider``.

.. code-block:: php

   <?php

   /** @var Netzmacht\Contao\Toolkit\Security\Csrf\CsrfTokenProvider $tokenProvider */

   // Value of Contao's default request token
   $requestToken = $tokenProvider->getTokenValue();

   // Token object of a specific token id
   $token = $tokenProvider->getToken('my_token');


Assertions
----------

Toolkit uses the `beberlei/assert`_ library. ``Netzmacht\Contao\Toolkit\Assertion\Assertion`` and
``Netzmacht\Contao\Toolkit\Assertion\Assert`` are configured to throw toolkit exceptions
(``AssertionFailed`` and ``LazyAssertionException``) which implement the toolkit's ``Exception`` interface.

.. code-block:: php

   <?php

   use Netzmacht\Contao\Toolkit\Assertion\Assertion;

   Assertion::integer($value);


Exceptions
----------

All exceptions thrown by the toolkit implement ``Netzmacht\Contao\Toolkit\Exception\Exception``, so you can catch them
all at once.

==================================================  ==================================================
Exception                                           Thrown when
==================================================  ==================================================
``Exception\InvalidArgumentException``              an invalid argument is passed
``Exception\RuntimeException``                      a runtime error occurs
``Exception\AccessDenied``                          the :doc:`updater <data/updater>` denies access
``Assertion\AssertionFailed``                       an assertion fails, e.g. an unknown data container
``Assertion\LazyAssertionException``                a lazy assertion chain fails
``Data\Alias\Exception\InvalidAliasException``      an alias is not unique, see :doc:`data/alias`
==================================================  ==================================================


.. _beberlei/assert: https://github.com/beberlei/assert
