<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\DependencyInjection\Exception;

use Interop\Container\Exception\ContainerException as InteropContainerException;

/**
 * Class ContainerException.
 *
 * @package Netzmacht\Contao\Toolkit\DependencyInjection\Exception
 */
class ContainerException extends \Exception implements InteropContainerException
{
}
