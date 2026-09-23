Services and tags
=================

All services are private. Reference them by their service id, see :doc:`../installation`.


Services
--------

================================================================  ====================================================
Service id                                                        Type
================================================================  ====================================================
``netzmacht.contao_toolkit.dca.manager``                          ``Dca\DcaManager``
``netzmacht.contao_toolkit.dca.formatter.factory``                ``Dca\Formatter\FormatterFactory``
``netzmacht.contao_toolkit.callback_invoker``                     ``Callback\Invoker``
``netzmacht.contao_toolkit.repository_manager``                   ``Data\Model\RepositoryManager``
``netzmacht.contao_toolkit.data.database_row_updater``            ``Data\Updater\Updater``
``netzmacht.contao_toolkit.template_renderer``                    ``View\Template\TemplateRenderer``
``netzmacht.contao_toolkit.assets_manager``                       ``View\Assets\AssetsManager``
``netzmacht.contao_toolkit.csrf.token_provider``                  ``Security\Csrf\CsrfTokenProvider``
``netzmacht.contao_toolkit.contao_services_factory``              ``DependencyInjection\ContaoServicesFactory``
``netzmacht.contao_toolkit.contao.*_adapter``                     ``Contao\CoreBundle\Framework\Adapter``, see
                                                                  :doc:`../dependency-injection/contao-services-factory`
================================================================  ====================================================

All types are relative to the ``Netzmacht\Contao\Toolkit`` namespace.


DCA listeners
-------------

=================================================================  ===================================================
Service id                                                         Class
=================================================================  ===================================================
``netzmacht.contao_toolkit.dca.listeners.slug_alias_generator``    ``Dca\Listener\Save\SlugAliasListener``
``netzmacht.contao_toolkit.dca.listeners.popup_wizard``            ``Dca\Listener\Wizard\PopupWizardListener``
``netzmacht.contao_toolkit.dca.listeners.template_options``        ``Dca\Listener\Options\TemplateOptionsListener``
=================================================================  ===================================================

The listeners are also registered with their class name as service id, so they can be used as callback directly.
See :doc:`../dca/callbacks`.


Tags
----

.. glossary::

   netzmacht.contao_toolkit.repository
    Registers a custom repository. The ``model`` attribute is required. See :doc:`../data/repositories`.

   netzmacht.contao_toolkit.dca.formatter
    Adds a value formatter to the formatter chain. See :doc:`../dca/formatter`.

   netzmacht.contao_toolkit.dca.formatter.pre_filter
    Adds a value formatter as pre filter. See :doc:`../dca/formatter`.

   netzmacht.contao_toolkit.dca.formatter.post_filter
    Adds a value formatter as post filter. See :doc:`../dca/formatter`.


Events
------

.. glossary::

   netzmacht.contao_toolkit.dca.create_formatter
    ``Dca\Formatter\Event\CreateFormatterEvent`` is dispatched when a formatter is created. See
    :doc:`../dca/formatter`.
