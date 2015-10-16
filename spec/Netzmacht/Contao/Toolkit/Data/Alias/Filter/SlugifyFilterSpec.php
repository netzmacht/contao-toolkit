<?php

namespace spec\Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use Netzmacht\Contao\Toolkit\Data\Alias\Filter\AbstractValueFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SlugifyFilter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

if (!function_exists('standardize')) {
    function standardize($str)
    {
        $a = array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','ÿ','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','Ð','d','E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g','H','h','H','h','I','i','I','i','I','i','I','i','I','i','?','?','J','j','K','k','L','l','L','l','L','l','?','?','L','l','N','n','N','n','N','n','?','O','o','O','o','O','o','Œ','œ','R','r','R','r','R','r','S','s','S','s','S','s','Š','š','T','t','T','t','T','t','U','u','U','u','U','u','U','u','U','u','U','u','W','w','Y','y','Ÿ','Z','z','Z','z','Ž','ž','?','ƒ','O','o','U','u','A','a','I','i','O','o','U','u','U','u','U','u','U','u','U','u','?','?','?','?','?','?');
        $b = array('A','A','A','A','A','A','AE','C','E','E','E','E','I','I','I','I','D','N','O','O','O','O','O','O','U','U','U','U','Y','s','a','a','a','a','a','a','ae','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','o','u','u','u','u','y','y','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','D','d','E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g','H','h','H','h','I','i','I','i','I','i','I','i','I','i','IJ','ij','J','j','K','k','L','l','L','l','L','l','L','l','l','l','N','n','N','n','N','n','n','O','o','O','o','O','o','OE','oe','R','r','R','r','R','r','S','s','S','s','S','s','S','s','T','t','T','t','T','t','U','u','U','u','U','u','U','u','U','u','U','u','W','w','Y','y','Y','Z','z','Z','z','Z','z','s','f','O','o','U','u','A','a','I','i','O','o','U','u','U','u','U','u','U','u','U','u','A','a','AE','ae','O','o');

        return strtolower(
            preg_replace(
                array('/[^a-zA-Z0-9 -]/','/[ -]+/','/^-|-$/'),array('','-',''),
                str_replace($a,$b,$str)
            )
        );
    }

    eval(
        'function standardize ($str) {
            return spec\Netzmacht\Contao\Toolkit\Data\Alias\Filter\standardize($str);
        }
        '
    );
}

/**
 * Class SlugifyFilterSpec
 * @package spec\Netzmacht\Contao\Toolkit\Data\Alias\Filter
 * @mixin SlugifyFilter
 */
class SlugifyFilterSpec extends ObjectBehavior
{
    const SEPARATOR = '-';

    const RAW_VALUE = 'Äöü -?^°#.test';

    const ALIAS_VALUE = 'alias-value';

    const COLUMN = 'title';

    function createInstance()
    {
        $this->beConstructedWith([static::COLUMN]);
    }

    function it_is_initializable()
    {
        $this->createInstance();
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\Filter\SlugifyFilter');
    }

    function it_is_an_alias_filter()
    {
        $this->createInstance();
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Data\Alias\Filter');
    }

    function it_breaks_by_default()
    {
        $this->createInstance();
        $this->breakIfValid()->shouldReturn(true);
    }


    function it_accepts_break_option()
    {
        $this->beConstructedWith([static::COLUMN], false);
        $this->breakIfValid()->shouldReturn(false);
    }

    function it_does_not_support_repeating()
    {
        $this->createInstance();
        $this->repeatUntilValid()->shouldReturn(false);
    }

    function it_standardizes_value()
    {
        $model = (object) [static::COLUMN => static::RAW_VALUE];

        $this->createInstance();
        $this->apply($model, '', static::SEPARATOR)->shouldReturn(standardize(static::RAW_VALUE));
    }

    function it_supports_multiple_columns()
    {
        $model = (object) [
            static::COLUMN => static::RAW_VALUE,
            'test' => 'ää0032ä-´12'
        ];

        $this->beConstructedWith([static::COLUMN, 'test']);
        $this->apply($model, '', static::SEPARATOR)->shouldReturn(
            standardize(static::RAW_VALUE) . static::SEPARATOR . standardize($model->test)
        );
    }

    function it_supports_custom_separator()
    {
        $model = (object) [
            static::COLUMN => static::RAW_VALUE,
            'test' => 'ää0032ä-´12'
        ];

        $this->beConstructedWith([static::COLUMN, 'test']);
        $this->apply($model, '', '_')->shouldReturn(
            standardize(static::RAW_VALUE) . '_' . standardize($model->test)
        );
    }

    function it_replaces_existing_value_by_default()
    {
        $model = (object) [
            static::COLUMN => static::RAW_VALUE,
            'id' => 5
        ];

        $this->beConstructedWith([static::COLUMN, 'id']);
        $this->apply($model, 'test', '_')->shouldReturn(
            standardize(static::RAW_VALUE) . '_' . 5
        );
    }

    function it_supports_appending()
    {
        $model = (object) [
            'id' => 5
        ];

        $this->beConstructedWith(['id'], true, AbstractValueFilter::COMBINE_APPEND);
        $this->apply($model, static::ALIAS_VALUE, static::SEPARATOR)->shouldReturn(
            static::ALIAS_VALUE . static::SEPARATOR . 5
        );
    }

    function it_supports_prepending()
    {
        $model = (object) [
            'id' => 5
        ];

        $this->beConstructedWith(['id'], true, AbstractValueFilter::COMBINE_PREPEND);
        $this->apply($model, static::ALIAS_VALUE, static::SEPARATOR)->shouldReturn(
            5 . static::SEPARATOR . static::ALIAS_VALUE
        );
    }
}
