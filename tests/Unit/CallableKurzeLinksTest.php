<?php

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use tomkyle\KurzeLinks\CallableKurzeLinks;
use tomkyle\KurzeLinks\KurzeLinksInterface;

class CallableKurzeLinksTest extends TestCase
{
    protected KurzeLinksInterface $kurzeLinksMock;

    protected CallableKurzeLinks $sut;

    /**
     * Set up common mocks and the subject under test (SUT).
     */
    protected function setUp(): void
    {
        $this->kurzeLinksMock = $this->createMock(KurzeLinksInterface::class);
    }


    public function testConstruct() : void
    {
        $sut = new CallableKurzeLinks($this->kurzeLinksMock);
        $this->assertInstanceOf(KurzeLinksInterface::class, $sut);
        $this->assertIsCallable($sut);
    }


    /**
     * Test that the create method delegates to the inner KurzeLinksInterface.
     */
    public function testCreateDelegatesToInnerKurzeLinks(): void
    {
        $url = 'https://example.com';
        $shortUrl = 'https://kurzelinks.de/abc123';

        $this->kurzeLinksMock
            ->method('create')
            ->with($url)
            ->willReturn($shortUrl);

        $sut = new CallableKurzeLinks($this->kurzeLinksMock);
        $result = $sut->create($url);

        $this->assertSame($shortUrl, $result);
    }

    /**
     * Test that invoking the CallableKurzeLinks object returns the shortened URL.
     */
    public function testInvokeCallsCreateMethod(): void
    {
        $url = 'https://example.com';
        $shortUrl = 'https://kurzelinks.de/abc123';

        $this->kurzeLinksMock
            ->method('create')
            ->with($url)
            ->willReturn($shortUrl);

        $sut = new CallableKurzeLinks($this->kurzeLinksMock);
        $result = $sut($url);

        $this->assertSame($shortUrl, $result);
    }
}

