<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Response;

use FOS\HttpCache\Exception\InvalidTagException as FosInvalidTagException;
use FOS\HttpCache\ResponseTagger as FosResponseTagger;
use Netzmacht\Contao\Toolkit\Exception\InvalidHttpResponseTagException;

final class FosCacheResponseTagger implements ResponseTagger
{
    /**
     * Friend of symfony http cache.
     */
    private FosResponseTagger $inner;

    /**
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
            throw new InvalidHttpResponseTagException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
