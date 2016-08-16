<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Component\Hybrid;

use ContaoCommunityAlliance\Translator\TranslatorInterface as Translator;
use Netzmacht\Contao\Toolkit\Component\Module\AbstractModule;
use Netzmacht\Contao\Toolkit\View\Template;
use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;

/**
 * Class AbstractHybrid.
 *
 * @package Netzmacht\Contao\Toolkit\Component\Hybrid
 */
class AbstractHybrid extends AbstractModule implements Hybrid
{
    /**
     * Hybrid key.
     *
     * @var string
     */
    protected $key;

    /**
     * Hybrid table.
     *
     * @var string
     */
    protected $table;

    /**
     * Hybrid data.
     *
     * @var array
     */
    private $hybrid = [];

    /**
     * Database connection.
     *
     * @var \Database
     */
    private $database;

    /**
     * {@inheritDoc}
     */
    public function __construct(
        $model,
        TemplateFactory $templateFactory,
        Translator $translator,
        \Database $database,
        $column
    ) {
        parent::__construct($model, $templateFactory, $translator, $column);

        $this->database = $database;
        $this->loadHybrid();
    }

    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        if ($this->isContentElement()) {
            if (!$this->isVisible()) {
                return '';
            }

            $this->renderInBackendMode = true;
        }

        return parent::generate();
    }

    /**
     * {@inheritDoc}
     */
    protected function compile(Template $template)
    {
        parent::compile($template);

        $template->set('hybrid', $this->hybrid);
    }

    /**
     * Get hybrid.
     *
     * @return array
     */
    protected function getHybrid()
    {
        return $this->hybrid;
    }

    /**
     * Check if content element is visible.
     *
     * @return bool
     */
    protected function isVisible()
    {
        if (TL_MODE !== 'FE' || !BE_USER_LOGGED_IN) {
            return true;
        }

        if (!$this->get('invisible')) {
            return false;
        }

        $now   = time();
        $start = $this->get('start');
        $stop  = $this->get('stop');

        if (($start != '' && $start > $now) || ($stop != '' && $stop < $now)) {
            return false;
        }

        return true;
    }

    /**
     * Check if hybrid is used as content element.
     *
     * @return bool
     */
    private function isContentElement()
    {
        return $this->getModel() instanceof \ContentModel;
    }

    /**
     * Load the hybrid.
     *
     * @return void
     */
    private function loadHybrid()
    {
        if (!$this->table || !$this->key) {
            return;
        }

        /** @var \Model $modelClass */
        $modelClass = \Model::getClassFromTable($this->table);
        if (class_exists($modelClass)) {
            $hybridModel = $modelClass::findByPk($this->get($this->key));

            if ($hybridModel) {
                $this->hybrid = $hybridModel->row();
            }
        } else {
            $result = $this->database
                ->prepare(sprintf('SELECT * FROM %s WHERE id=?', $this->table))
                ->execute($this->get($this->key));

            if ($result->numRows) {
                $this->hybrid = $result->row();
            }
        }
    }
}
