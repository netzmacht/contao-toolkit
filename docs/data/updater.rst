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
    Update the given columns of a record and return the saved values.

   hasUserAccess($dataContainerName, $columnName)
    Check if the current user is allowed to edit a column.


Usage of the database row updater
---------------------------------

Toolkit ships with a `database row updater`_ which updates a record the same way Contao's ``DC_Table`` driver does
when saving or toggling a record. It's provided as service :code:`netzmacht.contao_toolkit.data.database_row_updater`.

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\DataContainer;
   use Netzmacht\Contao\Toolkit\Data\Updater\Updater;

   final class PublishService
   {
       public function __construct(private readonly Updater $updater)
       {
       }

       public function publish(int $recordId, DataContainer $dataContainer): mixed
       {
           return $this->updater->update('tl_example', $recordId, ['published' => '1'], $dataContainer);
       }
   }

.. code-block:: yaml

   # config/services.yaml
   services:
       App\PublishService:
           arguments:
               - '@netzmacht.contao_toolkit.data.database_row_updater'

The context is passed to the callbacks. Usually an instance of ``Contao\DataContainer`` is used.


What happens on update
~~~~~~~~~~~~~~~~~~~~~~

#. **Access checks**: an ``AccessDenied`` exception is thrown if

   * the data container is ``notEditable``,
   * the user has no access to one of the given fields (see `Field access`_),
   * the record does not exist or
   * the ``contao_dc.<table>`` permission is denied for the ``ReadAction`` of the record.

#. **Versioning** is initialized if ``config.enableVersioning`` is set.
#. **Each value** is prepared:

   * Fields with ``eval.multiple`` and ``eval.csv`` are imploded by the csv delimiter.
   * The ``save_callback`` callbacks are triggered with the value and the context.
   * For ``eval.unique`` fields an ``InvalidArgumentException`` is thrown if the value already exists.
   * Empty values of ``eval.doNotSaveEmpty`` fields are skipped.
   * Unchanged values are skipped unless ``eval.alwaysSave`` is set.
   * Empty values are converted to the empty value of the column type (e.g. ``0`` for integer or ``NULL`` for
     nullable columns).

#. If any value is left, ``tstamp`` is set and the ``onbeforesubmit_callback`` callbacks are triggered with the
   values and the context. They have to return the values.
#. The ``contao_dc.<table>`` permission is checked for the ``UpdateAction``.
#. **The values are saved**: virtual fields are combined into their storage field, other values of ``eval.fallback``
   fields are reset and array values are serialized (except for ``json`` columns).
#. If the record was changed, the cache tags of the record and its parent are invalidated (including the
   ``oninvalidate_cache_tags_callback`` if the context is a ``DataContainer``) and a new version is created.
#. The ``onsubmit_callback`` callbacks are triggered with the context.
#. The saved values are returned.

.. note:: The updater expects raw values. Unlike the ``DC_Table`` driver, formatted date values of ``date``,
   ``time`` and ``datim`` fields are not converted into timestamps.


Field access
~~~~~~~~~~~~

``hasUserAccess()`` follows Contao's ``DataContainer::isFieldExcluded()``. A field is excluded if ``exclude`` is set to
true or – without an explicit ``exclude`` setting – if it has an ``inputType``. Only for excluded fields the
permission ``ContaoCorePermissions::USER_CAN_EDIT_FIELD_OF_TABLE`` is checked.

.. _Updater: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Updater/Updater.php
.. _database row updater: https://github.com/netzmacht/contao-toolkit/blob/develop/src/Data/Updater/DatabaseRowUpdater.php
