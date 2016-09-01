Repositories
============

Contao uses it's own implementation for Models. Even though it's called models the pattern behind it is rather the
`active record pattern`_. The main issue of this implementation is the hidden access to the database connection.
Implementing rich models and writing tests is really painful.

To overcome this issue I recommend to use the `repository pattern`_. Toolkit provides some tools to ease using
repositories within Contao. I recommend to provide interfaces for each repository you have to create. The real
implementation can be easily switched now.


Default repository
------------------

Toolkit includes an base `Repository`_ interface. It simply provide an interface for Contao common find* and
count* methods. Furthermore it introduces Specifications. The implementation of the interface is `ContaoRepository`_
which simply delegates all methods to the model class.

.. hint:: Instead of implementing the `Repository`_ interface it's recommend to introduce own interfaces describing each
   required method. It doesn't matter if the provides ContaoRepository is used as the implementation as your code only
   trust the interface.


QueryProxy
----------

If you want to access all magic finders and callers of your model class from the repository, you can add the
`QueryProxy`_ trait to your repository.


Specification
-------------

Toolkit provides a common `Specification`_ interface implementing the `specification pattern`_. It can be used with the
`ContaoRepository` to `findBySpecification` or `countBySpecification`.


.. _active record pattern: https://en.wikipedia.org/wiki/Active_record_pattern
.. _repository pattern: http://martinfowler.com/eaaCatalog/repository.html
.. _Repository: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Model/Repository.php
.. _ContaoRepository: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Model/ContaoRepository.php
.. _QueryProxy: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Model/QueryProxy.php
.. _Specification: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Model/Specification.php
.. _specification pattern: https://en.wikipedia.org/wiki/Specification_pattern
