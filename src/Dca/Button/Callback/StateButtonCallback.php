<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Button\Callback;

use Contao\Backend;
use Contao\Controller;
use Contao\Database;
use Contao\Image;
use Contao\Input;
use Contao\System;
use Contao\User;
use Netzmacht\Contao\Toolkit\Data\State\StateToggler;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Exception\AccessDeniedException;

/**
 * StateButtonCallback creates the state toggle button known in Contao.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Button\Callback
 */
class StateButtonCallback
{
    /**
     * Contao user.
     *
     * @var User
     */
    private $user;

    /**
     * The input.
     *
     * @var Input
     */
    private $input;

    /**
     * The database connection.
     *
     * @var Database
     */
    private $database;

    /**
     * Data container definition.
     *
     * @var Definition
     */
    private $definition;

    /**
     * Button name.
     *
     * @var string
     */
    private $buttonName;

    /**
     * StateButtonCallback constructor.
     *
     * @param User       $user       Contao user object.
     * @param Input      $input      Request Input.
     * @param Database   $database   Database connection.
     * @param Definition $definition Data container definition.
     * @param string     $buttonName Button name.
     */
    public function __construct(User $user, Input $input, Database $database, Definition $definition, $buttonName)
    {
        $this->user       = $user;
        $this->input      = $input;
        $this->database   = $database;
        $this->definition = $definition;
        $this->buttonName = $buttonName;
        $this->config     = $this->definition->get(
            ['list', 'operations', $this->buttonName, 'toolkit', 'state_button'],
            []
        );

        $this->toggler = $this->createStateToggler();
    }

    /**
     * Craete the state toggler.
     *
     * @return StateToggler
     * @throws \RuntimeException When no state column is defined.
     */
    private function createStateToggler()
    {
        if (!isset($this->config['column'])) {
            throw new \RuntimeException(
                sprintf('No state column defined for state toggle button "%s"', $this->buttonName)
            );
        }

        return new StateToggler($this->user, $this->database, $this->definition, $this->config['column']);
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
            try {
                $this->toggler->toggle($this->input->get('tid'), ($this->input->get('state') == 1), $this);
                Controller::redirect(Controller::getReferer());
            } catch (AccessDeniedException $e) {
                System::log($e->getMessage(), __METHOD__, TL_ERROR);
                Controller::redirect('contao/main.php?act=error');
            }
        }


        if (!$this->toggler->hasUserAccess()) {
            return '';
        }

        $href .= '&amp;id='.$this->input->get('id').'&amp;tid='.$row['id'].'&amp;state='.$row[''];

        if (!$row[$this->config['column']] || ($this->config['inverse'] && $row[$this->config['column']])) {
            $icon = $this->disableIcon($icon);
        }

        return sprintf(
            '<a href="%s" title="%s"%s>%s</a> ',
            Backend::addToUrl($href),
            specialchars($title),
            $attributes,
            Image::getHtml($icon, $label)
        );
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
        if (isset($this->config['disabledIcon'])) {
            return $this->config['disabledIcon'];
        }

        if ($icon === 'visible.gif') {
            return 'invisible.gif';
        }

        return preg_replace('\.([^\.]*)$', '._$1', $icon);
    }
}
