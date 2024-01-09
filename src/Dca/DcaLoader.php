<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;

/**
 * Class Loader loads the data container.
 *
 * Internal used loader class. Do not use it by your own.
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class DcaLoader extends Controller
{
    /**
     * Construct.
     *
     * @param ContaoFramework $framework Contao framework interface.
     */
    public function __construct(ContaoFramework $framework)
    {
        $framework->initialize();

        parent::__construct();
    }
}
