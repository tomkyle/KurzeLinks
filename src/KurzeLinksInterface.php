<?php

/**
 * This file is part of tomkyle/kurzelinks
 *
 * Link shortener using the kurzelinks.de API. Supports PSR-6 caches and rate limits.
 */

namespace tomkyle\KurzeLinks;

interface KurzeLinksInterface
{
    /**
     * Creates a short link representation for the given URL.
     *
     * @param  string $url Original URL
     * @return string      Shortened URL
     */
    public function create(string $url): string;
}
