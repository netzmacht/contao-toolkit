<?php

/**
 * @package    toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\View;

use ContaoCommunityAlliance\Translator\TranslatorInterface;

/**
 * TemplateDecorator allows to use template API for non toolkit templates.
 */
class TemplateDecorator implements Template
{
    /**
     * The inner template.
     * 
     * @var \Template
     */
    private $innerTemplate;

    /**
     * The translator.
     * 
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * The assets manager.
     *
     * @var AssetsManager
     */
    private $assetsManager;

    /**
     * TemplateDecorator constructor.
     *
     * @param \Template           $innerTemplate The inner template.
     * @param TranslatorInterface $translator    The translator.
     * @param AssetsManager       $assetsManager Assets manager.
     */
    public function __construct($innerTemplate, TranslatorInterface $translator, AssetsManager $assetsManager)
    {
        $this->innerTemplate = $innerTemplate;
        $this->translator    = $translator;
        $this->assetsManager = $assetsManager;

        $this->registerTemplateMethods();
    }

    /**
     * {@inheritDoc}
     */
    public function parse()
    {
        return $this->innerTemplate->parse();
    }

    /**
     * {@inheritDoc}
     */
    public function get($name)
    {
        return $this->innerTemplate->$name;
    }

    /**
     * {@inheritDoc}
     */
    public function set($name, $value)
    {
        $this->innerTemplate->$name = $value;
        
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function translate($string, $domain = null, array $parameters = array(), $locale = null)
    {
        return $this->translator->translate($string, $domain, $parameters, $locale);
    }

    /**
     * {@inheritDoc}
     */
    public function translatePluralized($string, $number, $domain = null, array $parameters = array(), $locale = null)
    {
        return $this->translatePluralized($string, $number, $domain, $parameters, $locale);
    }

    /**
     * {@inheritDoc}
     */
    public function getAssetsManager()
    {
        return $this->assetsManager;
    }

    /**
     * Register all provides methods of the decorator to the inner templte.
     *
     * @return void
     */
    private function registerTemplateMethods()
    {
        $this->set(
            'set',
            function ($name, $value) {
                return $this->set($name, $value);
            }
        );

        $this->set(
            'get',
            function ($name) {
                return $this->get($name);
            }
        );

        $this->set(
            'translate',
            function (
                $string,
                $domain = null,
                array $parameters = array(),
                $locale = null
            ) {
                return $this->translate($string, $domain, $parameters, $locale);
            }
        );

        $this->set(
            'translatePluralized',
            function (
                $string,
                $number,
                $domain = null,
                array $parameters = array(),
                $locale = null
            ) {
                return $this->translatePluralized($string, $number, $domain, $parameters, $locale);
            }
        );

        $this->set(
            'getAssetsManager',
            function () {
                return $this->getAssetsManager();
            }
        );
    }
}
