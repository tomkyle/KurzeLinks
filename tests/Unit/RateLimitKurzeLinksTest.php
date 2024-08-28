<?php

/**
 * tomkyle/kurzelinks
 *
 * Create short links with kurzelinks.de
 */

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use tomkyle\KurzeLinks\RateLimitKurzeLinks;
use tomkyle\KurzeLinks\KurzeLinksInterface;

class RateLimitKurzeLinksTest extends TestCase
{

    protected $pass_through;

    protected $inner_kurzelinks;

    #[\Override]
    protected function setUp() : void
    {
        parent::setUp();

        // Create a mock for the KurzeLinksInterface
        $this->inner_kurzelinks = $this->createMock(KurzeLinksInterface::class);
        $this->inner_kurzelinks->method('create')
           ->with($this->anything())
           ->willReturnCallback(fn($url) => $url);
    }


    public function testConstruct(): void
    {
        $sut = new RateLimitKurzeLinks($this->inner_kurzelinks);
        $this->assertInstanceOf(KurzeLinksInterface::class, $sut);

    }


    #[DataProvider('provideSleepBetweenValues')]
    public function testSleepBetweenInterceptors(int $milli_seconds): void
    {

        $sut = new RateLimitKurzeLinks($this->inner_kurzelinks, 0);
        $this->assertEquals(0, $sut->getSleepBetween());

        $this->assertEquals($milli_seconds, $sut->setSleepBetween($milli_seconds)->getSleepBetween());
    }

    public static function provideSleepBetweenValues() : array
    {
        return ['0ms' => [ 0], '10ms' => [ 10], '20000ms' => [ 2000]];
    }


    public function testPassThroughUnshortenedUrl(): void
    {
        $sut = new RateLimitKurzeLinks($this->inner_kurzelinks, 0);

        $url = "http://test.com";

        $this->assertSame($url, $sut->create($url));
    }

}
