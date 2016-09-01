Frontend module
===============

Focusing on the main goal for Toolkit there is an easy way to create lightweight decoupled frontend modules.

Interface Module
----------------

The only requirement for an frontend module supported by Toolkit is that the interface
`Module`_ which extends the `Component`_ interface.

The Main difference between Contao frontend module and the interface is that the data attributes has to be accessible by
the explicit `get` and `set` methods. Magic `__get` and `__set` are not recommended.


Register a frontend module
--------------------------

Since Toolkit supports dependency injection for frontend modules you have to register your module by configuring a
callable as factory. The callable has to support at least two arguments. The third one is optional.

.. glossary::

   $model
    frontend module model or database result

   $column
    The column in which the element is created. Default is `main`

   $container
    The dependency container instance of `ContainerInterop\\ContainerInterface`

.. code-block:: php

    // src/Example.php
    class Example implements Netzmacht\Contao\Toolkit\Component\Module\Module
    {
        // ...
    }

    // config.php
    $GLOBALS['FE_MOD']['application']['example'] = function ($model, $column, ContainerInterop\ContainerInterface $container) {
        return new Example(
            $model,
            $column,
            $container->get('dependency-1'),
            $container->get('dependency-2')
        );
    };

.. hint:: All occurrences of callables are collected and replaced by an decorator class. This wrapper is responsible
   to delegate all access from the outside to the actual frontend module. It also uses the factory to create the module.


Extending AbstractModule
------------------------

To simplify creating a new frontend module implementing the provided interface you can use the `AbstractModule`_
which itself is a subclass of `AbstractComponent`_.

There are several extension points where you can hook customize the behaviour. Some of the methods are just empty
placeholders which doesn't have to be called when being overriden.

.. glossary::

   $templateName
    The name of the template. Same as `strTemplate` in Contao

   $template
    frontend module template implementing `Template`_

   $renderInBackendMode
    If true the module is generated in the backend. Otherwise the `be_wildcard` placeholder is generated. Default is `false`.

   deserializeData(array $row)
    Method deserialize the given raw data coming form the database entry or model. You should call the parent method
    when overriding this one. Deserialization of the headline is done here.

   preCompile()
    Is an empty placeholder triggered before the template is created. It's recommend to use this method for redirects
    or non rendering related work.

   compile()
    Compile your frontend module here.

   postGenerate($buffer)
    Is triggered after the frontend module is parsed.

   generateBackendView()
    Is used to generate the backend view if $renderInBackendMode is false.

   generateBackendLink()
    Is triggered to create the backend edit link.


.. _Template: https://github.com/netzmacht/contao-toolkit/tree/develop/src/View/Template.php
.. _Component: https://github.com/netzmacht/contao-toolkit/tree/develop/src/Component/Component.php
.. _AbstractComponent: https://github.com/netzmacht/contao-toolkit/tree/develop/src/Component/AbstractComponent.php
.. _Module: https://github.com/netzmacht/contao-toolkit/tree/develop/src/Component/Module/Module.php
.. _AbstractModule: https://github.com/netzmacht/contao-toolkit/tree/develop/src/Component/Module/AbstractModule.php
