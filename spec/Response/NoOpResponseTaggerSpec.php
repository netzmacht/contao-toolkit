<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Response;

use Netzmacht\Contao\Toolkit\Response\NoOpResponseTagger;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use PhpSpec\ObjectBehavior;

final class NoOpResponseTaggerSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(NoOpResponseTagger::class);
    }

    public function it_is_a_response_tagger(): void
    {
        $this->shouldImplement(ResponseTagger::class);
    }
}
