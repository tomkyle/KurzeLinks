<?php

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use tomkyle\KurzeLinks\Psr18KurzeLinks;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Client\ClientExceptionInterface;
use GuzzleHttp\Psr7\HttpFactory;

class Psr18KurzeLinksTest extends TestCase
{

    /**
     * @var ClientInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    protected ClientInterface $httpClient;

    /**
     * @var RequestFactoryInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    protected RequestFactoryInterface $requestFactory;

    /**
     * @var StreamFactoryInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    protected StreamFactoryInterface $streamFactory;

    protected function setUp(): void
    {
        $this->httpFactory = new HttpFactory();
        $this->requestFactory = new HttpFactory();
        $this->streamFactory = new HttpFactory();

        $this->httpClient = $this->createMock(ClientInterface::class);
    }

    protected function createResponse(int $status, string $reason, string $body) : ResponseInterface
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($status);
        $response->method('getReasonPhrase')->willReturn($reason);
        $bodyStream = $this->httpFactory->createStream($body);
        $response->method('getBody')->willReturn($bodyStream);
        return $response;
    }


    /**
     * Tests that the Psr18KurzeLinks class can successfully create a short URL.
     */
    public function testCreateShortUrl(): void
    {
        $response = $this->createResponse(200, 'OK', json_encode([
            'shorturl' => [
                'url' => 'https://kurzelinks.de/short-url'
            ]
        ]));


        $this->httpClient->method('sendRequest')
            ->willReturn($response);

        $sut = new Psr18KurzeLinks(
            'https://kurzelinks.de/api',
            'fake_api_key',
            $this->httpClient,
            $this->requestFactory,
            $this->streamFactory
        );

        $shortUrl = $sut->create('https://example.com');

        $this->assertSame('https://kurzelinks.de/short-url', $shortUrl);
    }

    /**
     * Tests that the Psr18KurzeLinks class throws an exception for a non-200 status code.
     */
    public function testCreateThrowsExceptionOnNon200StatusCode(): void
    {
        $response = $this->createResponse(500, 'Internal Server Error', '');



        $this->httpClient->method('sendRequest')
            ->willReturn($response);

        $sut = new Psr18KurzeLinks(
            'https://kurzelinks.de/api',
            'fake_api_key',
            $this->httpClient,
            $this->requestFactory,
            $this->streamFactory
        );

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('API response from https://kurzelinks.de/api: 500 Internal Server Error');

        $sut->create('https://example.com');
    }

    /**
     * Tests that the Psr18KurzeLinks class throws an exception when invalid JSON is returned.
     */
    public function testCreateThrowsExceptionOnInvalidJson(): void
    {
        $response = $this->createResponse(200, 'OK', 'Invalid JSON');

        $this->httpClient->method('sendRequest')
            ->willReturn($response);

        $sut = new Psr18KurzeLinks(
            'https://kurzelinks.de/api',
            'fake_api_key',
            $this->httpClient,
            $this->requestFactory,
            $this->streamFactory
        );

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid JSON response');

        $sut->create('https://example.com');
    }

    /**
     * Tests that the Psr18KurzeLinks class throws an exception when the 'shorturl' key is missing in JSON response.
     */
    public function testCreateThrowsExceptionWhenShortUrlKeyIsMissing(): void
    {
        $response = $this->createResponse(200, 'OK', json_encode([]));

        $this->httpClient->method('sendRequest')
            ->willReturn($response);

        $sut = new Psr18KurzeLinks(
            'https://kurzelinks.de/api',
            'fake_api_key',
            $this->httpClient,
            $this->requestFactory,
            $this->streamFactory
        );

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage("Expected key 'shorturl' in JSON array");

        $sut->create('https://example.com');
    }

    /**
     * Tests that the Psr18KurzeLinks class throws an exception when the 'url' key is missing in the 'shorturl' element.
     */
    public function testCreateThrowsExceptionWhenUrlKeyIsMissing(): void
    {

        $response = $this->createResponse(200, 'OK', json_encode(['shorturl' => []]));

        $this->httpClient->method('sendRequest')
            ->willReturn($response);

        $sut = new Psr18KurzeLinks(
            'https://kurzelinks.de/api',
            'fake_api_key',
            $this->httpClient,
            $this->requestFactory,
            $this->streamFactory
        );

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage("Expected key 'url' in 'shorturl' element");

        $sut->create('https://example.com');
    }

    /**
     * Data provider for testCreateWithValidUrls.
     */
    public static function provideValidUrls(): array
    {
        return [
            'example.com' => ['https://example.com'],
            'another-example.com' => ['https://another-example.com'],
            'yet-another-example.com' => ['https://yet-another-example.com'],
        ];
    }

    /**
     * Tests that the Psr18KurzeLinks class successfully creates short URLs with various valid inputs.
     *
     * @param string $url The URL to shorten.
     */
    #[DataProvider('provideValidUrls')]
    public function testCreateWithValidUrls(string $url): void
    {
        $response = $this->createResponse(200, 'OK', json_encode([
            'shorturl' => [
                'url' => 'https://kurzelinks.de/short-url'
            ]
        ]));


        $this->httpClient->method('sendRequest')
            ->willReturn($response);

        $sut = new Psr18KurzeLinks(
            'https://kurzelinks.de/api',
            'fake_api_key',
            $this->httpClient,
            $this->requestFactory,
            $this->streamFactory
        );

        $shortUrl = $sut->create($url);

        $this->assertSame('https://kurzelinks.de/short-url', $shortUrl);
    }

/**
 * Tests that the Psr18KurzeLinks class catches and handles a ClientExceptionInterface thrown by the HTTP client.
 *
 * @return void
 */
public function testCreateCatchesClientException(): void
{
    // Create a mock for the exception to be thrown
    $exception = $this->createMock(ClientExceptionInterface::class);

    // Configure the HTTP client mock to throw the exception when sendRequest is called
    $this->httpClient->method('sendRequest')
        ->willThrowException($exception);

    $sut = new Psr18KurzeLinks(
        'https://kurzelinks.de/api',
        'fake_api_key',
        $this->httpClient,
        $this->requestFactory,
        $this->streamFactory
    );

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('HTTP request failed');

    // Call the create method, which should catch the ClientExceptionInterface and rethrow it as a RuntimeException
    $sut->create('https://example.com');
}

}
