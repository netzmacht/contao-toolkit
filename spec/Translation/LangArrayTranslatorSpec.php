<?php

namespace spec\Netzmacht\Contao\Toolkit\Translation;

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\System;
use Netzmacht\Contao\Toolkit\Translation\LangArrayTranslator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Translation\TranslatorInterface as Translator;
use function set_error_handler;
use const E_USER_DEPRECATED;

final class LangArrayTranslatorSpec extends ObjectBehavior
{
    public function let(Translator $translator, ContaoFrameworkInterface $framework): void
    {
        $systemAdapter = new class(System::class) extends Adapter {
            public function loadLanguageFile() {
                $GLOBALS['TL_LANG']['bar']['foo'] = 'Foobar';
            }
        };

        $framework->initialize()->willReturn(null);
        $framework->getAdapter(System::class)->willReturn($systemAdapter);

        $this->beConstructedWith($translator, $framework);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(LangArrayTranslator::class);
    }

    public function it_bypasses_translation_if_message_domain_doesnt_start_with_contao(Translator $translator): void
    {
        $translator->trans('foo', [], 'bar', null)
            ->shouldBeCalledOnce();

        $this->trans('foo', [], 'bar');
    }

    public function it_translates_contao_messages(Translator $translator): void
    {
        $translator->trans(Argument::allOf())->shouldNotBeCalled();

        $this->trans('bar.foo', [], 'contao_bar')->shouldBe('Foobar');
    }

    public function it_adds_domain_as_fallback(Translator $translator): void
    {
        $translator->trans(Argument::allOf())->shouldNotBeCalled();

        $this->trans('foo', [], 'contao_bar')->shouldBe('Foobar');
    }

    public function it_triggers_deprecation_warning_if_domain_is_added_as_fallback(): void
    {
        $exception = new class extends \Exception {};

        set_error_handler(
            function (int $errno , string $errstr) use ($exception) {
                throw $exception;
            },
            E_USER_DEPRECATED
        );

        $this->shouldThrow($exception)->during('trans', ['foo', [], 'contao_bar']);

        restore_error_handler();
    }
}
