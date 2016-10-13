<?php

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\EncryptedFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class EncryptedFormatterSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 * @mixin EncryptedFormatter
 */
class EncryptedFormatterSpec extends ObjectBehavior
{
    function let()
    {
        $GLOBALS['TL_CONFIG']['encryptionKey']    = 'gg889';
        $GLOBALS['TL_CONFIG']['encryptionMode']   = 'cfb';
        $GLOBALS['TL_CONFIG']['encryptionCipher'] = 'rijndael-256';

        $this->beConstructedWith(\Encryption::getInstance());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\EncryptedFormatter');
    }

    function it_is_a_value_formatter()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    function it_accepts_encryped_fields()
    {
        $definition['eval']['encrypt'] = true;

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    function it_does_not_accept_a_field_by_default()
    {
        $this->accepts('test', [])->shouldReturn(false);
    }

    function it_decrypts_encrypted_value()
    {
        $value = \Encryption::encrypt('val');
        $definition['eval']['encrypt'] = true;

        $this->format($value, 'test', $definition)->shouldReturn('val');
    }

    function it_decrypts_encrypted_array()
    {
        $value = \Encryption::encrypt(['val']);
        $definition['eval']['encrypt'] = true;

        $this->format($value, 'test', $definition)->shouldReturn(['val']);
    }
}
