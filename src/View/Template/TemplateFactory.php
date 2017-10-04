<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Toolkit\View\Template;

use Netzmacht\Contao\Toolkit\View\Template;

/**
 * TemplateFactory creates a template with some predefined helpers.
 */
interface TemplateFactory
{
    /**
     * Create a frontend template.
     *
     * @param string     $name        Template name.
     * @param array|null $data        Template data.
     * @param string     $contentType Content type.
     *
     * @return Template
     */
    public function createFrontendTemplate($name, array $data = null, $contentType = 'text/html');

    /**
     * Create a backend template.
     *
     * @param string     $name        Template name.
     * @param array|null $data        Template data.
     * @param string     $contentType Content type.
     *
     * @return Template
     */
    public function createBackendTemplate($name, array $data = null, $contentType = 'text/html');
}
