<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Callback;

/**
 * Class ToggleIconCallback is used as button_callback for state toggling icons.
 *
 * @package Netzmacht\Contao\DevTools\Dca
 */
class ToggleIconCallback extends \Controller
{
    /**
     * Contao user.
     *
     * @var \User
     */
    private $user;

    /**
     * The input.
     *
     * @var \Input
     */
    private $input;

    /**
     * The database connection.
     *
     * @var \Database
     */
    private $database;

    /**
     * Table name.
     *
     * @var string
     */
    private $table;

    /**
     * Column name.
     *
     * @var string
     */
    private $column;

    /**
     * State value is inversed.
     *
     * @var bool
     */
    private $inversed;

    /**
     * The disabled icon.
     *
     * @var string
     */
    private $disabledIcon;

    /**
     * Construct.
     *
     * @param \User       $user         The Contao user.
     * @param \Input      $input        The user input.
     * @param \Database   $database     The database connection.
     * @param string      $table        The table name.
     * @param string      $column       The column name.
     * @param bool        $inversed     State is inversed.
     * @param string|null $disabledIcon Custom disabled icon.
     */
    public function __construct(
        \User $user,
        \Input $input,
        \Database $database,
        $table,
        $column,
        $inversed = false,
        $disabledIcon = null
    ) {
        $this->user         = $user;
        $this->input        = $input;
        $this->table        = $table;
        $this->column       = $column;
        $this->inversed     = $inversed;
        $this->disabledIcon = $disabledIcon;
        $this->database     = $database;
    }

    /**
     * Invoke the callback.
     *
     * @param array  $row        Current data row.
     * @param string $href       Button link.
     * @param string $label      Button label.
     * @param string $title      Button title.
     * @param string $icon       Enabled button icon.
     * @param string $attributes Html attributes as string.
     *
     * @return string
     */
    public function __invoke($row, $href, $label, $title, $icon, $attributes)
    {
        if ($this->input->get('tid')) {
            $this->toggleVisibility($this->input->get('tid'), ($this->input->get('state') == 1));
            $this->redirect($this->getReferer());
        }

        if (!$this->hasAccess()) {
            return '';
        }

        $href .= '&amp;id='.$this->input->get('id').'&amp;tid='.$row['id'].'&amp;state='.$row[''];

        if (!$row[$this->column] || ($this->inversed && $row[$this->column])) {
            $icon = $this->disableIcon($icon);
        }

        return sprintf(
            '<a href="%s" title="%s"%s>%s</a> ',
            $this->addToUrl($href),
            specialchars($title),
            $attributes,
            \Image::getHtml($icon, $label)
        );
    }

    /**
     * Toggle the visibility state.
     *
     * @param int  $recordId Record id.
     * @param bool $newState New state.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function toggleVisibility($recordId, $newState)
    {
        if (!$this->hasAccess()) {
            $this->log(
                sprintf('Not enough permission to show/shide record ID "%s"', $recordId),
                __METHOD__,
                TL_ERROR
            );

            $this->redirect('contao/main.php?act=error');
        }

        $versions = new \Versions($this->table, $recordId);

        if (isset($GLOBALS['TL_DCA'][$this->table]['fields'][$this->column]['save_callback'])) {
            foreach ((array) $GLOBALS['TL_DCA'][$this->table]['fields'][$this->column]['save_callback'] as $callback) {
                $instance = new $callback[0];
                $instance->$callback[1]($newState, $this);
            }
        }

        $this->database
            ->prepare(sprintf('UPDATE %s %s WHERE id=?', $this->table, '%s'))
            ->set(array('tstamp' => time(), $this->column => $newState ? '1' : ''))
            ->execute($recordId);

        $versions->create();
    }

    /**
     * Disable the icon.
     *
     * @param string $icon The enabled icon.
     *
     * @return string
     */
    private function disableIcon($icon)
    {
        if ($this->disabledIcon) {
            return $this->disabledIcon;
        }

        if ($icon === 'visible.gif') {
            return 'invisible.gif';
        }

        return preg_replace('\.([^\.]*)$', '._$1', $icon);
    }

    /**
     * Check if user has access.
     *
     * @return bool
     */
    private function hasAccess()
    {
        if ($this->user instanceof \BackendUser) {
            return $this->user->hasAccess($this->table . '::' . $this->column, 'alexf');
        }

        return false;
    }
}
