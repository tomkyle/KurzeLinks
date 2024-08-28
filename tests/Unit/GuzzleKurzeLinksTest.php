<?php

namespace tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use tomkyle\KurzeLinks\GuzzleKurzeLinks;
use tomkyle\KurzeLinks\KurzeLinksInterface;

class GuzzleKurzeLinksTest extends TestCase
{
    protected Client $guzzleMock;

    protected GuzzleKurzeLinks $sut;

    protected string $api = 'https://kurzelinks.de/api';

    protected string $key = 'testapikey';

    /**
     * Set up common mocks and the subject under test (SUT).
     */
    protected function setUp(): void
    {
        $this->guzzleMock = $this->createMock(Client::class);
    }

    public function testConstruct() : void
    {
        $sut = new GuzzleKurzeLinks($this->api, $this->key, $this->guzzleMock);
        $this->assertInstanceOf(KurzeLinksInterface::class, $sut);
    }

    /**
     * Test that a URL is correctly shortened when the API returns a valid response.
     */
    public function testCreateReturnsShortenedUrl(): void
    {
        $url = 'https://example.com';
        $shortUrl = 'https://kurzelinks.de/abc123';
        $responseBody = json_encode(['shorturl' => ['url' => $shortUrl]]);

        $responseMock = $this->createMock(Response::class);
        $streamMock = $this->createMock(StreamInterface::class);

        $responseMock->method('getStatusCode')->willReturn(200);
        $responseMock->method('getReasonPhrase')->willReturn('OK');
        $responseMock->method('getBody')->willReturn($streamMock);

        $streamMock->method('__toString')->willReturn($responseBody);

        $this->guzzleMock->method('request')
            ->with('POST', $this->api, [
                'headers' => [
                    'Accept-Encoding' => 'gzip, deflate',
                    'Connection' => 'keep-alive',
                ],
                'form_params' => [
                    'key' => $this->key,
                    'json' => 1,
                    'url' => $url,
                ],
            ])
            ->willReturn($responseMock);

        $sut = new GuzzleKurzeLinks($this->api, $this->key, $this->guzzleMock);
        $result = $sut->create($url);

        $this->assertSame($shortUrl, $result);
    }

    /**
     * Test that an unexpected status code from the API throws an UnexpectedValueException.
     */
    public function testCreateThrowsExceptionOnUnexpectedStatusCode(): void
    {
        $this->expectException(\UnexpectedValueException::class);

        $url = 'https://example.com';
        $responseMock = $this->createMock(Response::class);

        $responseMock->method('getStatusCode')->willReturn(500);
        $responseMock->method('getReasonPhrase')->willReturn('Internal Server Error');

        $this->guzzleMock->method('request')->willReturn($responseMock);

        $sut = new GuzzleKurzeLinks($this->api, $this->key, $this->guzzleMock);
        $sut->create($url);
    }

    /**
     * Test that invalid JSON response throws an UnexpectedValueException.
     */
    public function testCreateThrowsExceptionOnInvalidJson(): void
    {
        $this->expectException(\UnexpectedValueException::class);

        $url = 'https://example.com';
        $responseMock = $this->createMock(Response::class);
        $streamMock = $this->createMock(StreamInterface::class);

        $responseMock->method('getStatusCode')->willReturn(200);
        $responseMock->method('getReasonPhrase')->willReturn('OK');
        $responseMock->method('getBody')->willReturn($streamMock);

        $streamMock->method('__toString')->willReturn('Invalid JSON');

        $this->guzzleMock->method('request')->willReturn($responseMock);

        $sut = new GuzzleKurzeLinks($this->api, $this->key, $this->guzzleMock);
        $sut->create($url);
    }

    /**
     * Data provider for missing keys in the JSON response.
     */
    public static function provideInvalidJsonResponses(): array
    {
        return [
            'Empty object {}' => ['{}', "Expected key 'shorturl' in JSON array"],
            'Missing "url" in "shorturl" object' => ['{"shorturl": {}}', "Expected key 'url' in 'shorturl' element"],
        ];
    }

    /**
     * Test that missing keys in the JSON response throw an UnexpectedValueException.
     *
     *
     */
    #[DataProvider('provideInvalidJsonResponses')]
    public function testCreateThrowsExceptionOnMissingJsonKeys(string $jsonResponse, string $expectedExceptionMessage): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $url = 'https://example.com';
        $responseMock = $this->createMock(Response::class);
        $streamMock = $this->createMock(StreamInterface::class);

        $responseMock->method('getStatusCode')->willReturn(200);
        $responseMock->method('getReasonPhrase')->willReturn('OK');
        $responseMock->method('getBody')->willReturn($streamMock);

        $streamMock->method('__toString')->willReturn($jsonResponse);

        $this->guzzleMock->method('request')->willReturn($responseMock);

        $sut = new GuzzleKurzeLinks($this->api, $this->key, $this->guzzleMock);
        $sut->create($url);
    }
}
