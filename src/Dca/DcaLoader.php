<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;

/**
 * Class Loader loads the data container.
 *
 * Internal used loader class. Do not used it by your own.
 *
 * @package Netzmacht\Contao\DevTools\Dca
 */
final class DcaLoader extends Controller
{
    /**
     * Construct.
     *
     * @param ContaoFrameworkInterface $framework Contao framework interface.
     */
    public function __construct(ContaoFrameworkInterface $framework)
    {
        $framework->initialize();

        parent::__construct();
    }
}
