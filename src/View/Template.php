<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View;

use Netzmacht\Contao\Toolkit\View\Template\Exception\HelperNotFound;

/**
 * Interface describes the templates being used in the toolkit.
 *
 * @package Netzmacht\Contao\Toolkit\View
 */
interface Template
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
    public function get(string $name);

    /**
     * Set a template var.
     *
     * @param string $name  The template var name.
     * @param mixed  $value The value.
     *
     * @return $this
     */
    public function set(string $name, $value): self;

    /**
     * Get an helper.
     *
     * @param string $name Name of the helper.
     *
     * @return mixed
     * @throws HelperNotFound If helper not exists.
     */
    public function helper(string $name);

    /**
     * Get all the template data.
     *
     * @return array
     */
    public function getData();

    /**
     * Set all the template data.
     *
     * @param array $data Template data.
     *
     * @return void
     */
    public function setData($data);
}
