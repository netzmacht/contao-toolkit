Changelog
=========

[Unreleased]
------------

### Added

 - Add CsrfTokenProvider to simplify access to the request token
 
### Changed

 - Adding `netzmacht.contao_toolkit.component.content_element_factory` and
   `netzmacht.contao_toolkit.component.frontend_module_factory` isn't required anymore 

[3.3.0] (2019-04-09)
--------------------

### Added

 - Auto register models in globals registry if defined as repository.
 - Log if translated message does not exist.
 - Generate deprecated error if domain prefix is added to a translated message.

[3.2.0] (2019-02-12)
--------------------

### Added

 - Add service for Contao Message class
 - Add service for Contao Dbafs class
 - Add `Definition#modify` method to modify given defintion.
 
### Fixed

 - Default alias generator factory has to be a public service.

[3.1.1] (2018-12-17)
------------------

### Fixed

 - Fix contao/core-bundle version detection if the monorepo contao/contao is used
 - Do not register services tagged as contao.hook as service for Contao 4.5 and later

[3.1.0] (2018-11-21)
--------------------

### Added

 - Provide a response tagger service for forward compatibility for Contao < 4.6
 - Support table prefixing for columns in findBySpecification and order for findAll calls.
 - Support `type` as alternative to `alias` for registering components

### Changed

 - Switch to strict_types everywhere. Set internal classes as final (You should treat all classes as final).
 - Improve wizard picker positions
 - Change callback method from `handleCallbackName` to `onCallbackName` for all callbacks.
 - Use class name as service name for dca callback listeners.
 - Require PHP 7.1.
 
### Fixed

 - Fix broken FilePickerListener.

### Deprecated

 - Deprecate `handle*` methods for all dca callback listeners.
 - Deprecate dotted named for dca callback listener services. Use class names instead.

[3.0.7] (2018-10-30)
--------------------

 - Fix a POSIX compilation error in the translator (See contao/core-bundle#1656)
 - Do not add domain as message prefix by default in the translator as Contao doesn't anymore. Added BC support. 

[3.0.6] (2018-08-27)
--------------------

 - Fix incompatibility with contao-community/translator

[3.0.5] (2018-08-24)
--------------------

 - Get travis build work again for Contao 4.5 and later
 - Fix rendering of component classes 
 
[3.0.4] (2018-08-24)
--------------------

 - Update readme

[3.0.3] (2018-08-24)
--------------------

 - Allow symfony 4 components.
 - RUn composer-require-checker and fix issues.

[3.0.2] (2018-03-29)
--------------------

 - Fix the order of the labels in the options builder
 - Improve the translator compatibility by supporting catalogs

[3.0.1] (2018-01-12)
--------------------

 - Prevent error than no dca array is loaded (See [contao-leaflet-maps/issues/54](https://github.com/netzmacht/contao-leaflet-maps/issues/54)
 - Added changelog (#9)

[Unreleased]: https://github.com/netzmacht/contao-toolkit/compare/3.3.0...dev-develop
[3.3.0]: https://github.com/netzmacht/contao-toolkit/compare/3.2.0...3.3.0
[3.2.0]: https://github.com/netzmacht/contao-toolkit/compare/3.1.1...3.2.0
[3.1.1]: https://github.com/netzmacht/contao-toolkit/compare/3.1.0...3.1.1
[3.1.0]: https://github.com/netzmacht/contao-toolkit/compare/3.0.7...3.1.0
[3.0.8]: https://github.com/netzmacht/contao-toolkit/compare/3.0.6...3.0.7
[3.0.6]: https://github.com/netzmacht/contao-toolkit/compare/3.0.5...3.0.6
[3.0.5]: https://github.com/netzmacht/contao-toolkit/compare/3.0.4...3.0.5
[3.0.4]: https://github.com/netzmacht/contao-toolkit/compare/3.0.3...3.0.4
[3.0.3]: https://github.com/netzmacht/contao-toolkit/compare/3.0.2...3.0.3
[3.0.2]: https://github.com/netzmacht/contao-toolkit/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/netzmacht/contao-toolkit/compare/3.0.0...3.0.1
