<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template;

use Netzmacht\Contao\Toolkit\View\Template\Exception\HelperNotFound;

/**
 * Trait extends the default Contao template classes.
 */
trait TemplateTrait
{
    /**
     * Template helper.
     *
     * @var array<string,object|callable>
     */
    protected array $helpers = [];

    /**
     * Get a template value.
     *
     * @param string $name The name.
     */
    public function get(string $name): mixed
    {
        return $this->$name;
    }

    /**
     * Set a template var.
     *
     * @param string $name  The template var name.
     * @param mixed  $value The value.
     *
     * @return $this
     */
    public function set(string $name, $value): self
    {
        $this->$name = $value;

        return $this;
    }

    /**
     * Get the helper.
     *
     * @param string $name Name of the view helper.
     *
     * @throws HelperNotFound If helper is not registered.
     */
    public function helper(string $name): object|callable
    {
        if (! isset($this->helpers[$name])) {
            throw new HelperNotFound($name);
        }

        return $this->helpers[$name];
    }

    /**
     * Insert a template.
     *
     * @param string                   $name The template name.
     * @param array<string,mixed>|null $data An optional data array.
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     * @phpcs:disable SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue.NullabilityTypeMissing
     */
    public function insert($name, array $data = null): void
    {
        $template = new static($name, $this->helpers, $this->strContentType);

        if ($data !== null) {
            $template->setData($data);
        }

        echo $template->parse();
    }
}
