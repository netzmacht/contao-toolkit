<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Response;

use Netzmacht\Contao\Toolkit\Exception\InvalidHttpResponseTagException;

/**
 * Interface ResponseTagger is introduced as a backward compatibility layer for Contao < 4.6.
 *
 * It allows you to use the response tagger in your userland code. The tags are only added if Contao can handle it
 * (since version 4.6).
 */
interface ResponseTagger
{
    /**
     * Add tags to be set on the response.
     *
     * This must be called before any HTTP response is sent to the client.
     *
     * @param list<string> $tags List of tags to add.
     *
     * @throws InvalidHttpResponseTagException When applying tags failed.
     */
    public function addTags(array $tags): void;
}
