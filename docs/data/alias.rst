Alias generator
===============

.. important::

   The filter-based alias generator described below (`FilterBasedAliasGenerator`, the `Filter`
   chain, `AliasGeneratorFactory`/`ToolkitAliasGeneratorFactory`) is deprecated as of 4.1.0 and
   will be removed in 5.0.0. Use the `Slug-based generator`_ instead.

.. _Slug-based generator:

Slug-based generator (recommended)
----------------------------------

`Netzmacht\\Contao\\Toolkit\\Data\\Alias\\SlugAliasGenerator` implements the same
`AliasGenerator`_ interface but delegates to Contao's own `contao.slug` service
(`Contao\\CoreBundle\\Slug\\Slug`, backed by `ausi/slug-generator`) instead of a custom filter
chain. It still uses the existing `Validator`_ (typically `UniqueDatabaseValueValidator`_) to
guard uniqueness — including for a manually entered, non-unique value, which now throws
`InvalidAliasException`_ instead of being silently overwritten (a deliberate behavior change
versus the deprecated filter-based generator).

Use it via the `SlugAliasListener` callback — see :doc:`../dca/callbacks`.

Filter based alias generator (deprecated)
------------------------------------------

The core of this tool is a `filter based alias generator`_. It implements the `alias generator interface`_ and uses a
set of `filters`_ to generate the alias. It uses a `validator`_ to ensure a valid alias is generated.

Features
~~~~~~~~

 * Different alias generation strategies
 * Combining multiple columns
 * Customizable separator string


Provided filters
~~~~~~~~~~~~~~~~

All provided filters have different strategies to modify an alias value. They can combine strings or just return a
simple value.

.. glossary::

   `ExistingAliasFilter`_
    This filter just passes the existing alias value. Usually it should be the first filter in the chain. If you
    always want to recreate an alias just don't use this filter.

   `RawValueFilter`_
    This filter uses the alias values

   `SlugifyFilter`_
    Generates a standardized value like Contao *standardize* helper function does.

   `SuffixFilter`_
    This filter adds a numeric suffix which is count until an valid alias is generated.


Provided validators
~~~~~~~~~~~~~~~~~~~

.. glossary::

   `UniqueDatabaseValueValidator`_
    This validator checks the database if an unique value exists. It only has a global scope which means the unique
    value has to be unique in the whole data set.


Default factory
---------------

Toolkit has a default factory which creates an alias generator which has the default behaviour how Contao generates
alias:

 1. Only create alias value if field is empty
 2. standardize the value
 3. Add suffix value

.. code-block:: php

    <?php
    // Default alias generator factory is provided as service with service id:
    // "netzmacht.contao_toolkit.data.alias_generator.factory.default_factory"

    /** @var \Netzmacht\Contao\Toolkit\Data\Alias\Factory\AliasGeneratorFactory $factory */
    $aliasGenerator = $factory->create('my_table', 'alias_field', ['value', 'fields']);


Alias Generator callback
------------------------

Toolkit also provides an alias generator callback. See :doc:`../dca/callbacks` for it.


.. _filter based alias generator: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Alias/FilterBasedAliasGenerator.php
.. _alias generator interface: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Alias/AliasGenerator.php
.. _filters: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Alias/Filter.php
.. _validator: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Alias/Validator.php
.. _ExistingAliasFilter: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Alias/Filter/ExistingAliasFilter.php
.. _RawValueFilter: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Alias/Filter/RawValueFilter.php
.. _SlugifyFilter: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Alias/Filter/SlugifyFilter.php
.. _SuffixFilter: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Alias/Filter/SuffixFilter.php
.. _UniqueDatabaseValueValidator: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Alias/Validator/UniqueDatabaseValueValidator.php
