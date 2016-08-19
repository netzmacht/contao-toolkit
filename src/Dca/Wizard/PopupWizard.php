<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Wizard;

use RequestToken;
use ContaoCommunityAlliance\Translator\TranslatorInterface;
use Netzmacht\Contao\Toolkit\Dca\Callback\Wizard\AbstractWizard;
use Netzmacht\Contao\Toolkit\View\Template\BackendTemplate;

/**
 * Class PopupWizard.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Wizard
 */
class PopupWizard extends AbstractWizard
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
     * @param TranslatorInterface $translator   Translator.
     * @param RequestToken        $requestToken Request token.
     * @param string              $href         Link href snippet.
     * @param string              $label        Button label.
     * @param string              $title        Button title.
     * @param string              $icon         Button icon.
     * @param bool                $always       If true the button is generated always no matter if an value is given.
     * @param string              $template     Template name.
     */
    public function __construct(
        TranslatorInterface $translator,
        RequestToken $requestToken,
        $href,
        $label,
        $title,
        $icon,
        $always = false,
        $template = null
    ) {
        parent::__construct($translator, $template);

        $this->requestToken = $requestToken;
        $this->always       = $always;
        $this->icon         = $icon;
        $this->href         = $href;
        $this->label        = $label;
        $this->title        = $title;
    }

    /**
     * Get always.
     *
     * @return boolean
     */
    public function isAlways()
    {
        return $this->always;
    }

    /**
     * Get the link pattern.
     *
     * @return string
     */
    public function getLinkPattern()
    {
        return $this->linkPattern;
    }

    /**
     * Set the link pattern.
     *
     * @param string $linkPattern Link pattern.
     *
     * @return $this
     */
    public function setLinkPattern($linkPattern)
    {
        $this->linkPattern = $linkPattern;

        return $this;
    }

    /**
     * Get icon.
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set icon.
     *
     * @param string $icon Icon.
     *
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
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
            $href    = sprintf($this->requestToken, $this->href, $value, $this->requestToken->get());
            $jsTitle = specialchars(str_replace('\'', '\\\'', $this->title));

            $template = new BackendTemplate($this->template);
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
}
