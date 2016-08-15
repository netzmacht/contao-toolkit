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
 * Class ModuleDecorator.
 *
 * @package Netzmacht\Contao\Toolkit\Component\ContentElement
 */
class ModuleDecorator extends \ContentElement
{
    use ComponentDecoratorTrait;

    /**
     * Get the content element factory.
     *
     * @param \ModuleModel $model Component model.
     *
     * @return callable
     * @throws ModuleNotFound If module is not registered as service.
     */
    protected function getFactory($model)
    {
        $container   = $this->getContainer();
        $factoryName = 'contao.modules.' . $model->type;

        if (!$container->has($factoryName)) {
            throw ModuleNotFound::forModel($model);
        }

        return $container->get($factoryName);
    }
}
