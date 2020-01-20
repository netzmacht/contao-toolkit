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

namespace spec\Netzmacht\Contao\Toolkit\Response;

use FOS\HttpCache\Exception\InvalidTagException as FosInvalidTagException;
use FOS\HttpCache\ResponseTagger as FosResponseTagger;
use Netzmacht\Contao\Toolkit\Exception\InvalidHttpResponseTagException;
use Netzmacht\Contao\Toolkit\Response\FosCacheResponseTagger;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use PhpSpec\ObjectBehavior;

final class FosCacheResponseTaggerSpec extends ObjectBehavior
{
    public function let(FosResponseTagger $responseTagger): void
    {
        $this->beConstructedWith($responseTagger);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(FosCacheResponseTagger::class);
    }

    public function it_is_a_response_tagger(): void
    {
        $this->shouldImplement(ResponseTagger::class);
    }

    public function it_delegates_tags(FosResponseTagger $responseTagger): void
    {
        $tags = ['foo', 'bar'];

        $responseTagger->addTags($tags)->shouldBeCalledOnce();

        $this->addTags($tags);
    }

    public function it_throws_invalid_response_tag_exception_if_fos_response_tagger_failed(
        FosResponseTagger $responseTagger
    ): void {
        $tags = ['foo', 'bar'];

        $responseTagger->addTags($tags)
            ->shouldBeCalled()
            ->willThrow(new FosInvalidTagException());

        $this->shouldThrow(InvalidHttpResponseTagException::class)->during('addTags', [$tags]);
    }
}
