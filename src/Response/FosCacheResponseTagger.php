<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Response;

use FOS\HttpCache\Exception\InvalidTagException as FosInvalidTagException;
use FOS\HttpCache\ResponseTagger as FosResponseTagger;
use Netzmacht\Contao\Toolkit\Exception\InvalidHttpResponseTagException;

/**
 * Class FosCacheResponseTagger
 *
 * @package Netzmacht\Contao\Toolkit\Response
 */
final class FosCacheResponseTagger implements ResponseTagger
{
    /**
     * Friend of symfony http cache.
     *
     * @var FosResponseTagger
     */
    private $inner;

    /**
     * FosCacheResponseTagger constructor.
     *
     * @param FosResponseTagger $inner Friend of symfony http cache.
     */
    public function __construct(FosResponseTagger $inner)
    {
        $this->inner = $inner;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidHttpResponseTagException If apply tags failed.
     */
    public function addTags(array $tags): void
    {
        try {
            $this->inner->addTags($tags);
        } catch (FosInvalidTagException $e) {
            throw new InvalidHttpResponseTagException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
