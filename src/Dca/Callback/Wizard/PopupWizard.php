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

namespace Netzmacht\Contao\Toolkit\Dca\Callback\Wizard;

use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface as CsrfTokenManager;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class PopupWizard.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Wizard
 */
final class PopupWizard extends AbstractWizard
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $template = 'be_wizard_popup';

    /**
     * If true the button is generated always no matter if an value is given.
     *
     * @var bool
     */
    private $always;

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
     * Button icon.
     *
     * @var string
     */
    private $icon;

    /**
     * Button href.
     *
     * @var string
     */
    private $href;

    /**
     * Button label.
     *
     * @var string
     */
    private $label;

    /**
     * Button title.
     *
     * @var string
     */
    private $title;

    /**
     * Crsf token name.
     *
     * @var string
     */
    private $tokenName;

    /**
     * Construct.
     *
     * @param TemplateFactory  $templateFactory  Template Factory.
     * @param Translator       $translator       Translator.
     * @param CsrfTokenManager $csrfTokenManager Csrf Token manager.
     * @param string           $csrfTokenName    Csrf Token name.
     * @param string           $href             Link href snippet.
     * @param string           $label            Button label.
     * @param string           $title            Button title.
     * @param string           $icon             Button icon.
     * @param bool             $always           If true the button is generated always no matter if an value is given.
     * @param string|null      $linkPattern      Link pattern.
     * @param string|null      $template         Template name.
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        TemplateFactory $templateFactory,
        Translator $translator,
        CsrfTokenManager $csrfTokenManager,
        string $csrfTokenName,
        string $href,
        string $label,
        string $title,
        string $icon,
        bool $always = false,
        ?string $linkPattern = null,
        ?string $template = null
    ) {
        parent::__construct($templateFactory, $translator, $template);

        $this->csrfTokenManager = $csrfTokenManager;
        $this->tokenName        = $csrfTokenName;
        $this->always           = $always;
        $this->icon             = $icon;
        $this->href             = $href;
        $this->label            = $label;
        $this->title            = $title;

        if ($linkPattern) {
            $this->linkPattern = $linkPattern;
        }
    }

    /**
     * Generate the popup wizard.
     *
     * @param mixed $value Id value.
     *
     * @return string
     */
    public function generate($value): string
    {
        if ($this->always || $value) {
            $token   = $this->csrfTokenManager->getToken($this->tokenName)->getValue();
            $href    = sprintf($this->linkPattern, $this->href, $value, $token);
            $jsTitle = specialchars(str_replace('\'', '\\\'', $this->title));

            $template = $this->createTemplate();
            $template
                ->set('href', $href)
                ->set('label', specialchars($this->label))
                ->set('title', specialchars($this->title))
                ->set('jsTitle', $jsTitle)
                ->set('icon', $this->icon);

            return $template->parse();
        }

        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke($dataContainer): string
    {
        return $this->generate($dataContainer->value);
    }
}
