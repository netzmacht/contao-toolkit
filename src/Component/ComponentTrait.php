<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Component;

use Netzmacht\Contao\Toolkit\ServiceContainerTrait;
use Netzmacht\Contao\Toolkit\View\Template;
use Netzmacht\Contao\Toolkit\View\TemplateDecorator;

/**
 * ComponentTrait provides the toolkit way to compile components.
 *
 * @package Netzmacht\Contao\Toolkit\Component
 */
trait ComponentTrait
{
    use ServiceContainerTrait;

    /**
     * {@inheritDoc}
     */
    protected function compile()
    {
        $translator    = $this->getServiceContainer()->getTranslator();
        $assetsManager = $this->getServiceContainer()->getAssetsManager();
        $template      = new TemplateDecorator($this->Template, $translator, $assetsManager);

        $this->render($template);
    }

    /**
     * Compile the template.
     *
     * @param Template $template The template.
     *
     * @return void
     */
    abstract protected function render(Template $template);
}
