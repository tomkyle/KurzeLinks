<?php

/**
 * tomkyle/kurzelinks
 *
 * Create short links with kurzelinks.de
 */

namespace tomkyle\KurzeLinks;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class Psr6CacheKurzeLinks implements KurzeLinksInterface
{
    /**
     * @param KurzeLinksInterface $kurzeLinks KurzeLinks API client
     * @param CacheItemPoolInterface $cacheItemPool PSR-6 CacheItemPool
     */
    public function __construct(protected KurzeLinksInterface $kurzeLinks, protected CacheItemPoolInterface $cacheItemPool)
    {
        // noop
    }


    #[\Override]
    public function create(string $url): string
    {
        $cache_key = $this->getCacheKey($url);

        try {
            $cache_item = $this->cacheItemPool->getItem($cache_key);
            if ($cache_item->isHit()) {
                return $cache_item->get();
            }

            $short_url = $this->kurzeLinks->create($url);
            $cache_item->set($short_url);
            $this->cacheItemPool->save($cache_item);

            return $short_url;

        } catch (\Throwable $throwable) {
            throw new \RuntimeException('Caught exception: ' . $throwable->getMessage(), 0, $throwable);
        }
    }

    public function getCacheKey(string $url): string
    {
        return 'kurzelinks_' . md5($url);
    }
}
