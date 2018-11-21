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

namespace Netzmacht\Contao\Toolkit\Component\ContentElement;

use Contao\ContentElement;
use Netzmacht\Contao\Toolkit\Component\ComponentDecoratorTrait;
use Netzmacht\Contao\Toolkit\Component\ComponentFactory;

/**
 * Class ContentElementDecorator.
 *
 * @package Netzmacht\Contao\Toolkit\Component\ContentElement
 */
final class ContentElementDecorator extends ContentElement
{
    use ComponentDecoratorTrait;

    /**
     * {@inheritDoc}
     */
    public function __construct($contentModel, string $column = 'main')
    {
        $this->component = $this->getFactory()->create($contentModel, $column);
    }

    /**
     * {@inheritDoc}
     */
    protected function getFactory(): ComponentFactory
    {
        return $this->getContainer()->get('netzmacht.contao_toolkit.component.content_element_factory');
    }
}
