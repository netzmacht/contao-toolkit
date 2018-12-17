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

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Wizard;

use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface as CsrfTokenManager;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;
use Symfony\Component\Translation\TranslatorInterface as Translator;
use const E_USER_DEPRECATED;

/**
 * Class PopupWizard.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Wizard
 */
final class PopupWizardListener extends AbstractWizardListener
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $template = 'toolkit:be:be_wizard_popup.html5';

    /**
     * Link pattern for the url.
     *
     * @var string
     */
    private $linkPattern = 'contao/main.php?%s&amp;id=%s&amp;popup=1&amp;nb=1&amp;rt=%s';

    /**
     * Csrf token manager.
     *
     * @var CsrfTokenManager
     */
    private $csrfTokenManager;

    /**
     * Crsf token name.
     *
     * @var string
     */
    private $tokenName;

    /**
     * Construct.
     *
     * @param TemplateEngine   $templateEngine   Template Engine.
     * @param Translator       $translator       Translator.
     * @param Manager          $dcaManager       Data container manager.
     * @param CsrfTokenManager $csrfTokenManager Csrf Token manager.
     * @param string           $csrfTokenName    Csrf Token name.
     * @param string           $template         Template name.
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        TemplateEngine $templateEngine,
        Translator $translator,
        Manager $dcaManager,
        CsrfTokenManager $csrfTokenManager,
        string $csrfTokenName,
        string $template = ''
    ) {
        parent::__construct($templateEngine, $translator, $dcaManager, $template);

        $this->csrfTokenManager = $csrfTokenManager;
        $this->tokenName        = $csrfTokenName;
    }

    /**
     * Generate the popup wizard.
     *
     * @param mixed $value  Id value.
     * @param array $config Wizard config.
     *
     * @return string
     */
    public function generate($value, array $config = []): string
    {
        $config = array_merge(
            [
                'href'        => null,
                'title'       => null,
                'linkPattern' => $this->linkPattern,
                'icon'        => null,
                'always'      => false,
            ],
            $config
        );

        if ($config['always'] || $value) {
            $token      = $this->csrfTokenManager->getToken($this->tokenName)->getValue();
            $parameters = [
                'href'    => sprintf($config['linkPattern'], $config['href'], $value, $token),
                'label'   => StringUtil::specialchars($config['label']),
                'title'   => StringUtil::specialchars($config['title']),
                'jsTitle' => StringUtil::specialchars(str_replace('\'', '\\\'', $config['title'])),
                'icon'    => $config['icon'],
            ];

            return $this->render($this->template, $parameters);
        }

        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function onWizardCallback($dataContainer): string
    {
        $definition = $this->getDefinition($dataContainer);
        $config     = (array) $definition->get(['fields', $dataContainer->field, 'toolkit', 'popup_wizard']);

        return $this->generate($dataContainer->value, $config);
    }

    /**
     * {@inheritDoc}
     */
    public function handleWizardCallback($dataContainer): string
    {
        // @codingStandardsIgnoreStart
        @trigger_error(
            sprintf(
                '%1$s::handleWizardCallback is deprecated and will be removed in Version 4.0.0. '
                . 'Use %1$s::onWizardCallback instead.',
                static::class
            ),
            E_USER_DEPRECATED
        );
        // @codingStandardsIgnoreEnd

        return $this->onWizardCallback($dataContainer);
    }
}
