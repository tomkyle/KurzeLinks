<?php

/**
 * tomkyle/kurzelinks
 *
 * Create short links with kurzelinks.de
 */

namespace tomkyle\KurzeLinks;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Client\ClientExceptionInterface;

class Psr18KurzeLinks implements KurzeLinksInterface
{
    /**
     * @param string                   $api            KurzeLinks.de API endpoint
     * @param string                   $key            KurzeLinks.de API key
     * @param ClientInterface          $httpClient     PSR-18 compliant HTTP client
     * @param RequestFactoryInterface  $requestFactory PSR-17 request factory
     * @param StreamFactoryInterface   $streamFactory  PSR-17 stream factory
     */
    public function __construct(protected string $api, protected string $key, protected ClientInterface $httpClient, protected RequestFactoryInterface $requestFactory, protected StreamFactoryInterface $streamFactory)
    {
    }

    #[\Override]
    public function create(string $url): string
    {
        // Create the request body
        $body = $this->streamFactory->createStream(http_build_query([
            'key' => $this->key,
            'json' => 1,
            'url' => $url,
        ]));

        // Create the request
        $request = $this->requestFactory->createRequest('POST', $this->api)
            ->withHeader('Accept-Encoding', 'gzip, deflate')
            ->withHeader('Connection', 'keep-alive')
            ->withBody($body);

        try {
            // Send the request and get the response
            $response = $this->httpClient->sendRequest($request);

            $code = $response->getStatusCode();
            $reason = $response->getReasonPhrase();

            if ($code !== 200) {
                $msg = sprintf("API response from %s: %s %s", $this->api, $code, $reason);
                throw new \UnexpectedValueException($msg);
            }

            $stream = (string) $response->getBody();

            if (!json_validate($stream)) {
                $msg = sprintf("Invalid JSON response: %s – %s", json_last_error(), json_last_error_msg());
                throw new \UnexpectedValueException($msg);
            }

            $body_decoded = json_decode($stream, true);

            if (!array_key_exists('shorturl', $body_decoded)) {
                throw new \UnexpectedValueException("Expected key 'shorturl' in JSON array");
            }

            if (!array_key_exists('url', $body_decoded['shorturl'])) {
                throw new \UnexpectedValueException("Expected key 'url' in 'shorturl' element");
            }

            return $body_decoded['shorturl']['url'];
        } catch (ClientExceptionInterface $clientException) {
            throw new \RuntimeException('HTTP request failed', 0, $clientException);
        }
    }
}
