<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\View\Assets;

use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;
use Netzmacht\Contao\Toolkit\View\Assets\GlobalsAssetsManager;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Asset\Packages;

use function expect;

/** @SuppressWarnings(PHPMD.Superglobals) */
final class GlobalsAssetsManagerSpec extends ObjectBehavior
{
    public function let(Packages $packages): void
    {
        unset($GLOBALS['TL_JAVASCRIPT'], $GLOBALS['TL_CSS']);

        $this->beConstructedWith($packages, true);
    }

    public function letGo(): void
    {
        unset($GLOBALS['TL_JAVASCRIPT'], $GLOBALS['TL_CSS']);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(GlobalsAssetsManager::class);
        $this->shouldImplement(AssetsManager::class);
    }

    public function it_uses_name_as_prefix_for_each_javascript(): void
    {
        $this->addJavascripts(['a.js', 'b.js', 'c.js'], false, 'theme');

        expect($GLOBALS['TL_JAVASCRIPT'])->shouldReturn(
            ['theme_0' => 'a.js', 'theme_1' => 'b.js', 'theme_2' => 'c.js'],
        );
    }

    public function it_uses_name_as_prefix_for_string_keys_of_javascripts(): void
    {
        $this->addJavascripts(['app' => 'a.js', 'vendor' => 'v.js'], false, 'theme');

        expect($GLOBALS['TL_JAVASCRIPT'])->shouldReturn(['theme_app' => 'a.js', 'theme_vendor' => 'v.js']);
    }

    public function it_uses_string_keys_as_javascript_names_without_name(): void
    {
        $this->addJavascripts(['app' => 'a.js', 'vendor' => 'v.js', 'x.js'], false);

        expect($GLOBALS['TL_JAVASCRIPT'])->shouldReturn(['app' => 'a.js', 'vendor' => 'v.js', 0 => 'x.js']);
    }

    public function it_uses_name_as_prefix_for_each_stylesheet(): void
    {
        $this->addStylesheets(['one.css', 'two.css', 'three.css'], '', false, 'theme');

        expect($GLOBALS['TL_CSS'])->shouldReturn(
            ['theme_0' => 'one.css', 'theme_1' => 'two.css', 'theme_2' => 'three.css'],
        );
    }

    public function it_uses_string_keys_as_stylesheet_names_without_name(): void
    {
        $this->addStylesheets(['base' => 'base.css', 'print.css'], 'print', false);

        expect($GLOBALS['TL_CSS'])->shouldReturn(['base' => 'base.css|print', 0 => 'print.css|print']);
    }

    public function it_adds_static_flag_in_production_mode(Packages $packages): void
    {
        $this->beConstructedWith($packages, false);

        $this->addJavascript('a.js');
        $this->addStylesheet('a.css');

        expect($GLOBALS['TL_JAVASCRIPT'])->shouldReturn(['a.js|static']);
        expect($GLOBALS['TL_CSS'])->shouldReturn(['a.css||static']);
    }

    public function it_does_not_add_static_flag_in_debug_mode(): void
    {
        $this->addJavascript('a.js');
        $this->addStylesheet('a.css');

        expect($GLOBALS['TL_JAVASCRIPT'])->shouldReturn(['a.js']);
        expect($GLOBALS['TL_CSS'])->shouldReturn(['a.css']);
    }

    public function it_resolves_package_paths(Packages $packages): void
    {
        $packages->getUrl('js/app.js', 'my_bundle')->willReturn('/bundles/mybundle/js/app.js');

        $this->addJavascript('my_bundle::js/app.js', false);

        expect($GLOBALS['TL_JAVASCRIPT'])->shouldReturn(['/bundles/mybundle/js/app.js']);
    }
}
