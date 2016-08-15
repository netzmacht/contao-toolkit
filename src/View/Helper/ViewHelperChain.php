<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\View\Helper;

use Netzmacht\Contao\Toolkit\View\ViewHelper;

/**
 * Class TemplateHelper.
 *
 * @package Netzmacht\Contao\Toolkit\View
 */
final class ViewHelperChain implements ViewHelper
{
    /**
     * Created helper instances.
     *
     * @var array
     */
    private $helpers = [];

    /**
     * TemplateHelper constructor.
     *
     * @param ViewHelper[] $helpers View helpers.
     */
    public function __construct(array $helpers = [])
    {
        $this->helpers = $helpers;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($method)
    {
        foreach ($this->helpers as $helper) {
            if ($helper->supports($method)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    function __call($name, array $arguments)
    {
        foreach ($this->helpers as $helper) {
            if ($helper->supports($name)) {
                return $helper->__call($name, $arguments);
            }
        }

        throw new HelperNotFound($name);
    }
}
