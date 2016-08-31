<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Component\Module;

use ContaoCommunityAlliance\Translator\TranslatorInterface as Translator;
use Database\Result;
use Model;
use Model\Collection;
use Netzmacht\Contao\Toolkit\Component\AbstractComponent;
use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;

/**
 * Class AbstractModule.
 *
 * @package Netzmacht\Contao\Toolkit\Component\Module
 */
abstract class AbstractModule extends AbstractComponent implements Module
{
    /**
     * Should the module be rendered in backend mode.
     *
     * @var bool
     */
    protected $renderInBackendMode = false;

    /**
     * Translator.
     *
     * @var Translator
     */
    protected $translator;

    /**
     * AbstractModule constructor.
     *
     * @param Model|Collection|Result $model           Object model or result.
     * @param TemplateFactory         $templateFactory Template factory.
     * @param Translator              $translator      Translator.
     * @param string                  $column          Column.
     */
    public function __construct($model, TemplateFactory $templateFactory, Translator $translator, $column = 'main')
    {
        parent::__construct($model, $templateFactory, $column);

        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        if (TL_MODE === 'BE' && !$this->renderInBackendMode) {
            return $this->generateBackendView();
        }

        return parent::generate();
    }

    /**
     * Generate the backend view.
     *
     * @return string
     */
    protected function generateBackendView()
    {
        $template = $this->getTemplateFactory()->createBackendTemplate('be_wildcard');
        $href     = $this->generateBackendLink();
        $wildcard = sprintf(
            '### %s ###',
            $this->getTranslator()->translate(sprintf('FMD.%s.0', $this->get('type')))
        );

        $template->set('wildcard', $wildcard);
        $template->set('title', $this->get('headline'));
        $template->set('id', $this->get('id'));
        $template->set('link', $this->get('name'));
        $template->set('href', $href);

        return $template->parse();
    }

    /**
     * Generate the backend link.
     *
     * @return string
     */
    protected function generateBackendLink()
    {
        return 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->get('id');
    }

    /**
     * Get translator.
     *
     * @return Translator
     */
    protected function getTranslator()
    {
        return $this->translator;
    }
}
