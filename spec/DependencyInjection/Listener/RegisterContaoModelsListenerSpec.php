<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\DependencyInjection\Listener;

use Netzmacht\Contao\Toolkit\DependencyInjection\Listener\RegisterContaoModelsListener;
use PhpSpec\ObjectBehavior;

use function expect;

final class RegisterContaoModelsListenerSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(['foo' => 'Bar']);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RegisterContaoModelsListener::class);
    }

    public function it_registers_models_in_global_registry(): void
    {
        $GLOBALS['TL_MODELS'] = ($GLOBALS['TL_MODELS'] ?? []);

        expect($GLOBALS['TL_MODELS'])->shouldNotHaveKeyWithValue('foo', 'bar');

        $this->onInitializeSystem();

        expect($GLOBALS['TL_MODELS'])->shouldHaveKeyWithValue('foo', 'Bar');
    }

    public function it_overrides_existing_configuration(): void
    {
        $GLOBALS['TL_MODELS']        = ($GLOBALS['TL_MODELS'] ?? []);
        $GLOBALS['TL_MODELS']['foo'] = 'Baz';

        expect($GLOBALS['TL_MODELS'])->shouldHaveKeyWithValue('foo', 'Baz');

        $this->onInitializeSystem();

        expect($GLOBALS['TL_MODELS'])->shouldHaveKeyWithValue('foo', 'Bar');
    }
}
