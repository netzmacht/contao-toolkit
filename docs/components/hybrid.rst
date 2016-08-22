.. _hybrid:

Hybrid
======

The interface
-------------

The hybrid is a special kind of component. It's interface inherits from the interfaces of :doc:`content-element` and
:doc:`module` both. Components implementing the interface can be used as frontend modules and content elements at the
same time.


Using AbstractHybrid
--------------------

Contao also knows the concept of hybrids but in Contao hybrids are a bit limited. The assume that a foreign table is used
for the actual content. It's used for frontend forms in Contao, for instance.

The `AbstractHybrid`_ does not autoload any foreign dataset for you. instead it combines the behaviour of the abstract
content element and the abstract module described the the documentation. Each customization point is available here as
well.

.. _AbstractHybrid: https://github.com/netzmacht/contao-toolkit/tree/develop/src/Component/Hybrid/AbstractHybrid.php
