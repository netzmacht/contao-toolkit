<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\DependencyInjection\Exception;

use Interop\Container\Exception\NotFoundException as InteropNotFoundException;

/**
 * ServiceNotFound exception is thrown when the Container can't locate a service.
 *
 * @package Netzmacht\Contao\Toolkit\DependencyInjection\Exception
 */
class ServiceNotFound extends ContainerException implements InteropNotFoundException
{
}
