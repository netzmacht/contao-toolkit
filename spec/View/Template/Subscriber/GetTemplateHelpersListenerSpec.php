<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

namespace spec\Netzmacht\Contao\Toolkit\View\Template\Subscriber;

use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;
use Netzmacht\Contao\Toolkit\View\Template\Event\GetTemplateHelpersEvent;
use Netzmacht\Contao\Toolkit\View\Template\Subscriber\GetTemplateHelpersListener;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Subject;
use PhpSpec\Wrapper\Wrapper;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class GetTemplateHelpersListenerSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\View\Template\Subscriber
 * @mixin GetTemplateHelpersListener
 */
class GetTemplateHelpersListenerSpec extends ObjectBehavior
{
    function let(AssetsManager $assetsManager, TranslatorInterface $translator)
    {
        $this->beConstructedWith($assetsManager, $translator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\View\Template\Subscriber\GetTemplateHelpersListener');
    }

    function it_registers_default_helpers()
    {
        $event = new GetTemplateHelpersEvent('example');

        $this->handle($event);

        expect($event->getHelpers()['assets'])->shouldHaveType(AssetsManager::class);
        expect($event->getHelpers()['translator'])->shouldHaveType(TranslatorInterface::class);
    }
}
