<?php

/**
 * @package    toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit;

use ContaoCommunityAlliance\Translator\TranslatorInterface;

/**
 * This traits provides access to the translator service.
 */
trait TranslatorTrait
{
    use ServiceContainerTrait;

    /**
     * Retrieve the translator.
     *
     * @return TranslatorInterface
     */
    protected function getTranslator()
    {
        return static::getServiceContainer()->getTranslator();
    }

    /**
     * {@inheritdoc}
     */
    public function translate($string, $domain = null, array $parameters = array(), $locale = null)
    {
        return $this->getTranslator()->translate($string, $domain, $parameters, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function translatePluralized($string, $number, $domain = null, array $parameters = array(), $locale = null)
    {
        return $this->getTranslator()->translatePluralized($string, $number, $domain, $parameters, $locale);
    }
}
