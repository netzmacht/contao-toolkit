<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Component\Module;

use Contao\Database\Result;
use Contao\Model;
use Contao\Model\Collection;
use Netzmacht\Contao\Toolkit\Component\AbstractComponent;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;
use Symfony\Component\Translation\TranslatorInterface as Translator;

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
     * @param Model|Collection|Result $model          Object model or result.
     * @param TemplateEngine          $templateEngine Template engine.
     * @param Translator              $translator     Translator.
     * @param string                  $column         Column.
     */
    public function __construct($model, TemplateEngine $templateEngine, Translator $translator, $column = 'main')
    {
        parent::__construct($model, $templateEngine, $column);

        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function generate(): string
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
    protected function generateBackendView(): string
    {
        $wildcard = sprintf(
            '### %s ###',
            $this->getTranslator()->trans(sprintf('FMD.%s.0', $this->get('type')), [], 'contao_modules')
        );

        $parameters = [
            'wildcard' => $wildcard,
            'title'    => $this->get('headline'),
            'id'       => $this->get('id'),
            'link'     => $this->get('name'),
            'href'     => $this->generateBackendLink()
        ];

        return $this->render('toolkit:be:be_wildcard.html5', $parameters);
    }

    /**
     * Generate the backend link.
     *
     * @return string
     */
    protected function generateBackendLink(): string
    {
        return 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->get('id');
    }

    /**
     * Get translator.
     *
     * @return Translator
     */
    protected function getTranslator(): Translator
    {
        return $this->translator;
    }
}
