<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Response;

use Override;

/**
 * Class NoOpResponseTagger is there for BC reasons. It's used if Contao < 4.6 is used.
 */
final class NoOpResponseTagger implements ResponseTagger
{
    /** {@inheritDoc} */
    #[Override]
    public function addTags(array $tags): void
    {
        // Do nothing. It's the NoOpResponseTagger
    }
}
