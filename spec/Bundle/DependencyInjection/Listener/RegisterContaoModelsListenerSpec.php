<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2019 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */
namespace spec\Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Listener;

use function expect;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Listener\RegisterContaoModelsListener;
use PhpSpec\ObjectBehavior;

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
        $GLOBALS['TL_MODELS'] = $GLOBALS['TL_MODELS'] ?? [];

        expect($GLOBALS['TL_MODELS'])->shouldNotHaveKeyWithValue('foo', 'bar');

        $this->onInitializeSystem();

        expect($GLOBALS['TL_MODELS'])->shouldHaveKeyWithValue('foo', 'Bar');
    }

    public function it_overrides_existing_configuration(): void
    {
        $GLOBALS['TL_MODELS']        = $GLOBALS['TL_MODELS'] ?? [];
        $GLOBALS['TL_MODELS']['foo'] = 'Baz';

        expect($GLOBALS['TL_MODELS'])->shouldHaveKeyWithValue('foo', 'Baz');

        $this->onInitializeSystem();

        expect($GLOBALS['TL_MODELS'])->shouldHaveKeyWithValue('foo', 'Bar');
    }
}
