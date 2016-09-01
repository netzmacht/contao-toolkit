Content element
===============

Focusing on the main goal for Toolkit there is an easy way to create lightweight decoupled content elements.

Interface ContentElement
------------------------

The only requirement for an content element supported by Toolkit is that the interface
`ContentElement`_ is implemented. It extends the `Component`_ interface.

The Main difference between Contao content element and the interface is that the data attributes has to be accessible by
the explicit `get` and `set` methods. Magic `__get` and `__set` are not recommended.


Register a content element
--------------------------

Since Toolkit supports dependency injection for content elements you have to register your content element by
configuring a callable as factory. The callable has to support at least two arguments. The third one is optional.

.. glossary::

   $model
    Content element model or database result

   $column
    The column in which the element is created. Default is `main`

   $container
    The dependency container instance of `ContainerInterop\\ContainerInterface`

.. code-block:: php

    // src/Example.php
    class Example implements Netzmacht\Contao\Toolkit\Component\ContentElement\ContentElement
    {
        // ...
    }

    // config.php
    $GLOBALS['TL_CTE']['text']['example'] = function ($model, $column, ContainerInterop\ContainerInterface $container) {
        return new Example(
            $model,
            $column,
            $container->get('dependency-1'),
            $container->get('dependency-2')
        );
    };

.. hint:: All occurrences of callables are collected and replaced by an decorator class. This wrapper is responsible
   to delegate all access from the outside to the actual content element. It also uses the factory to create the content
   element.


Extending AbstractContentElement
--------------------------------

To simplify creating a new content element implementing the provided interface you can use the `AbstractContentElement`_
which itself is a subclass of `AbstractComponent`_.

There are several extension points where you can hook customize the behaviour. Some of the methods are just empty
placeholders which doesn't have to be called when being overriden.

.. glossary::

   $templateName
    The name of the template. Same as `strTemplate` in Contao

   $template
    Content element template implementing `Template`_

   deserializeData(array $row)
    Method deserialize the given raw data coming form the database entry or model. You should call the parent method
    when overriding this one. Deserialization of the headline is done here.

   isVisible()
    Is called to decide if content element should be generated. You should call the parent to keep the default behaviour.

   preCompile()
    Is an empty placeholder triggered before the template is created. It's recommend to use this method for redirects
    or non rendering related work.

   compile()
    Compile your content element here.

   postGenerate($buffer)
    Is triggered after the content element is parsed.


.. _Template: https://github.com/netzmacht/contao-toolkit/tree/develop/src/View/Template.php
.. _Component: https://github.com/netzmacht/contao-toolkit/tree/develop/src/Component/Component.php
.. _AbstractComponent: https://github.com/netzmacht/contao-toolkit/tree/develop/src/Component/AbstractComponent.php
.. _ContentElement: https://github.com/netzmacht/contao-toolkit/tree/develop/src/Component/ContentElement/ContentElement.php
.. _AbstractContentElement: https://github.com/netzmacht/contao-toolkit/tree/develop/src/Component/ContentElement/AbstractContentElement.php
