Changelog
=========

[Unreleased]
------------

### Added

 - Asset manager: Add support for asset packages based assets

### Improved

 - Declare model type in the abstract fragment controllers using psalm's templates feature

### Changed

 - Replace patchwork/utf8 to symfony/string ([#31](https://github.com/netzmacht/contao-toolkit/pull/31))
 - Remove components, use proper fragment controllers


[3.7.4] (2022-01-25)
--------------------

### Fixed

 - Fix state button callback for disabling a state

[3.7.3] (2022-01-25)
--------------------

### Fixed

 - Fix visibility logic in hybrid component
 - Fix unique database value validator
 - Fix broken YesNoFormatter

[3.7.2] (2022-01-25)
--------------------

### Fixed

 - Remove backward incompatible type annotation [#29](https://github.com/netzmacht/contao-toolkit/issues/29)

[3.7.0] (2022-01-24)
--------------------

### Changed

 - Allow doctrine/dbal ^3.0 for Contao 4.13 compatibility
 - Bump symfony requirements
 - Change coding standard
 - Use symfony contracts where required for Symfony 5 compatibility

[3.6.2] (2021-10-12)
--------------------

### Changed

 - Unlock more symfony 5 components

### Fixed

 - Use the correct csrf token manager ([#28](https://github.com/netzmacht/contao-toolkit/pull/28))

[3.6.1] (2021-01-07)
--------------------

### Fixed

 - Introduce new template renderer which does not require the templating engine to support twig templates 

[3.6.0] (2020-12-15)
--------------------

### Added

 - Provide base fragment controllers for content elements, modules and hybrids
 - Support short templates names (`fe:template_name`, `be:template_name`) 

### Changed

 - Require at least Contao `^4.9`
 - Always activate the fos cache response tagger if available
 - Wizard listeners now support `netzmacht.contao_toolkit.template_renderer`. Support for `templating` is deprecated.
 
### Removed

 - Remove Contao translator backport
 - Remove tagged hook listener backport

[3.5.0] (2020-11-20)
--------------------

### Added

 - Add a new template renderer service which will replace the integration of the templating engine
 
### Deprecated

 - Integration in the templating engine. Will be removed in version 4.0.
 - Deprecate the components (Frontend module and content elements) - Use contao fragments instead


[3.4.5] (2020-08-28)
--------------------

### Fixed

 - Fix type class for content elements based on hybrids

[3.4.4] (2020-08-27)
--------------------

### Fixed

 - `ỲesNoLabelFormatter` was not registered

[3.4.3] (2020-08-26)
--------------------

### Fixed

 - Prevent initialization error caused by not initialized contao framework

[3.4.2] (2020-06-30)
--------------------

### Changed

 - Unlock `symfony/security-core:^5.0`

[3.4.1] (2020-06-26)
--------------------

### Fixed

 - Fix specs from missing merge

[3.4.0] (2020-06-26)
--------------------

### Added

 - Add `netzmacht.contao_toolkit.contao.image_adapter` for the `Contao\Image` class
 - Introduce interface `Netzmacht\Contao\Toolkit\Dca\DcaManager`
 - Add alias `netzmacht.contao_toolkit.csrf.token_manager` which refers to the token manager used in Contao
 - Add CsrfTokenProvider to simplify access to the request token
 - Add `Netzmacht\Contao\Toolkit\View\Assets\HtmlPageAssetsManager` interface which extends the AssetsManager

### Changed

 - Adding `netzmacht.contao_toolkit.component.content_element_factory` and
   `netzmacht.contao_toolkit.component.frontend_module_factory` isn't required anymore 
 - Do not depend on constant TL_MODE to detect request scope. Use `RequestScopeMatcher` instead for components. Not
   passing `RequestScopeMatcher` as constructor argument to components (content elements, modules) is deprecated now.
 - Do not detect preview mode using BE_USER_LOGGED_IN constant. State has to be passed to the constructor for content 
   elements. Deprecate not passing the `isPreviewMode` state as constructor argument.
 - Adding `netzmacht.contao_toolkit.component.content_element_factory` and
   `netzmacht.contao_toolkit.component.frontend_module_factory` isn't required anymore
 - Service `netzmacht.contao_toolkit.assets_manager` has to implement 
   `Netzmacht\Contao\Toolkit\View\Assets\HtmlPageAssetsManager` now.
   
### Fixed

 - Fix css class for frontend modules

[3.3.3] (2020-03-30)
--------------------

### Fixed

 - Fixed potential security issues of symfony components [CVE-2019-10913](https://github.com/advisories/GHSA-x92h-wmg2-6hp7) 
   and [CVE-2019-18888](https://github.com/advisories/GHSA-xhh6-956q-4q69)
   
[3.3.2] (2020-01-20)
--------------------

### Fixed

 - Fixed specs and coding standards

[3.3.1] (2019-05-07)
--------------------

### Fixed

 - Fix visibility of content elements

[3.3.0] (2019-04-09)
--------------------

### Added

 - Auto register models in globals registry if defined as repository.
 - Log if translated message does not exist.
 - Generate deprecated error if domain prefix is added to a translated message.

### Fixed

 - Fix css class for frontend modules

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

[Unreleased]: https://github.com/netzmacht/contao-toolkit/compare/3.4.5...master
[3.6.0]: https://github.com/netzmacht/contao-toolkit/compare/3.5.0...3.6.0
[3.5.0]: https://github.com/netzmacht/contao-toolkit/compare/3.4.5...3.5.0
[3.4.5]: https://github.com/netzmacht/contao-toolkit/compare/3.4.4...3.4.5
[3.4.4]: https://github.com/netzmacht/contao-toolkit/compare/3.4.3...3.4.4
[3.4.3]: https://github.com/netzmacht/contao-toolkit/compare/3.4.2...3.4.3
[3.4.2]: https://github.com/netzmacht/contao-toolkit/compare/3.4.1...3.4.2
[3.4.1]: https://github.com/netzmacht/contao-toolkit/compare/3.4.0...3.4.1
[3.4.0]: https://github.com/netzmacht/contao-toolkit/compare/3.3.3...3.4.0
[3.3.3]: https://github.com/netzmacht/contao-toolkit/compare/3.3.2...3.3.3
[3.3.2]: https://github.com/netzmacht/contao-toolkit/compare/3.3.1...3.3.2
[3.3.1]: https://github.com/netzmacht/contao-toolkit/compare/3.3.0...3.3.1
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
