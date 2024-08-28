<?php

namespace tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use tomkyle\KurzeLinks\KurzeLinksInterface;
use tomkyle\KurzeLinks\Psr6CacheKurzeLinks;

class Psr6CacheKurzeLinksTest extends TestCase
{
    protected KurzeLinksInterface $kurzeLinksMock;

    protected CacheItemPoolInterface $cacheItemPoolMock;

    protected CacheItemInterface $cacheItemMock;

    /**
     * Set up common mocks and the subject under test (SUT).
     */
    protected function setUp(): void
    {
        $this->kurzeLinksMock = $this->createMock(KurzeLinksInterface::class);
        $this->cacheItemPoolMock = $this->createMock(CacheItemPoolInterface::class);
        $this->cacheItemMock = $this->createMock(CacheItemInterface::class);
    }

    public function testConstruct() : void
    {
        $sut = new Psr6CacheKurzeLinks($this->kurzeLinksMock, $this->cacheItemPoolMock);
        $this->assertInstanceOf(KurzeLinksInterface::class, $sut);
    }

    /**
     * Test that the cache key is correctly generated.
     *
     *
     */
    #[DataProvider('provideUrlsForCacheKey')]
    public function testGetCacheKey(string $url, string $expectedKey): void
    {
        $sut = new Psr6CacheKurzeLinks($this->kurzeLinksMock, $this->cacheItemPoolMock);
        $result = $sut->getCacheKey($url);
        $this->assertSame($expectedKey, $result);
    }


    /**
     * Data provider for the cache key test.
     */
    public static function provideUrlsForCacheKey(): array
    {
        return [
            'example.com' => ['https://example.com', 'kurzelinks_' . md5('https://example.com')],
            'another-example.com' => ['https://another-example.com', 'kurzelinks_' . md5('https://another-example.com')],
        ];
    }

    /**
     * Test that a URL is correctly shortened and cached when it is not already in the cache.
     */
    public function testCreateUrlNotInCache(): void
    {
        $sut = new Psr6CacheKurzeLinks($this->kurzeLinksMock, $this->cacheItemPoolMock);

        $url = 'https://example.com';
        $cacheKey = $sut->getCacheKey($url);
        $shortUrl = 'https://kurzelinks.de/abc123';
        $this->kurzeLinksMock->method('create')->with($url)->willReturn($shortUrl);

        $this->cacheItemPoolMock->method('getItem')->with($cacheKey)->willReturn($this->cacheItemMock);
        $this->cacheItemPoolMock->expects($this->once())->method('save')->with($this->cacheItemMock);

        $this->cacheItemMock->method('isHit')->willReturn(false);
        $this->cacheItemMock->expects($this->once())->method('set')->with($shortUrl);

        $result = $sut->create($url);

        $this->assertSame($shortUrl, $result);
    }

    /**
     * Test that the method returns the cached URL if it is already present in the cache.
     */
    public function testCreateUrlAlreadyInCache(): void
    {
        $sut = new Psr6CacheKurzeLinks($this->kurzeLinksMock, $this->cacheItemPoolMock);

        $url = 'https://example.com';
        $cacheKey = $sut->getCacheKey($url);
        $shortUrl = 'https://kurzelinks.de/abc123';

        $this->cacheItemPoolMock->method('getItem')->with($cacheKey)->willReturn($this->cacheItemMock);
        $this->cacheItemMock->method('isHit')->willReturn(true);
        $this->cacheItemMock->method('get')->willReturn($shortUrl);

        $result = $sut->create($url);

        $this->assertSame($shortUrl, $result);
    }

    /**
     * Test that an exception thrown by the cache item pool is correctly caught and rethrown as a RuntimeException.
     */
    public function testCreateThrowsRuntimeExceptionOnCacheError(): void
    {
        $sut = new Psr6CacheKurzeLinks($this->kurzeLinksMock, $this->cacheItemPoolMock);

        $this->expectException(\RuntimeException::class);

        $this->cacheItemPoolMock
            ->method('getItem')
            ->willThrowException(new \InvalidArgumentException('Invalid cache key'));

        $sut->create('https://example.com');
    }

}

