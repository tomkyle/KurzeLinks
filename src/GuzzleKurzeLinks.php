<?php

/**
 * tomkyle/kurzelinks
 *
 * Create short links with kurzelinks.de
 */

namespace tomkyle\KurzeLinks;

use GuzzleHttp\Client;

class GuzzleKurzeLinks implements KurzeLinksInterface
{
    /**
     * The Guzzle client
     */
    protected Client $guzzle;

    /**
     * @param string      $api    KurzeLinks.de API endpint
     * @param string      $key    KurzeLinks.de API key
     * @param Client|null $client Optional: Custom Guzzle client
     */
    public function __construct(protected string $api, protected string $key, Client $client = null)
    {
        $this->guzzle = $client ?: new Client();
    }

    #[\Override]
    public function create(string $url): string
    {

        $response = $this->guzzle->request('POST', $this->api, [
            'headers' => [
                'Accept-Encoding' => 'gzip, deflate',
                'Connection' => 'keep-alive',
            ],
            'form_params' => [
                'key' => $this->key,
                'json' => 1,
                'url' => $url,
            ],
        ]);

        $code = $response->getStatusCode();
        $reason = $response->getReasonPhrase();
        if ($code !== 200) {
            $msg = sprintf("API response from %s: %s %s", $this->api, $code, $reason);
            throw new \UnexpectedValueException($msg);
        }

        $stream = $response->getBody();

        if (!json_validate($stream)) {
            $msg = sprintf("Invalid JSON response: %s – %s", json_last_error(), json_last_error_msg());
            throw new \UnexpectedValueException($msg);
        }

        $body_decoded = json_decode($stream, associative: true);

        if (!array_key_exists('shorturl', $body_decoded)) {
            throw new \UnexpectedValueException("Expected key 'shorturl' in JSON array");
        }

        if (!array_key_exists('url', $body_decoded['shorturl'])) {
            throw new \UnexpectedValueException("Expected key 'url' in 'shorturl' element");
        }

        return $body_decoded['shorturl']['url'];
    }
}
