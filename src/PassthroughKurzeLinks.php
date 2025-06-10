<?php

/**
 * This file is part of tomkyle/kurzelinks
 *
 * Link shortener using the kurzelinks.de API. Supports PSR-6 caches and rate limits.
 */

namespace tomkyle\KurzeLinks;

use GuzzleHttp\Client;

/**
 * This "pass-through" implementation just returns the original URL.
 */
class PassthroughKurzeLinks implements KurzeLinksInterface
{
    #[\Override]
    public function create(string $url): string
    {
        return $url;
    }
}
