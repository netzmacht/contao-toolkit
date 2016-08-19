<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Component\ContentElement;

use Netzmacht\Contao\Toolkit\Component\ComponentDecoratorTrait;
use Netzmacht\Contao\Toolkit\DependencyInjection\Services;

/**
 * Class ContentElementDecorator.
 *
 * @package Netzmacht\Contao\Toolkit\Component\ContentElement
 */
final class ContentElementDecorator extends \ContentElement
{
    use ComponentDecoratorTrait;

    /**
     * {@inheritDoc}
     */
    protected function getFactory()
    {
        return $this->getContainer()->get(Services::CONTENT_ELEMENT_FACTORY);
    }
}
