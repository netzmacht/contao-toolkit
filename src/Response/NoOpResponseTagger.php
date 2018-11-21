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

namespace Netzmacht\Contao\Toolkit\Response;

/**
 * Class NoOpResponseTagger is there for BC reasons. It's used if Contao < 4.6 is used.
 */
final class NoOpResponseTagger implements ResponseTagger
{
    /**
     * {@inheritdoc}
     */
    public function addTags(array $tags): void
    {
        // Do nothing. It's the NoOpResponseTagger
    }
}
