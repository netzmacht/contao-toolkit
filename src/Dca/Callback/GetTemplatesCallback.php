<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Callback;

use Contao\Controller;
use Contao\DataContainer;

/**
 * Class GetTemplatesCallback gets a set of templates.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Callback
 */
trait GetTemplatesCallback
{
    /**
     * Get templates.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    public function getTemplates($dataContainer)
    {
        $config    = $this->getDefinition()->get(['fields', $dataContainer->field, 'toolkit', 'get_templates']);
        $prefix    = empty($config['prefix']) ? '' : $config['prefix'];
        $templates = Controller::getTemplateGroup($prefix);

        if (empty($config['exclude'])) {
            return $templates;
        }

        return array_diff($templates, $config['exclude']);
    }
}
