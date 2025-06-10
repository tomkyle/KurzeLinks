<?php

/**
 * This file is part of tomkyle/kurzelinks
 *
 * Link shortener using the kurzelinks.de API. Supports PSR-6 caches and rate limits.
 */

namespace tomkyle\KurzeLinks;

class CallableKurzeLinks implements KurzeLinksInterface
{
    /**
     * @param KurzeLinksInterface $kurzeLinks Inner KurzeLinks API client
     */
    public function __construct(protected KurzeLinksInterface $kurzeLinks)
    {
        // noop
    }

    /**
     * Creates a short link representation for the given URL.
     *
     * @param  string $url Original URL
     * @return string      Shortened URL
     */
    public function __invoke(string $url): string
    {
        return $this->create($url);
    }


    #[\Override]
    public function create(string $url): string
    {
        return $this->kurzeLinks->create($url);
    }
}
