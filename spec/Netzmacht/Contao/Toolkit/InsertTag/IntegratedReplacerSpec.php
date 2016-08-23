<?php

namespace spec\Netzmacht\Contao\Toolkit\InsertTag;

use Netzmacht\Contao\Toolkit\InsertTag\IntegratedReplacer;
use Netzmacht\Contao\Toolkit\InsertTag\Parser;
use PhpSpec\ObjectBehavior;

/**
 * Class IntegratedReplacerSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\InsertTag
 * @mixin IntegratedReplacer
 */
class IntegratedReplacerSpec extends ObjectBehavior
{
    function let()
    {
        $insertTags = new InsertTagsMock();
        $this->beConstructedWith($insertTags);

        $insertTags->register($this->getWrappedObject());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\InsertTag\IntegratedReplacer');
    }

    function it_is_a_replacer()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\InsertTag\Replacer');
    }

    public function it_supports_parsers(Parser $parser)
    {
        $this->registerParser($parser)->shouldReturn($this);

        $parser
            ->supports('test')
            ->shouldBeCalled()
            ->willReturn(true);

        $parser
            ->parse('test', 'test', null, true)
            ->shouldBeCalled();

        $this->replace('test');
    }

    public function it_provides_method_for_replace_insert_tags_hook(Parser $parser)
    {
        $this->registerParser($parser)->shouldReturn($this);

        $parser
            ->supports('test')
            ->shouldBeCalled()
            ->willReturn(true);

        $parser
            ->parse('test', 'test', null, true)
            ->shouldBeCalled();

        $this->replaceTag('test');
    }
}

class InsertTagsMock
{
    /**
     * @var IntegratedReplacer
     */
    private $replacer;

    public function register(IntegratedReplacer $replacer)
    {
        $this->replacer = $replacer;
    }

    public function replace($buffer, $cache = true)
    {
        return $this->replacer->replaceTag($buffer, $cache);
    }
}
