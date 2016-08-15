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

/**
 * Class ContentElementDecorator
 *
 * @package Netzmacht\Contao\Toolkit\Component\ContentElement
 */
class ContentElementDecorator extends \ContentElement
{
    use ComponentDecoratorTrait;

    /**
     * Get the content element factory.
     *
     * @param \ContentModel $model Component model.
     *
     * @return callable
     * @throws ContentElementNotFound
     */
    protected function getFactory($model)
    {
        $container   = $this->getContainer();
        $factoryName = 'elements.factory.' . $model->type;

        if (!$container->has($factoryName)) {
            throw ContentElementNotFound::forModel($model);
        }

        $factory = $container->get($factoryName);

        return $factory;
    }
}
