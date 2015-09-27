<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Callback;

/**
 * Class ColorPickerCallback handles a colorpicker wizard callback for a customized color picker.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Callback
 */
class ColorPickerCallback
{
    /**
     * If true no '#' item is displayed.
     *
     * @var bool
     */
    private $replaceHex;

    /**
     * The color picker icon.
     *
     * @var string
     */
    private $icon;

    /**
     * The alt attribute.
     *
     * @var string
     */
    private $alt;

    /**
     * The class attribute.
     *
     * @var string
     */
    private $class;

    /**
     * Construct.
     *
     * @param bool   $replaceHex Should the hex '#' be replaced.
     * @param string $icon       Optional custom color picker icon.
     * @param string $alt        Optional color picker alt.
     * @param string $class      Optional color picker class.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __construct($replaceHex = false, $icon = null, $alt = null, $class = null)
    {
        $this->replaceHex = $replaceHex;
        $this->icon       = $icon ?: 'pickcolor.gif';
        $this->alt        = $alt ?: $GLOBALS['TL_LANG']['MSC']['colorpicker'];
        $this->class      = $class;
    }

    /**
     * Invoke the callback.
     *
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return string
     */
    public function __invoke($dataContainer)
    {
        $version = COLORPICKER;
        $replace = '';

        if ($this->replaceHex) {
            $replace = '.replace("#", "")';
        }

        $html  = \Image::getHtml($this->icon, $this->alt, $this->getAttributes($dataContainer));
        $html .= <<<HTML
<script>
window.addEvent('domready', function() {
    new MooRainbow('moo_{$dataContainer->field}', {
        id: 'ctrl_{$dataContainer->field}',
        startColor: ((cl = $('ctrl_{$dataContainer->field}').value.hexToRgb(true)) ? cl : [255, 0, 0]),
        imgPath: 'assets/mootools/colorpicker/{$version}/images/',
        onComplete: function(color) {
            $('ctrl_{$dataContainer->field}').value = color.hex{$replace};
        }
    });
});
</script>
HTML;

        return $html;
    }

    /**
     * Get the attributes.
     *
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return string
     */
    private function getAttributes($dataContainer)
    {
        return sprintf(
            'style="vertical-align:top;cursor:pointer;padding-left:3px" title="%s" id="moo_%s"',
            specialchars($this->alt),
            $dataContainer->field
        );
    }
}
