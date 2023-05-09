<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\View\Assets;

use Netzmacht\Contao\Toolkit\View\Assets\AssetPackageAssetsManager;
use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Asset\Packages;

final class AssetPackageAssetsManagerSpec extends ObjectBehavior
{
    public function let(AssetsManager $inner, Packages $packages): void
    {
        $this->beConstructedWith($inner, $packages);

        $inner->addStylesheet(Argument::cetera())->willReturn($inner);
        $inner->addStylesheets(Argument::cetera())->willReturn($inner);
        $inner->addJavascript(Argument::cetera())->willReturn($inner);
        $inner->addJavascripts(Argument::cetera())->willReturn($inner);
        $inner->addToHead(Argument::cetera())->willReturn($inner);
        $inner->appendToHead(Argument::cetera())->willReturn($inner);
        $inner->addToBody(Argument::cetera())->willReturn($inner);
        $inner->appendToBody(Argument::cetera())->willReturn($inner);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(AssetPackageAssetsManager::class);
    }

    public function it_is_an_html_page_assets_manager(): void
    {
        $this->shouldBeAnInstanceOf(AssetsManager::class);
    }

    public function it_locates_stylesheet_paths(AssetsManager $inner, Packages $packages): void
    {
        $packages->getUrl('asset.css', 'package')
            ->shouldBeCalled()
            ->willReturn('/bundles/package/asset.css');

        $inner->addStylesheet('/bundles/package/asset.css', Argument::cetera())
            ->shouldBeCalled();

        $this->addStylesheet('package::asset.css')->shouldReturn($this);
    }

    public function it_locates_stylesheets_paths(AssetsManager $inner, Packages $packages): void
    {
        $packages->getUrl('asset.css', 'package')
            ->shouldBeCalled()
            ->willReturn('/bundles/package/asset.css');

        $packages->getUrl('asset2.css', 'package')
            ->shouldBeCalled()
            ->willReturn('/bundles/package/asset2.css');

        $inner
            ->addStylesheets(
                ['/bundles/package/asset.css', '/bundles/package/asset2.css', 'style.css'],
                Argument::cetera(),
            )
            ->shouldBeCalled();

        $this->addStylesheets(['package::asset.css', 'package::asset2.css', 'style.css'])->shouldReturn($this);
    }

    public function it_locates_javascript_paths(AssetsManager $inner, Packages $packages): void
    {
        $packages->getUrl('asset.js', 'package')
            ->shouldBeCalled()
            ->willReturn('/bundles/package/asset.js');

        $inner->addJavascript('/bundles/package/asset.js', Argument::cetera())
            ->shouldBeCalled();

        $this->addJavaScript('package::asset.js')->shouldReturn($this);
    }

    public function it_locates_javascripts_paths(AssetsManager $inner, Packages $packages): void
    {
        $packages->getUrl('asset.js', 'package')
            ->shouldBeCalled()
            ->willReturn('/bundles/package/asset.js');

        $packages->getUrl('asset2.js', 'package')
            ->shouldBeCalled()
            ->willReturn('/bundles/package/asset2.js');

        $inner
            ->addJavascripts(
                ['/bundles/package/asset.js', '/bundles/package/asset2.js', 'style.js'],
                Argument::cetera(),
            )
            ->shouldBeCalled();

        $this->addJavaScripts(['package::asset.js', 'package::asset2.js', 'style.js'])->shouldReturn($this);
    }

    public function it_delegates_add_to_head(AssetsManager $inner): void
    {
        $this->addToHead('foo', 'bar');
        $inner->addToHead('foo', 'bar')->shouldBeCalled();
    }

    public function it_delegates_append_to_head(AssetsManager $inner): void
    {
        $this->appendToHead('foo');
        $inner->appendToHead('foo')->shouldBeCalled();
    }

    public function it_delegates_add_to_body(AssetsManager $inner): void
    {
        $this->addToBody('foo', 'bar');
        $inner->addToBody('foo', 'bar')->shouldBeCalled();
    }

    public function it_delegates_append_to_body(AssetsManager $inner): void
    {
        $this->appendToBody('foo');
        $inner->appendToBody('foo')->shouldBeCalled();
    }
}
