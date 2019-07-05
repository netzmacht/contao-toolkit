Frontend module
===============

Focusing on the main goal for Toolkit there is an easy way to create lightweight decoupled frontend modules.

.. hint:: If you're developing for Contao 4.5 you might look at the `fragments support`_. If you have to stay on Contao
   LTS 4.4 you maybe want to read further.

Interface Module
----------------

The only requirement for an frontend module supported by Toolkit is that the interface
`Module`_ which extends the `Component`_ interface.

The Main difference between Contao frontend module and the interface is that the data attributes has to be accessible by
the explicit `get` and `set` methods. Magic `__get` and `__set` are not recommended.


Register a frontend module
--------------------------

Since Toolkit supports dependency injection for content elements you have to create factories which creates them. A
factory has to implement the `ComponentFactory`_ interface and must be registered as a tagged service.

Provided tags:

.. glossary::

   netzmacht.contao_toolkit.component.frontend_module_factory
    Tag your factory service with this tag so that toolkit will use it to create the elements.

   netzmacht.contao_toolkit.component.frontend_module
    Tag your factory with this tag to provide information about the supported content elements. You have to define
    the `category` and `type` attribute as well.

.. code-block:: php

    <?php

    // src/ExampleFactory.php
    class ExampleFactory implements Netzmacht\Contao\Toolkit\Component\ComponentFactory
    {
        // ...
    }

    // src/Example.php
    class Example implements Netzmacht\Contao\Toolkit\Component\Module\Module
    {
        // ...
    }

.. code-block:: yml

    // services.yml
    foo.frontend_module.factory:
        class: ExampleFactory
        tags:
            - { name: 'netzmacht.contao_toolkit.component.frontend_module', category: 'texts', type: 'example' }


.. hint:: It's possible to create multiple types with a factory. Just add multiple tags.

You don't have to register you frontend module in the `config.php`. Toolkit will do it for you.

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

   compile()
    Is called before the template data are prepared.

   prepareTemplateData(array $data)
    Prepares the data which are passed to the template.

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
.. _ComponentFactory: https://github.com/netzmacht/contao-toolkit/tree/develop/src/Component/ComponentFactory.php
