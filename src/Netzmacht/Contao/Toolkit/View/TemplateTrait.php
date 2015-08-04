<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\View;

/**
 * Trait extends the default Contao template classes.
 *
 * @package Netzmacht\Contao\Toolkit\View
 */
trait TemplateTrait
{
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
}
