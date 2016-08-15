<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\View\Template;

use Netzmacht\Contao\Toolkit\View\Helper\ViewHelper;

/**
 * Trait extends the default Contao template classes.
 *
 * @package Netzmacht\Contao\Toolkit\View
 */
trait TemplateTrait
{
    /**
     * Template helper.
     *
     * @var ViewHelper
     */
    private $helper;

    /**
     * TemplateTrait constructor.
     *
     * @param string     $name        The template name.
     * @param ViewHelper $helper      The template helper.
     * @param string     $contentType The content type.
     */
    public function __construct($name, ViewHelper $helper, $contentType = 'text/html')
    {
        parent::__construct($name, $contentType);

        $this->helper = $helper;
    }

    /**
     * Get a template value.
     *
     * @param string $name The name.
     *
     * @return mixed
     */
    public function get($name)
    {
        return $this->$name;
    }

    /**
     * Set a template var.
     *
     * @param string $name  The template var name.
     * @param mixed  $value The value.
     *
     * @return $this
     */
    public function set($name, $value)
    {
        $this->$name = $value;

        return $this;
    }

    /**
     * Get the helper.
     *
     * @return ViewHelper
     */
    public function helper()
    {
        return $this->helper;
    }

    /**
     * Shortcut to get the helper.
     *
     * @return ViewHelper
     */
    public function h()
    {
        return $this->helper;
    }

    /**
     * Insert a template.
     *
     * @param string $name The template name.
     * @param array  $data An optional data array.
     *
     * @return void
     */
    public function insert($name, array $data = null)
    {
        $template = new static($name, $this->helper, $this->strContentType);

        if ($data !== null) {
            $template->setData($data);
        }

        echo $template->parse();
    }
}
