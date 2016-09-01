Updater
=======

The data container definitions (dca) of Contao provides flexible, extendible data structures. One goal of Toolkit is to
provide these features outside the internal data container drivers of Contao.

Interface Updater
-----------------

Like the most tools of Toolkit the updater also depends on an interface. It's described as the `Updater`_.


Usage of the database row updater
---------------------------------

Toolkit ships with a `database row updater`_ supporting versioning and data callbacks are supported. It's
provided as a service.

.. code-block:: php

    <?php
    use Netzmacht\Contao\Toolkit\DependencyInjection\Services;

    /** @var Interop\Container\ContainerInterface
    $container;

    // Context object is passed to the save callback. Usually an instance of \DataContainer is passed here.
    $context = ...;

    $updater = $container->get(Services::DATABASE_ROW_UPDATER);
    $data    = ['name' => 'New Name'];

    // Following this are done:
    // 1. Check if user has access
    // 2. Check if versioning is supported. Initialize if supported
    // 3. Execute the save callbacks
    // 4. Save the entry recognizing alwaysSave and doNotSaveEmpty setting
    // 5. Create a new version if versioning is supported.
    // 6. Return the saved data.
    $savedData = $updater->update('my_table', ID, $data, $context);

.. _Updater: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Updater/Updater.php
.. _database row updater: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Updater/DatabaseRowUpdater.php
