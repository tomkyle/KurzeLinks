<?php

/**
 * tomkyle/kurzelinks
 *
 * Create short links with kurzelinks.de
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
