Alias generator
===============

`Netzmacht\\Contao\\Toolkit\\Data\\Alias\\SlugAliasGenerator` implements the `AliasGenerator`_
interface and delegates to Contao's own `contao.slug` service (`Contao\\CoreBundle\\Slug\\Slug`,
backed by `ausi/slug-generator`). It uses the existing `Validator`_ (typically
`UniqueDatabaseValueValidator`_) to guard uniqueness — including for a manually entered,
non-unique value, which throws `InvalidAliasException`_ instead of being silently overwritten.

Use it via the `SlugAliasListener` callback — see :doc:`../dca/callbacks`.

.. _AliasGenerator: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Alias/AliasGenerator.php
.. _Validator: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Alias/Validator.php
.. _UniqueDatabaseValueValidator: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Alias/Validator/UniqueDatabaseValueValidator.php
.. _InvalidAliasException: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Alias/Exception/InvalidAliasException.php
