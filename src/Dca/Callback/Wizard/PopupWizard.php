<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Callback\Wizard;

use RequestToken;
use ContaoCommunityAlliance\Translator\TranslatorInterface as Translator;
use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;

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
     * Request token.
     *
     * @var RequestToken
     */
    private $requestToken;

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
     * Construct.
     *
     * @param TemplateFactory $templateFactory Template Factory.
     * @param Translator      $translator      Translator.
     * @param RequestToken    $requestToken    Request token.
     * @param string          $href            Link href snippet.
     * @param string          $label           Button label.
     * @param string          $title           Button title.
     * @param string          $icon            Button icon.
     * @param bool            $always          If true the button is generated always no matter if an value is given.
     * @param string|null     $linkPattern     Link pattern.
     * @param string          $template        Template name.
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        TemplateFactory $templateFactory,
        Translator $translator,
        RequestToken $requestToken,
        $href,
        $label,
        $title,
        $icon,
        $always = false,
        $linkPattern = null,
        $template = null
    ) {
        parent::__construct($templateFactory, $translator, $template);

        $this->requestToken = $requestToken;
        $this->always       = $always;
        $this->icon         = $icon;
        $this->href         = $href;
        $this->label        = $label;
        $this->title        = $title;

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
    public function generate($value)
    {
        if ($this->always || $value) {
            $href    = sprintf($this->linkPattern, $this->href, $value, $this->requestToken->get());
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
    public function __invoke($dataContainer)
    {
        return $this->generate($dataContainer->value);
    }
}
