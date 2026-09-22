Auto-registered field callbacks
=================================

As of 4.1.0, setting a `fields.<field>.toolkit.<key>` configuration for one of the three
remaining toolkit DCA listeners automatically registers the matching Contao callback — you no
longer have to also wire `options_callback`/`save_callback`/`wizard` manually.

============================  =================  ===============================================
Config key                    DCA slot           Listener (method)
============================  =================  ===============================================
`toolkit.template_options`    `options_callback` `TemplateOptionsListener::onOptionsCallback`
`toolkit.alias_generator`     `save_callback`    `SlugAliasListener::onSaveCallback`
`toolkit.popup_wizard`        `wizard`           `PopupWizardListener::onWizardCallback`
============================  =================  ===============================================

.. code-block:: php

   <?php

    // This alone is now enough - no manual save_callback registration needed:
    $GLOBALS['TL_DCA']['tl_example']['fields']['alias']['toolkit']['alias_generator'] = [
        'fields' => ['title'],
    ];

Manually registering a callback remains a fully supported, explicit override:

- For `options_callback` (a single `[class, method]` pair in Contao), a manually set callback
  always wins — auto-registration never overwrites it.
- For `save_callback`/`wizard` (arrays of `[class, method]` pairs in Contao), the toolkit callback
  is appended alongside any manually registered ones, without duplicating itself across repeated
  `loadDataContainer` invocations.

This auto-registration only applies to the three listeners above — the deprecated
`GenerateAliasListener`, `StateButtonCallbackListener`, `ColorPickerListener`, `FilePickerListener`
and `PagePickerListener` are intentionally excluded (see :doc:`callbacks`), as is the generic
`AbstractPickerListener`/`AbstractFieldPickerListener` infrastructure (no fixed service/method to
tag).
