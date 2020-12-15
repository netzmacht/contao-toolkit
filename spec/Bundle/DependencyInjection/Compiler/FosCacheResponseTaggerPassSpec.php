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

namespace spec\Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\FosCacheResponseTaggerPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class FosCacheResponseTaggerPassSpec extends ObjectBehavior
{
    private const TOOLKIT_TAGGER_SERVICE_ID = 'netzmacht.contao_toolkit.response_tagger';

    private const FOS_TAGGER_SERVICE_ID = 'fos_http_cache.http.symfony_response_tagger';

    public function let(): void
    {
        $this->beConstructedWith();
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(FosCacheResponseTaggerPass::class);
    }

    public function it_doesnt_register_response_tagger_if_fos_response_tagger_not_available(
        ContainerBuilder $container
    ): void {
        $container->has(self::FOS_TAGGER_SERVICE_ID)
            ->shouldBeCalled()
            ->willReturn(false);

        $container->setDefinition(self::TOOLKIT_TAGGER_SERVICE_ID, Argument::type(Definition::class))
            ->shouldNotBeCalled();

        $this->process($container);
    }

    public function it_registers_response_tagger_if_fos_response_tagger_is_available(
        ContainerBuilder $container
    ): void {
        $container->has(self::FOS_TAGGER_SERVICE_ID)
            ->shouldBeCalled()
            ->willReturn(true);

        $container->setDefinition(self::TOOLKIT_TAGGER_SERVICE_ID, Argument::type(Definition::class))
            ->shouldBeCalled();

        $this->process($container);
    }
}
