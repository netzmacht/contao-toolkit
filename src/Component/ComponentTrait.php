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
use Netzmacht\Contao\Toolkit\View\FrontendTemplate;
use Netzmacht\Contao\Toolkit\View\Template;

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
        $this->Template = new FrontendTemplate($this->strTemplate);
        $this->Template->setData($this->arrData);

        $this->preCompile();
        $this->render($this->Template);
    }

    /**
     * Pre compile the component.
     *
     * Override this method for non rendering tasks.
     *
     * @return void
     */
    protected function preCompile()
    {
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
