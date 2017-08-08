<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use Contao\StringUtil;
use Patchwork\Utf8;

/**
 * SlugifyFilter creates a slug value of the columns being represented.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias\Filter
 */
final class SlugifyFilter extends AbstractValueFilter
{
    /**
     * Preserve uppercase.
     *
     * @var bool
     */
    private $preserveUppercase;

    /**
     * Encoding charset.
     *
     * @var string
     */
    private $charset;

    /**
     * Construct.
     *
     * @param array  $columns           Columns being used for the value.
     * @param bool   $break             If true break after the filter if value is unique.
     * @param int    $combine           Combine flag.
     * @param bool   $preserveUppercase If true uppercase values are not transformed.
     * @param string $charset           Encoding charset.
     */
    public function __construct(
        array $columns,
        $break = true,
        $combine = self::COMBINE_REPLACE,
        $preserveUppercase = false,
        $charset = 'utf-8'
    ) {
        parent::__construct($columns, $break, $combine);

        $this->preserveUppercase = (bool) $preserveUppercase;
        $this->charset           = $charset;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($model, $value, $separator)
    {
        $values = array();

        foreach ($this->columns as $column) {
            $values[] = $this->slugify($model->$column, $separator);
        }

        return $this->combine($value, $values, $separator);
    }

    /**
     * Slugify a value.
     *
     * @param string $value     Given value.
     * @param string $separator Separator string.
     *
     * @return string
     */
    private function slugify($value, $separator)
    {
        $arrSearch  = array('/[^a-zA-Z0-9 \.\&\/_-]+/', '/[ \.\&\/-]+/');
        $arrReplace = array('', $separator);

        $value = html_entity_decode($value, ENT_QUOTES, $this->charset);
        $value = StringUtil::stripInsertTags($value);
        $value = Utf8::toAscii($value);
        $value = preg_replace($arrSearch, $arrReplace, $value);

        if (!$this->preserveUppercase) {
            $value = strtolower($value);
        }

        return trim($value, $separator);
    }
}
