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

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Component;

use Contao\Database\Result;
use Contao\Model;
use Contao\Model\Collection;
use Contao\StringUtil;
use InvalidArgumentException;
use Netzmacht\Contao\Toolkit\Assertion\Assertion;
use Netzmacht\Contao\Toolkit\View\Template\TemplateReference as ToolkitTemplateReference;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;
use Symfony\Component\Templating\TemplateReferenceInterface as TemplateReference;

/**
 * Base element.
 *
 * @package Netzmacht\Contao\Toolkit\Component\ContentElement
 */
abstract class AbstractComponent implements Component
{
    /**
     * Assigned model.
     *
     * @var Model
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
     * @var array
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
     * AbstractContentElement constructor.
     *
     * @param Model|Collection|Result $model          Object model or result.
     * @param TemplateEngine          $templateEngine Template engine.
     * @param string                  $column         Column.
     *
     * @throws InvalidArgumentException When model does not have a row method.
     */
    public function __construct($model, TemplateEngine $templateEngine, $column = 'main')
    {
        if ($model instanceof Collection) {
            $model = $model->current();
        }

        if ($model instanceof Model) {
            $this->model = $model;
        }

        Assertion::methodExists('row', $model);

        $this->templateEngine = $templateEngine;
        $this->column         = $column;
        $this->data           = $this->deserializeData($model->row());

        if ($this->get('customTpl') != '' && TL_MODE == 'FE') {
            $this->setTemplateName((string) $this->get('customTpl'));
        }
    }

    /**
     * {@inheritDoc}
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
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * Get templateName.
     *
     * @return string
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

    /**
     * {@inheritDoc}
     */
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
     * @param array                    $parameters   Additional parameters being passed to the template.
     *
     * @return string
     */
    protected function render($templateName, array $parameters = []): string
    {
        return $this->templateEngine->render($templateName, $parameters);
    }

    /**
     * Pre template data.
     *
     * @param array $data Given data.
     *
     * @return array
     */
    protected function prepareTemplateData(array $data): array
    {
        $style = [];
        $space = $this->get('space');

        if (!empty($space[0])) {
            $style[] = 'margin-top:' . $space[0]. 'px;';
        }

        if (!empty($space[1])) {
            $style[] = 'margin-bottom:' . $space[1]. 'px;';
        }

        $cssID    = $this->get('cssID');
        $cssClass = $this->compileCssClass();

        // Do not change this order (see #6191)
        $data['style']    = implode(' ', $style);
        $data['class']    = $cssClass;
        $data['cssID']    = ($cssID[0] != '') ? ' id="' . $cssID[0] . '"' : '';
        $data['inColumn'] = $this->getColumn();

        return $data;
    }

    /**
     * Post generate the output.
     *
     * @param string $buffer Generated component.
     *
     * @return string
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
    protected function compile()
    {
    }

    /**
     * Get the template data.
     *
     * @return array
     */
    protected function getData(): array
    {
        return $this->data;
    }

    /**
     * Deserialize the model data.
     *
     * @param array $row Model data.
     *
     * @return array
     */
    protected function deserializeData(array $row): array
    {
        if (!empty($row['space'])) {
            $row['space'] = StringUtil::deserialize($row['space']);
        }

        if (!empty($row['cssID'])) {
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
     *
     * @return string
     */
    protected function compileCssClass(): string
    {
        $cssID    = $this->get('cssID');
        $cssClass = 'ce_' . $this->get('type');

        if (!empty($cssID[1])) {
            $cssClass .= ' ' . $cssID[1];
        }

        if ($this->getModel() && $this->getModel()->classes) {
            $cssClass .= ' ' . implode(' ', (array) $this->getModel()->classes);
        }

        return $cssClass;
    }

    /**
     * Get the column.
     *
     * @return string
     */
    protected function getColumn(): string
    {
        return $this->column;
    }

    /**
     * Get the template reference.
     *
     * @return TemplateReference
     */
    protected function getTemplateReference(): TemplateReference
    {
        return new ToolkitTemplateReference(
            $this->getTemplateName(),
            'html5',
            ToolkitTemplateReference::SCOPE_FRONTEND
        );
    }
}
