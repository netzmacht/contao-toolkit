Introduction
============

I develop Contao extensions for many years. Although Contao provides a useful library some aspects which are required in
the daily life are missing. Furthermore some developer concepts which has grown in popularity, especially dependency
injection, wasn't really usable in Contao. Working in projects which have high quality standards some improvements were
required.


Goals
-----

Developing and providing Toolkit was made with following goals in mind:

 * Provide often required features
 * Increase code quality
 * Create testable code
 * Fasten development


Developer tools
---------------

Toolkit does not replace existing developer tools which are provided by other Contao developers. It rather complement
and uses the features provided by the other tools.

.. glossary::

   dependency-container
    The `dependency-container`_ introduces dependency injection into Contao first. Toolkit uses it for its own
    dependency injection features.

   event-dispatcher
    The `event-dispatcher`_ integrates the Symfony event dispatcher into Contao. Toolkit is based on it and provides
    some useful events to interact with Toolkit.

   translator
    The `translator`_ library provides a neat interface to use translation in your extensions. Toolkit requires it as
    dependency because the internal used translations is done by it. It also recommend to use it in own extensions.

   contao-haste
    The `haste libary`_ is one of the more comprehensive developer tools for Contao developers. There are some
    intersections between Toolkit and haste but the main focus is different.



Examples
--------

If you want to see how Toolkit is used in real contao extensions you can have a look at following examples:

 * `contao-leaflet-maps`_
 * `contao-form-validation`_
 * `contao-timelinejs`_


.. _haste libary: https://github.com/codefog/contao-haste
.. _dependency-container: https://github.com/contao-community-alliance/dependency-container
.. _event-dispatcher: https://github.com/contao-community-alliance/event-dispatcher
.. _translator: https://github.com/contao-community-alliance/translator
.. _contao-leaflet-maps: https://github.com/netzmacht/contao-leaflet-maps
.. _contao-form-validation: https://github.com/netzmacht/contao-form-validation
.. _contao-timelinejs:  https://github.com/netzmacht/contao-timelinejs
