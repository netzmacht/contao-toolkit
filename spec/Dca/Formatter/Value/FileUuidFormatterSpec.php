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

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FileUuidFormatter;
use PhpSpec\ObjectBehavior;

/**
 * Class FileUuidFormatterSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
class FileUuidFormatterSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FileUuidFormatter');
    }

    public function it_is_a_value_formatter()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    public function it_accepts_file_trees()
    {
        $definition['inputType'] = 'fileTree';

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    public function it_does_not_accept_other_types()
    {
        $this->accepts('test', [])->shouldReturn(false);

        foreach (['text', 'pageTree', 'select', 'checkbox', 'textarea'] as $type) {
            $definition['inputType'] = $type;

            $this->accepts('test', $definition)->shouldReturn(false);
        }
    }

    public function it_formats_binary_uuid()
    {
        $uuid   = '31092867-468d-11e5-945d-e766493b4cce';
        $binary = StringUtil::uuidToBin($uuid);

        $this->format($binary, 'test', [])->shouldReturn($uuid);
    }

    public function it_formats_binary_uuids()
    {
        $uuids = [
            '31092867-468d-11e5-945d-e766493b4cce',
            '8a4d64cc-73f7-11e5-a93d-236cb64a50b1'
        ];

        $binary = array_map('Contao\StringUtil::uuidToBin', $uuids);

        $this->format($binary, 'test', [])->shouldReturn($uuids);
    }

    public function it_filters_empty_values()
    {
        $uuids = [
            '31092867-468d-11e5-945d-e766493b4cce',
            '8a4d64cc-73f7-11e5-a93d-236cb64a50b1'
        ];

        $value = [
            StringUtil::uuidToBin('31092867-468d-11e5-945d-e766493b4cce'),
            '',
            null,
            false,
            StringUtil::uuidToBin('8a4d64cc-73f7-11e5-a93d-236cb64a50b1')
        ];

        $this->format($value, 'test', [])->shouldReturn($uuids);
    }
}
