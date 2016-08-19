<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\DependencyInjection;

use Interop\Container\ContainerInterface;
use Netzmacht\Contao\Toolkit\DependencyInjection\Exception\ContainerException;
use Netzmacht\Contao\Toolkit\DependencyInjection\Exception\ServiceNotFound;

/**
 * Class PimpleAdapter.
 *
 * @package Netzmacht\Contao\Toolkit\DependencyInjection
 */
class PimpleAdapter implements ContainerInterface
{
    /**
     * Pimple adapter.
     *
     * @var \Pimple
     */
    private $pimple;

    /**
     * PimpleAdapter constructor.
     *
     * @param \Pimple $pimple Pimple container.
     */
    public function __construct(\Pimple $pimple)
    {
        $this->pimple = $pimple;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        try {
            return $this->pimple[$id];
        } catch (\InvalidArgumentException $previous) {
            throw new ServiceNotFound(sprintf('Service with id "%s" not found.', $id), $previous->getCode(), $previous);
        } catch (\Exception $previous) {
            throw new ContainerException('', $previous->getCode(), $previous);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return isset($this->pimple[$id]);
    }
}
