<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use Netzmacht\Contao\Toolkit\Data\Alias\Filter;

/**
 * Base filter class.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias\Filter
 */
abstract class AbstractFilter implements Filter
{
    const COMBINE_REPLACE = 0;
    const COMBINE_PREPEND = 1;
    const COMBINE_APPEND  = 2;

    /**
     * If true break after the filter if value is unique.
     *
     * @var bool
     */
    private $break;

    /**
     * Combine flag.
     *
     * @var int
     */
    private $combine;

    /**
     * AbstractFilter constructor.
     *
     * @param bool $break   If true break after the filter if value is unique.
     * @param int  $combine Combine flag.
     */
    public function __construct($break, $combine = self::COMBINE_REPLACE)
    {
        $this->break   = (bool) $break;
        $this->combine = (int) $combine;
    }

    /**
     * {@inheritdoc}
     */
    public function breakIfUnique()
    {
        return $this->break;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
    }

    /**
     * Combine the current value with the previous one.
     *
     * @param string $previous  Previous alias value.
     * @param string $current   Current alias value.
     * @param string $separator A separator string.
     *
     * @return string
     */
    protected function combine($previous, $current, $separator)
    {
        if (!$previous) {
            return $current;
        }

        switch ($this->combine) {
            case static::COMBINE_PREPEND:
                return $current . $separator . $previous;

            case static::COMBINE_APPEND:
                return $previous . $separator . $current;

            default:
                return $current;
        }
    }
}
