<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use Netzmacht\Contao\Toolkit\Data\Alias\Filter;

/**
 * Base filter class.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias\Filter
 */
abstract class AbstractFilter implements Filter
{
    public const COMBINE_REPLACE = 0;
    public const COMBINE_PREPEND = 1;
    public const COMBINE_APPEND  = 2;

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
    public function __construct(bool $break, int $combine = self::COMBINE_REPLACE)
    {
        $this->break   = $break;
        $this->combine = $combine;
    }

    /**
     * {@inheritdoc}
     */
    public function breakIfValid(): bool
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
     * @param mixed  $current   Current alias value.
     * @param string $separator A separator string.
     *
     * @return string
     */
    protected function combine($previous, $current, string $separator): string
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
