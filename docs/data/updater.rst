Updater
=======

The data container definitions (dca) of Contao provides flexible, extendible data structures. One goal of Toolkit is to
provide these features outside the internal data container drivers of Contao.

Interface Updater
-----------------

Like the most tools of Toolkit the updater also depends on an interface. It's described as the `Updater`_ and provides
two methods:

.. glossary::

   update($dataContainerName, $recordId, $data, $context)
    Update the given columns of a record and return the saved data.

   hasUserAccess($dataContainerName, $columnName)
    Check if the current user is allowed to edit a column.


Usage of the database row updater
---------------------------------

Toolkit ships with a `database row updater`_ supporting permission checks, versioning and save callbacks. It's
provided as service :code:`netzmacht.contao_toolkit.data.database_row_updater`.

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\DataContainer;
   use Netzmacht\Contao\Toolkit\Data\Updater\Updater;

   final class ToggleService
   {
       public function __construct(private readonly Updater $updater)
       {
       }

       public function publish(int $recordId, DataContainer $dataContainer): mixed
       {
           // Following steps are done:
           // 1. Check if the user has access to each column. Throws an AccessDenied exception otherwise.
           // 2. Initialize the versioning if enableVersioning is set for the data container.
           // 3. Execute the save callbacks of each column. The context is passed as second argument.
           // 4. Save the data. The tstamp column is updated if it exists and isn't part of the data.
           // 5. Create a new version if versioning is enabled.
           // 6. Return the saved data.
           return $this->updater->update('tl_example', $recordId, ['published' => '1'], $dataContainer);
       }
   }

.. code-block:: yaml

   # config/services.yaml
   services:
       App\ToggleService:
           arguments:
               - '@netzmacht.contao_toolkit.data.database_row_updater'

The context is passed to the save callbacks. Usually an instance of ``Contao\DataContainer`` is used.

.. note::

   ``DatabaseRowUpdater::hasUserAccess()`` checks permissions via Symfony's
   ``Security::isGranted(ContaoCorePermissions::USER_CAN_EDIT_FIELD_OF_TABLE, ...)``. If the user is not allowed to
   edit one of the columns, a ``Netzmacht\Contao\Toolkit\Exception\AccessDenied`` exception is thrown.

.. _Updater: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Updater/Updater.php
.. _database row updater: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Updater/DatabaseRowUpdater.php
