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

namespace Netzmacht\Contao\Toolkit\Translation;

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface as ContaoFramework;
use Contao\System;
use Symfony\Component\Translation\TranslatorInterface as Translator;
use function array_unshift;
use function trigger_error;
use const E_USER_DEPRECATED;

/**
 * LangArrayTranslator is a translator implementation using the globals of Contao.
 *
 * It's a backport of https://github.com/contao/core-bundle/blob/develop/src/Translation/Translator.php
 * introduced in Contao 4.5.
 */
class LangArrayTranslator implements Translator
{
    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * Contao framework.
     *
     * @var ContaoFramework
     */
    private $framework;

    /**
     * Constructor.
     *
     * @param Translator      $translator The translator to decorate.
     * @param ContaoFramework $framework  Contao framework.
     */
    public function __construct(Translator $translator, ContaoFramework $framework)
    {
        $this->translator = $translator;
        $this->framework  = $framework;
    }

    /**
     * {@inheritdoc}
     *
     * Gets the translation from Contao’s $GLOBALS['TL_LANG'] array if the message domain starts with
     * "contao_". The locale parameter is ignored in this case.
     */
    public function trans($messageId, array $parameters = [], $domain = null, $locale = null)
    {
        // Forward to the default translator
        if (null === $domain || strncmp($domain, 'contao_', 7) !== 0) {
            return $this->translator->trans($messageId, $parameters, $domain, $locale);
        }

        $domain = substr($domain, 7);

        $this->framework->initialize();
        $this->loadLanguageFile($domain);

        $translated = $this->getFromGlobals($messageId);

        if (null === $translated && $domain !== 'default') {
            $translated = $this->getFromGlobals($messageId, $domain);
        }

        if (null === $translated) {
            return $messageId;
        }

        if (!empty($parameters)) {
            $translated = vsprintf($translated, $parameters);
        }

        return $translated;
    }

    /**
     * {@inheritdoc}
     */
    public function transChoice($messageId, $number, array $parameters = [], $domain = null, $locale = null)
    {
        // Forward to the default translator
        return $this->translator->transChoice($messageId, $number, $parameters, $domain, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        // Forward to the default translator
        return $this->translator->setLocale($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        // Forward to the default translator
        return $this->translator->getLocale();
    }

    /**
     * Get the inner translator.
     *
     * @return Translator
     */
    public function getInnerTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * Returns the labels from the $GLOBALS['TL_LANG'] array.
     *
     * @param string      $messageId Message id, e.g. "MSC.view".
     * @param string|null $domain    Message domain, e.g. "messages" or "tl_content".
     *
     * @return string|null
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getFromGlobals(string $messageId, ?string $domain = null): ?string
    {
        // Split the ID into chunks allowing escaped dots (\.) and backslashes (\\)
        preg_match_all('/(?:\\\\[\.\\\\]|[^\.])++/', $messageId, $matches);
        $parts = preg_replace('/\\\\([\.\\\\])/', '$1', $matches[0]);

        if ($domain) {
            array_unshift($parts, $domain);

            // @codingStandardsIgnoreStart
            @trigger_error(
                'Autoprefixing message domain to message id is deprecated as it\'s not supported by Contao anymore',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd
        }

        $item = &$GLOBALS['TL_LANG'];

        foreach ($parts as $part) {
            if (!isset($item[$part])) {
                return null;
            }

            $item = &$item[$part];
        }

        return $item;
    }

    /**
     * Loads a Contao framework language file.
     *
     * @param string $name Language file name.
     *
     * @return void
     */
    private function loadLanguageFile(string $name): void
    {
        /** @var System|Adapter $system */
        $system = $this->framework->getAdapter(System::class);

        $system->loadLanguageFile($name);
    }
}
