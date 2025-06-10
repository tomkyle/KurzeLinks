<?php

/**
 * This file is part of tomkyle/kurzelinks
 *
 * Link shortener using the kurzelinks.de API. Supports PSR-6 caches and rate limits.
 */

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use tomkyle\KurzeLinks\PassthroughKurzeLinks;
use tomkyle\KurzeLinks\KurzeLinksInterface;

class PassthroughKurzeLinksTest extends TestCase
{
    public function testConstruct(): void
    {
        $sut = new PassthroughKurzeLinks();

        $this->assertInstanceOf(KurzeLinksInterface::class, $sut);
    }

    public function testPassThroughUnshortenedUrl(): void
    {
        $sut = new PassthroughKurzeLinks();

        $url = "http://test.com";

        $this->assertSame($url, $sut->create($url));
    }

}
