<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Wizard;

use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface as CsrfTokenManager;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

use function array_merge;
use function sprintf;
use function str_replace;

final class PopupWizardListener extends AbstractWizardListener
{
    /**
     * Template name.
     */
    protected string $template = 'toolkit:be:be_wizard_popup.html5';

    /**
     * Link pattern for the url.
     */
    private string $linkPattern = 'contao/main.php?%s&amp;id=%s&amp;popup=1&amp;nb=1&amp;rt=%s';

    /**
     * Csrf token manager.
     */
    private CsrfTokenManager $csrfTokenManager;

    /**
     * Crsf token name.
     */
    private string $tokenName;

    /**
     * Construct.
     *
     * @param TemplateRenderer $templateRenderer Template renderer.
     * @param Translator       $translator       Translator.
     * @param DcaManager       $dcaManager       Data container manager.
     * @param CsrfTokenManager $csrfTokenManager Csrf Token manager.
     * @param string           $csrfTokenName    Csrf Token name.
     * @param string           $template         Template name.
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        TemplateRenderer $templateRenderer,
        Translator $translator,
        DcaManager $dcaManager,
        CsrfTokenManager $csrfTokenManager,
        string $csrfTokenName,
        string $template = ''
    ) {
        parent::__construct($templateRenderer, $translator, $dcaManager, $template);

        $this->csrfTokenManager = $csrfTokenManager;
        $this->tokenName        = $csrfTokenName;
    }

    /**
     * Generate the popup wizard.
     *
     * @param mixed               $value  Id value.
     * @param array<string,mixed> $config Wizard config.
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
                'href'    => sprintf($config['linkPattern'], (string) $config['href'], $value, $token),
                'label'   => StringUtil::specialchars((string) $config['label']),
                'title'   => StringUtil::specialchars((string) $config['title']),
                'jsTitle' => StringUtil::specialchars(str_replace('\'', '\\\'', (string) $config['title'])),
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
}
