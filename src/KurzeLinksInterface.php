<?php

/**
 * tomkyle/kurzelinks
 *
 * Create short links with kurzelinks.de
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
