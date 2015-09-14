Contao Dev Tools
==================

[![Build Status](http://img.shields.io/travis/netzmacht/contao-toolkit/master.svg?style=flat-square)](https://travis-ci.org/netzmacht/contao-toolkit)
[![Version](http://img.shields.io/packagist/v/netzmacht/contao-toolkit.svg?style=flat-square)](http://packagist.com/packages/netzmacht/contao-toolkit)
[![License](http://img.shields.io/packagist/l/netzmacht/contao-toolkit.svg?style=flat-square)](http://packagist.com/packages/netzmacht/contao-toolkit)
[![Downloads](http://img.shields.io/packagist/dt/netzmacht/contao-toolkit.svg?style=flat-square)](http://packagist.com/packages/netzmacht/contao-toolkit)
[![Contao Community Alliance coding standard](http://img.shields.io/badge/cca-coding_standard-red.svg?style=flat-square)](https://github.com/contao-community-alliance/coding-standard)

This library is a developer toolkit for developing in Contao CMS. It contains a bunch of helpers which are used in many 
of the extensions of [netzmacht creative][8].

Install
-------

You can install this library using Composer. It requires at least PHP 5.4 and Contao 3.2.

```
$ php composer.phar require netzmacht/contao-toolkit:~1.0
```

Features
--------

There are different topics which the toolkit covers.

 * Easy **dependency Injection** in closed Contao objects providing a [ServiceContainerTrait][1] and a basic 
   [ServiceContainer service][2].
 * Adding assets through an [AssetsManager][3].
 * Translations using contao-community-alliance/translator. Easy access providing an [Trait][4].
 * Extended [Frontend and Backend template][5] classes without magic property access and including the translator service.
 * Data container related features:
     * API access for [reading and manipulating definitions][6].
     * OptionsBuilder to convert typically data into options.
     * Generic callbacks for toggle icons, alias generation and a customizable color picker.
 * Providing default events
     * [BuildModelQueryOptionsEvent][7] for highly customizable components.

[1]: https://github.com/netzmacht/contao-toolkit/blob/master/src/Netzmacht/Contao/Toolkit/ServiceContainerTrait.php
[2]: https://github.com/netzmacht/contao-toolkit/blob/master/src/Netzmacht/Contao/Toolkit/ServiceContainer.php
[3]: https://github.com/netzmacht/contao-toolkit/blob/master/src/Netzmacht/Contao/Toolkit/View/AssetsManager.php
[4]: https://github.com/netzmacht/contao-toolkit/blob/master/src/Netzmacht/Contao/Toolkit/TranslatorTrait.php
[5]: https://github.com/netzmacht/contao-toolkit/tree/master/src/Netzmacht/Contao/Toolkit/View
[6]: https://github.com/netzmacht/contao-toolkit/blob/master/src/Netzmacht/Contao/Toolkit/Dca/Definition.php
[7]: https://github.com/netzmacht/contao-toolkit/blob/master/src/Netzmacht/Contao/Toolkit/Event/BuildModelQueryOptionsEvent.php
[8]: https://github.com/netzmacht/
