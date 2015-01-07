<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\DevTools\Dca\Callback;


class ColorPickerCallback
{
    /**
     * @var bool
     */
    private $replaceHex;
    /**
     * @var null
     */
    private $icon;
    /**
     * @var null
     */
    private $alt;
    /**
     * @var null
     */
    private $class;

    public function __construct($replaceHex = false, $icon = null, $alt = null, $class = null)
    {
        $this->replaceHex = $replaceHex;
        $this->icon       = $icon ?: 'pickcolor.gif';
        $this->alt        = $alt ?: $GLOBALS['TL_LANG']['MSC']['colorpicker'];
        $this->class      = $class;
    }

    public function __invoke($dataContainer)
    {
        $version = COLORPICKER;
        $replace = '';

        if ($this->replaceHex) {
            $replace = '.replace("#", "")';
        }

        $html = \Image::getHtml($this->icon, $this->alt, $this->getStyle($dataContainer));
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

    private function getStyle($dataContainer)
    {
        return sprintf(
            'style="vertical-align:top;cursor:pointer;padding-left:3px" title="%s" id="moo_%s"',
            specialchars($this->alt),
            $dataContainer->field
        );
    }
}
