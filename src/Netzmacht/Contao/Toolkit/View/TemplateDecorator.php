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
     * TemplateDecorator constructor.
     *
     * @param \Template           $innerTemplate The inner template.
     * @param TranslatorInterface $translator    The translator.
     */
    public function __construct($innerTemplate, TranslatorInterface $translator)
    {
        $this->innerTemplate = $innerTemplate;
        $this->translator    = $translator;
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
}
