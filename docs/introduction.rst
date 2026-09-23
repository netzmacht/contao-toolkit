Introduction
============

I develop Contao extensions for many years. Although Contao provides a useful library some aspects which are required in
the daily life are missing. Toolkit fills these gaps with small, interface based services which integrate into
Contao's Symfony based architecture. Working in projects which have high quality standards some improvements were
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

   contao-haste
    The `haste libary`_ is one of the more comprehensive developer tools for Contao developers. There are some
    intersections between Toolkit and haste but the main focus is different.

Where Contao meanwhile provides a native solution, Toolkit removed its own implementation in favour of the Contao
feature. See the `upgrade guide`_ for details.


Examples
--------

If you want to see how Toolkit is used in real contao extensions you can have a look at following examples:

 * `contao-leaflet-maps`_
 * `contao-form-validation`_


.. _haste libary: https://github.com/codefog/contao-haste
.. _upgrade guide: https://github.com/netzmacht/contao-toolkit/blob/develop/UPGRADE-5.0.md
.. _contao-leaflet-maps: https://github.com/netzmacht/contao-leaflet-maps
.. _contao-form-validation: https://github.com/netzmacht/contao-form-validation
