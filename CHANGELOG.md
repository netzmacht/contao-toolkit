Changelog
=========

3.1.1 TBD
---------

[Full Changelog](https://github.com/netzmacht/contao-toolkit/compare/3.1.0...hotfix/3.1.1)

### Fixed

 - Do not register services tagged as contao.hook as service for Contao 4.5 and later

3.1.0 (2018-11-21)
------------------

[Full Changelog](https://github.com/netzmacht/contao-toolkit/compare/3.0.7...3.1.0)

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

3.0.7 (2018-10-30)
------------------

[Full Changelog](https://github.com/netzmacht/contao-toolkit/compare/3.0.6...3.0.7)

 - Fix a POSIX compilation error in the translator (See contao/core-bundle#1656)
 - Do not add domain as message prefix by default in the translator as Contao doesn't anymore. Added BC support. 

3.0.6 (2018-08-27)
------------------

[Full Changelog](https://github.com/netzmacht/contao-toolkit/compare/3.0.5...3.0.6)

 - Fix incompatibility with contao-community

3.0.5 (2018-08-24)
------------------

[Full Changelog](https://github.com/netzmacht/contao-toolkit/compare/3.0.4...3.0.5)

 - Get travis build work again for Contao 4.5 and later
 - Fix rendering of component classes 
 
3.0.4 (2018-08-24)
------------------

[Full Changelog](https://github.com/netzmacht/contao-toolkit/compare/3.0.3...3.0.4)

 - Update readme

3.0.3 (2018-08-24)
------------------

[Full Changelog](https://github.com/netzmacht/contao-toolkit/compare/3.0.2...3.0.3)

 - Allow symfony 4 components.
 - RUn composer-require-checker and fix issues.

3.0.2 (2018-03-29)
------------------

[Full Changelog](https://github.com/netzmacht/contao-toolkit/compare/3.0.1...3.0.2)

 - Fix the order of the labels in the options builder
 - Improve the translator compatibility by supporting catalogs

3.0.1 (2018-01-12)
------------------

[Full Changelog](https://github.com/netzmacht/contao-toolkit/compare/3.0.0...3.0.1)

 - Prevent error than no dca array is loaded (See [contao-leaflet-maps/issues/54](https://github.com/netzmacht/contao-leaflet-maps/issues/54)
 - Added changelog (#9)
