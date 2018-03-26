<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Toolkit\Translation;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface as ContaoFramework;
use Netzmacht\Contao\Toolkit\Assertion\Assertion;
use Symfony\Component\Translation\TranslatorBagInterface as TranslatorBag;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * LangArrayTranslator is a translator implementation using the globals of Contao.
 *
 * It's a backport of https://github.com/contao/core-bundle/blob/develop/src/Translation/Translator.php
 * introduced in Contao 4.5.
 */
class LangArrayTranslatorBagTranslator extends LangArrayTranslator implements TranslatorBag
{
    /**
     * Constructor.
     *
     * @param Translator      $translator The translator to decorate.
     * @param ContaoFramework $framework  Contao framework.
     *
     * @throws \Assert\AssertionFailedException When a translator is passed not implementing TranslatorBag interface.
     */
    public function __construct(Translator $translator, ContaoFramework $framework)
    {
        Assertion::implementsInterface($translator, TranslatorBag::class);

        parent::__construct($translator, $framework);
    }

    /**
     * {@inheritdoc}
     */
    public function getCatalogue($locale = null)
    {
        /** @var TranslatorBag $inner */
        $inner = $this->getInnerTranslator();

        return $inner->getCatalogue($locale);
    }
}
