<?php

/**
 * tomkyle/kurzelinks
 *
 * Create short links with kurzelinks.de
 */

namespace tomkyle\KurzeLinks;

class RateLimitKurzeLinks implements KurzeLinksInterface
{
    /**
     * @param KurzeLinksInterface $kurzeLinks KurzeLinks API client
     * @param int|integer         $sleep   Rate limit pause in milliseconds
     */
    public function __construct(protected KurzeLinksInterface $kurzeLinks, protected int $sleep = 4000)
    {
        // noop
    }


    #[\Override]
    public function create(string $url): string
    {
        usleep($this->sleep * 1000);
        return $this->kurzeLinks->create($url);
    }

    public function setSleepBetween(int $sleep): self
    {
        $this->sleep = $sleep;
        return $this;
    }

    public function getSleepBetween(): int
    {
        return $this->sleep;
    }
}
