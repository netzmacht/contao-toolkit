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

namespace spec\Netzmacht\Contao\Toolkit\View\Template;

use Netzmacht\Contao\Toolkit\View\Template\TemplateReference;
use Netzmacht\Contao\Toolkit\View\Template\ToolkitTemplateNameParser;
use PhpSpec\ObjectBehavior;

use function assert;

class ToolkitTemplateNameParserSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ToolkitTemplateNameParser::class);
    }

    public function it_parses_short_frontend_template_name(): void
    {
        $reference = $this->parse('fe:bar');
        assert($reference instanceof TemplateReference);
        $reference->getLogicalName()->shouldBe('bar');

        $reference->all()->shouldReturn(
            [
                'name' => 'bar',
                'engine' => 'toolkit',
                'format' => 'html5',
                'scope' => 'fe',
                'contentType' => 'text/html',
            ]
        );
    }

    public function it_parses_short_backend_template_name(): void
    {
        $reference = $this->parse('be:bar');
        assert($reference instanceof TemplateReference);
        $reference->getLogicalName()->shouldBe('bar');

        $reference->all()->shouldReturn(
            [
                'name' => 'bar',
                'engine' => 'toolkit',
                'format' => 'html5',
                'scope' => 'be',
                'contentType' => 'text/html',
            ]
        );
    }
}
