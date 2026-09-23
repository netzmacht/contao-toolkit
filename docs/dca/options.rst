Options
=======

Options callbacks often transform a model collection or a database result into an array of options.
``Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder`` simplifies this and supports grouping and tree structures.


Create options
--------------

The builder provides three named constructors. Each of them expects the source, the label column and the value column
(default ``id``). If no label column is given, the value column is used as label.

.. glossary::

   OptionsBuilder::fromCollection(?Collection $collection, $labelColumn = null, $valueColumn = 'id')
    Create options from a Contao model collection. ``null`` is accepted and results in empty options.

   OptionsBuilder::fromResult(?Result $result, $labelColumn = null, $valueColumn = 'id')
    Create options from a legacy ``Contao\Database\Result``. ``null`` is accepted and results in empty options.

   OptionsBuilder::fromArrayList(array $data, $labelKey = null, $valueKey = 'id')
    Create options from a list of associative arrays, e.g. the result of Doctrine's ``fetchAllAssociative()``.

The label can also be a callable. It gets the current row as array passed and has to return the label.

.. code-block:: php

   <?php

   declare(strict_types=1);

   use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
   use Contao\PageModel;
   use Doctrine\DBAL\Connection;
   use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;

   final class ExampleOptionsListener
   {
       public function __construct(private readonly Connection $connection)
       {
       }

       #[AsCallback(table: 'tl_example', target: 'fields.page.options')]
       public function pageOptions(): array
       {
           return OptionsBuilder::fromCollection(PageModel::findAll(), 'title')->getOptions();
       }

       #[AsCallback(table: 'tl_example', target: 'fields.category.options')]
       public function categoryOptions(): array
       {
           $rows = $this->connection->fetchAllAssociative('SELECT id, title, alias FROM tl_category');

           return OptionsBuilder::fromArrayList(
               $rows,
               static fn (array $row): string => sprintf('%s [%s]', $row['title'], $row['alias']),
           )->getOptions();
       }
   }


Group options
-------------

``groupBy($column, ?callable $callback = null)`` groups the options by the value of a column. The optional callback
gets the column value and the row passed and returns the group label.

.. code-block:: php

   <?php

   $options = OptionsBuilder::fromArrayList($rows, 'title')
       ->groupBy('type', static fn (string $type): string => $GLOBALS['TL_LANG']['tl_example']['types'][$type])
       ->getOptions();

   // ['Group A' => [1 => 'Title 1', 3 => 'Title 3'], 'Group B' => [2 => 'Title 2']]


Options as tree
---------------

``asTree($parent = 'pid', $indentBy = '-- ')`` sorts the options hierarchically based on the parent column. Child
labels are indented by repeating ``$indentBy`` per level. The tree starts with the entries having the parent value
``0``.

.. code-block:: php

   <?php

   $options = OptionsBuilder::fromArrayList($rows, 'title')
       ->asTree()
       ->getOptions();

   // [1 => ' Root', 2 => '--  Child', 3 => '-- --  Grandchild']


Custom options
--------------

The builder works on an ``Netzmacht\Contao\Toolkit\Dca\Options\Options`` instance. Toolkit ships ``ArrayOptions``,
``ArrayListOptions`` and ``CollectionOptions``. You can implement the interface for other data sources and pass it to
the constructor of the ``OptionsBuilder``.
