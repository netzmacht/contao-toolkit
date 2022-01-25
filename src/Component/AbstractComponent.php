<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Component;

use Contao\Database\Result;
use Contao\Model;
use Contao\Model\Collection;
use Contao\StringUtil;
use InvalidArgumentException;
use Netzmacht\Contao\Toolkit\Assertion\Assertion;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateReference as ToolkitTemplateReference;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;
use Symfony\Component\Templating\TemplateReferenceInterface as TemplateReference;

use function array_key_exists;
use function defined;
use function implode;
use function is_array;
use function trigger_error;
use function trim;

use const E_USER_DEPRECATED;
use const TL_MODE;

/**
 * Base element.
 *
 * @deprecated Since 3.5.0 and get removed in 4.0.0
 *
 * @psalm-suppress DeprecatedInterface
 * @psalm-suppress DeprecatedClass
 */
abstract class AbstractComponent implements Component
{
    /**
     * Assigned model.
     *
     * @var Model|null
     */
    private $model;

    /**
     * Column of the element.
     *
     * @var string
     */
    private $column;

    /**
     * Components parameter.
     *
     * @var array<string,mixed>
     */
    private $data;

    /**
     * The template name.
     *
     * @var string
     */
    protected $templateName = '';

    /**
     * Template engine.
     *
     * @var TemplateEngine
     */
    private $templateEngine;

    /**
     * Request scope matcher.
     *
     * @var RequestScopeMatcher|null
     */
    protected $requestScopeMatcher;

    /**
     * @param Model|Collection|Result  $model               Object model or result.
     * @param TemplateEngine           $templateEngine      Template engine.
     * @param string                   $column              Column.
     * @param RequestScopeMatcher|null $requestScopeMatcher Request scope matcher.
     *
     * @throws InvalidArgumentException When model does not have a row method.
     */
    public function __construct(
        $model,
        TemplateEngine $templateEngine,
        $column = 'main',
        ?RequestScopeMatcher $requestScopeMatcher = null
    ) {
        if ($model instanceof Collection) {
            $model = $model->current();
        }

        if ($model instanceof Model) {
            $this->model = $model;
        }

        if ($requestScopeMatcher === null) {
            // @codingStandardsIgnoreStart
            @trigger_error(
                'RequestScopeMatcher not passed as forth argument. RequestScopeMatcher will be required in version 4.0.0',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd
        }

        Assertion::methodExists('row', $model);

        $this->templateEngine      = $templateEngine;
        $this->column              = $column;
        $this->requestScopeMatcher = $requestScopeMatcher;
        $this->data                = $this->deserializeData($model->row());

        if ($this->get('customTpl') === '' || ! $this->isFrontendRequest()) {
            return;
        }

        $this->setTemplateName((string) $this->get('customTpl'));
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress DeprecatedClass
     */
    public function set(string $name, $value): Component
    {
        $this->data[$name] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name)
    {
        if ($this->has($name)) {
            return $this->data[$name];
        }

        return null;
    }

    /**
     * Check if parameter exists.
     *
     * @param string $name Name of the parameter.
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * Get templateName.
     */
    protected function getTemplateName(): string
    {
        return $this->templateName;
    }

    /**
     * Set templateName.
     *
     * @param string $templateName Template name.
     *
     * @return $this
     */
    protected function setTemplateName(string $templateName): self
    {
        $this->templateName = $templateName;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getModel()
    {
        return $this->model;
    }

    public function generate(): string
    {
        $this->compile();

        $data   = $this->prepareTemplateData($this->getData());
        $buffer = $this->render($this->getTemplateReference(), $data);
        $buffer = $this->postGenerate($buffer);

        return $buffer;
    }

    /**
     * Render a template.
     *
     * @param TemplateReference|string $templateName Template name or reference.
     * @param array<string,mixed>      $parameters   Additional parameters being passed to the template.
     */
    protected function render($templateName, array $parameters = []): string
    {
        return $this->templateEngine->render($templateName, $parameters);
    }

    /**
     * Pre template data.
     *
     * @param array<string,mixed> $data Given data.
     *
     * @return array<string,mixed>
     */
    protected function prepareTemplateData(array $data): array
    {
        $style = [];
        $space = $this->get('space');

        if (! empty($space[0])) {
            $style[] = 'margin-top:' . $space[0] . 'px;';
        }

        if (! empty($space[1])) {
            $style[] = 'margin-bottom:' . $space[1] . 'px;';
        }

        $cssID    = $this->get('cssID');
        $cssClass = $this->compileCssClass();

        // Do not change this order (see #6191)
        $data['style']    = implode(' ', $style);
        $data['class']    = $cssClass;
        $data['cssID']    = $cssID[0] !== '' ? ' id="' . $cssID[0] . '"' : '';
        $data['inColumn'] = $this->getColumn();

        return $data;
    }

    /**
     * Post generate the output.
     *
     * @param string $buffer Generated component.
     */
    private function postGenerate(string $buffer): string
    {
        return $buffer;
    }

    /**
     * Compile the component.
     *
     * @return void
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
    protected function compile()
    {
    }

    /**
     * Get the template data.
     *
     * @return array<string,mixed>
     */
    protected function getData(): array
    {
        return $this->data;
    }

    /**
     * Deserialize the model data.
     *
     * @param array<string,mixed> $row Model data.
     *
     * @return array<string,mixed>
     */
    protected function deserializeData(array $row): array
    {
        if (! empty($row['space'])) {
            $row['space'] = StringUtil::deserialize($row['space']);
        }

        if (! empty($row['cssID'])) {
            $row['cssID'] = StringUtil::deserialize($row['cssID'], true);
        }

        $headline = StringUtil::deserialize($row['headline']);
        if (is_array($headline)) {
            $row['headline'] = $headline['value'];
            $row['hl']       = $headline['unit'];
        } else {
            $row['headline'] = $headline;
            $row['hl']       = 'h1';
        }

        return $row;
    }

    /**
     * Compile the css class.
     */
    protected function compileCssClass(): string
    {
        $cssID    = $this->get('cssID');
        $cssClass = '';

        if (! empty($cssID[1])) {
            $cssClass .= $cssID[1];
        }

        $model = $this->getModel();
        if ($model && $model->classes) {
            $cssClass .= ' ' . implode(' ', (array) $model->classes);
        }

        return trim($cssClass);
    }

    /**
     * Get the column.
     */
    protected function getColumn(): string
    {
        return $this->column;
    }

    /**
     * Get the template reference.
     */
    protected function getTemplateReference(): TemplateReference
    {
        return new ToolkitTemplateReference(
            $this->getTemplateName(),
            'html5',
            ToolkitTemplateReference::SCOPE_FRONTEND
        );
    }

    /**
     * Check if current request is a Contao frontend request.
     */
    protected function isFrontendRequest(): bool
    {
        if ($this->requestScopeMatcher) {
            return $this->requestScopeMatcher->isFrontendRequest();
        }

        return defined('TL_MODE') && TL_MODE === 'FE';
    }

    /**
     * Check if current request is a Contao backend request.
     */
    protected function isBackendRequest(): bool
    {
        if ($this->requestScopeMatcher) {
            return $this->requestScopeMatcher->isBackendRequest();
        }

        return defined('TL_MODE') && TL_MODE === 'BE';
    }
}
