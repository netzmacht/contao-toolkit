<?php

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
