Contao Dev Tools
==================

[![Build Status](http://img.shields.io/travis/netzmacht/contao-dev-tools/develop.svg?style=flat-square)](https://travis-ci.org/netzmacht/contao-dev-tools)
[![Version](http://img.shields.io/packagist/v/netzmacht/contao-dev-tools.svg?style=flat-square)](http://packagist.com/packages/netzmacht/contao-dev-tools)
[![License](http://img.shields.io/packagist/l/netzmacht/contao-dev-tools.svg?style=flat-square)](http://packagist.com/packages/netzmacht/contao-dev-tools)
[![Downloads](http://img.shields.io/packagist/dt/netzmacht/contao-dev-tools.svg?style=flat-square)](http://packagist.com/packages/netzmacht/contao-dev-tools)
[![Contao Community Alliance coding standard](http://img.shields.io/badge/cca-coding_standard-red.svg?style=flat-square)](https://github.com/contao-community-alliance/coding-standard)

This library provides tools for solving common tasks required when developing for Contao CMS.

Install
-------

You can install this library using Composer.

```
$ php composer.phar require netzmacht/contao-dev-tools:~1.0
```

Features
--------

### DCA related features

####Toggle icon callback

This library ships with a common toggle state icon callback.

```php
<?php

// dca/tl_custom.php

$GLOBALS['TL_DCA']['tl_custom']['fields']['published']['button_callback'] = 
	Netzmacht\Contao\DevTools\Ḑca::createToggleIconCallback(
		'tl_custom',	// The database table.
		'published',	// the state column
		false,			// Inverse the state. Set to true for invisible='' columns
		'invisible.gif' // Disabled icon. If not set, icons are transformed from edit.gif to edit_.gif
	)
```

####Convert models to options

One standard task using Contao models is to transform when to options for select lists or checkbox menus. Dev-Tools 
simplifies it for you.

 * Convert collections to options arrays.
 * Use callbacks to customize the labels.
 * Group values by a third column/callback value.

```php
<?php 

public function yourOptionsCallback()
{
	$collection = \PageModel::findAll();
	
	// Empty collections by Contao are NULL. The OptionsBuilder handles is correctly.
	return Netzmacht\Contao\DevTools\Dca\Options\OptionsBuilder::fromCollection($collection, 'id', 'name')
		->groupBy('type')
		->getOptions();
}
```php

###Dependency injection

Contao does not provide any help for dependency injection. If you need some dependencies from the 
[dependency container](https://github.com/contao-community-alliance/dependency-container) you can use this Trait:

```php

class YourContentElement extends \ContentElement
{
	use Netzmacht\Contao\DevTolls\ServiceContainerTrait;
	
	private $service;
	
	public function __construct($model)
	{
		parent::__construct($model);
		
		$this->service = $this->getService('your-required-service');
	}
}

```

## Requirements

 * PHP 5.4 is required.
 * Contao 3.2 - 3.4 is supported.
 * contao-community-alliance/dependency-container ~1.6 is required.
