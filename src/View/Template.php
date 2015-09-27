<?php

/**
 * @package    toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\View;

use ContaoCommunityAlliance\Translator\TranslatorInterface;

/**
 * Interface describes the templates being used in the toolkit.
 *
 * @package Netzmacht\Contao\Toolkit\View
 */
interface Template extends TranslatorInterface
{
    /**
     * Parse the template.
     *
     * @return string
     */
    public function parse();

    /**
     * Get a template value.
     *
     * @param string $name The name.
     *
     * @return mixed
     */
    public function get($name);

    /**
     * Set a template var.
     *
     * @param string $name  The template var name.
     * @param mixed  $value The value.
     *
     * @return $this
     */
    public function set($name, $value);

    /**
     * Get the assets manager.
     *
     * @return AssetsManager
     */
    public function getAssetsManager();
}
