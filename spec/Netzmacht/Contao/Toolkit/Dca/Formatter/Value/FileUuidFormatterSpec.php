<?php

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Contao\String;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FileUuidFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class FileUuidFormatterSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 * @mixin FileUuidFormatter
 */
class FileUuidFormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FileUuidFormatter');
    }

    function it_is_a_value_formatter()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    function it_accepts_file_trees()
    {
        $definition['inputType'] = 'fileTree';

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    function it_does_not_accept_other_types()
    {
        $this->accepts('test', [])->shouldReturn(false);

        foreach (['text', 'pageTree', 'select', 'checkbox', 'textarea'] as $type) {
            $definition['inputType'] = $type;

            $this->accepts('test', $definition)->shouldReturn(false);
        }
    }

    function it_formats_binary_uuid()
    {
        $uuid   = '31092867-468d-11e5-945d-e766493b4cce';
        $binary = String::uuidToBin($uuid);

        $this->format($binary, 'test', [])->shouldReturn($uuid);
    }

    function it_formats_binary_uuids()
    {
        $uuids = [
            '31092867-468d-11e5-945d-e766493b4cce',
            '8a4d64cc-73f7-11e5-a93d-236cb64a50b1'
        ];

        $binary = array_map('Contao\String::uuidToBin', $uuids);

        $this->format($binary, 'test', [])->shouldReturn($uuids);
    }

    function it_filters_empty_values()
    {
        $uuids = [
            '31092867-468d-11e5-945d-e766493b4cce',
            '8a4d64cc-73f7-11e5-a93d-236cb64a50b1'
        ];

        $value = [
            String::uuidToBin('31092867-468d-11e5-945d-e766493b4cce'),
            '',
            null,
            false,
            String::uuidToBin('8a4d64cc-73f7-11e5-a93d-236cb64a50b1')
        ];

        $this->format($value, 'test', [])->shouldReturn($uuids);
    }
}
