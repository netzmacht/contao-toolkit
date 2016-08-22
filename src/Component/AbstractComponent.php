<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Component;

use Database\Result;
use Model;
use Model\Collection;
use Netzmacht\Contao\Toolkit\View\Template;
use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;

/**
 * Base element.
 *
 * @package Netzmacht\Contao\Toolkit\Component\ContentElement
 */
abstract class AbstractComponent
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
    protected $templateName;

    /**
     * Template factory.
     *
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * Template instance.
     *
     * @var Template
     */
    protected $template;

    /**
     * AbstractContentElement constructor.
     *
     * @param Model|Collection|Result $model           Object model or result.
     * @param TemplateFactory         $templateFactory Template factory.
     * @param string                  $column          Column.
     */
    public function __construct($model, TemplateFactory $templateFactory, $column = 'main')
    {
        if ($model instanceof Collection) {
            $model = $model->current();
        }

        if ($model instanceof Model) {
            $this->model = $model;
        }

        $this->templateFactory = $templateFactory;
        $this->column          = $column;
        $this->data            = $this->deserializeData($model->row());

        if ($this->get('customTpl') != '' && TL_MODE == 'FE') {
            $this->setTemplateName($this->get('customTpl'));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function get($name)
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
    public function has($name)
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * Get templateName.
     *
     * @return string
     */
    protected function getTemplateName()
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
    protected function setTemplateName($templateName)
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
    public function generate()
    {
        $this->preGenerate();

        $this->template = $this->templateFactory->createFrontendTemplate($this->getTemplateName(), $this->getData());
        $this->prepareTemplate($this->template);
        $this->compile();

        $buffer = $this->template->parse();
        $buffer = $this->postGenerate($buffer);

        return $buffer;
    }

    /**
     * Pre generate is called before creating the template.
     *
     * @return void
     */
    protected function preGenerate()
    {
    }

    /**
     * Post generate the output.
     *
     * @param string $buffer Generated component.
     *
     * @return string
     */
    private function postGenerate($buffer)
    {
        return $buffer;
    }

    /**
     * Prepare the template.
     *
     * @param Template $template Template name.
     *
     * @return void
     */
    private function prepareTemplate(Template $template)
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
        $template->set('style', implode(' ', $style));
        $template->set('class', $cssClass);
        $template->set('cssID', ($cssID[0] != '') ? ' id="' . $cssID[0] . '"' : '');
        $template->set('inColumn', $this->getColumn());
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
    protected function getData()
    {
        return $this->data;
    }

    /**
     * Get the template factory.
     *
     * @return TemplateFactory
     */
    protected function getTemplateFactory()
    {
        return $this->templateFactory;
    }

    /**
     * Deserialize the model data.
     *
     * @param array $row Model data.
     *
     * @return array
     */
    protected function deserializeData(array $row)
    {
        if (!empty($row['space'])) {
            $row['space'] = deserialize($row['space']);
        }

        if (!empty($row['cssID'])) {
            $row['cssID'] = deserialize($row['cssID'], true);
        }

        $headline = deserialize($row['headline']);
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
    protected function compileCssClass()
    {
        $cssID    = $this->get('cssID');
        $cssClass = 'ce_' . $this->get('type');

        if (!empty($cssID[1])) {
            $cssClass .= ' ' . $cssID[1];
        }

        if ($this->getModel() && $this->getModel()->classes) {
            $cssClass .= $this->getModel()->classes;
        }

        return $cssClass;
    }

    /**
     * Get the column.
     *
     * @return string
     */
    protected function getColumn()
    {
        return $this->column;
    }
}
