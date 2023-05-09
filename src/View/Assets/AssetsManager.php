<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Assets;

/**
 * Assets manager describes an asset manager which handles assets being added by components.
 */
interface AssetsManager
{
    public const STATIC_PRODUCTION = 'prod';

    /**
     * Add a javascript file to Contao assets.
     *
     * @param string      $path   The assets path.
     * @param string|bool $static Register it as static entry.
     * @param string|null $name   Optional assets name.
     *
     * @return $this
     */
    public function addJavascript(string $path, $static = self::STATIC_PRODUCTION, string|null $name = null): self;

    /**
     * Add javascript files to Contao assets.
     *
     * @param array<int|string,string> $paths  The assets paths.
     * @param string|bool              $static Register it as static entry.
     * @param string|null              $name   Optional assets name.
     *
     * @return $this
     */
    public function addJavascripts(array $paths, $static = self::STATIC_PRODUCTION, string|null $name = null): self;

    /**
     * Add a javascript file to Contao assets.
     *
     * @param string      $path   The assets path.
     * @param string      $media  The media query.
     * @param string|bool $static Register it as static entry.
     * @param string|null $name   Optional assets name.
     *
     * @return $this
     */
    public function addStylesheet(
        string $path,
        string $media = '',
        $static = self::STATIC_PRODUCTION,
        string|null $name = null,
    ): self;

    /**
     * Add stylesheet files to Contao assets.
     *
     * @param array<int|string,string> $paths  The assets paths.
     * @param string                   $media  The media type.
     * @param string|bool              $static Register it as static entry.
     * @param string|null              $name   Optional assets name.
     *
     * @return $this
     */
    public function addStylesheets(
        array $paths,
        string $media = '',
        $static = self::STATIC_PRODUCTION,
        string|null $name = null,
    ): self;

    /**
     * Add a named block to the body.
     *
     * If the block already exists, it gets overridden
     *
     * @param string $name The name of the block.
     * @param string $html The content of the block.
     *
     * @return $this
     */
    public function addToBody(string $name, string $html): self;

    /**
     * Append a block to the body.
     *
     * @param string $html The content of the block.
     */
    public function appendToBody(string $html): self;

    /**
     * Add a named block to the head.
     *
     * If the block already exists, it gets overridden
     *
     * @param string $name The name of the block.
     * @param string $html The content of the block.
     *
     * @return $this
     */
    public function addToHead(string $name, string $html): self;

    /**
     * Append a block to the head.
     *
     * @param string $html The content of the block.
     *
     * @return $this
     */
    public function appendToHead(string $html): self;
}
