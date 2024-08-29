# tomkyle/kurzelinks

**tomkyle/kurzelinks** is a PHP library designed to create short links using the [kurzelinks.de](https://kurzelinks.de) service. This library provides different implementations of the `KurzeLinksInterface` to allow developers to integrate and extend the short link creation functionality with ease.

[![Packagist](https://img.shields.io/packagist/v/tomkyle/kurzelinks.svg?style=flat)](https://packagist.org/packages/tomkyle/kurzelinks)
[![PHP version](https://img.shields.io/packagist/php-v/tomkyle/kurzelinks.svg)](https://packagist.org/packages/tomkyle/kurzelinks)
[![Tests](https://github.com/tomkyle/KurzeLinks/actions/workflows/php.yml/badge.svg)](https://github.com/tomkyle/KurzeLinks/actions/workflows/php.yml)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
  - [GuzzleKurzeLinks](#guzzlekurzelinks)
  - [Psr18KurzeLinks](#psr18kurzelinks)
  - [RateLimitKurzeLinks](#ratelimitkurzelinks)
  - [Psr6CacheKurzeLinks](#psr6cachekurzelinks)
  - [PassthroughKurzeLinks](#passthroughkurzelinks)
  - [CallableKurzeLinks](#callablekurzelinks)
- [Best Practice: Usage Recommendation](#best-practice-usage-recommendation)
- [Interface](#interface)
  - [KurzeLinksInterface](#kurzelinksinterface)

## Installation

You can install this library via Composer:

```bash
composer require tomkyle/kurzelinks
```

## Usage

### GuzzleKurzeLinks

The `GuzzleKurzeLinks` class is an implementation of `KurzeLinksInterface` that uses GuzzleHTTP to interact with the [KurzeLinks.de API.](https://kurzelinks.de/kurz-url-api)

#### Example

```php
use tomkyle\KurzeLinks\GuzzleKurzeLinks;

$api = 'https://kurzelinks.de/api';
$key = 'your_api_key';
$kurzeLinks = new GuzzleKurzeLinks($api, $key);

$shortUrl = $kurzeLinks->create('https://example.com');
echo $shortUrl;  // Outputs the shortened URL
```

### Psr18KurzeLinks

The `Psr18KurzeLinks` class is an implementation of `KurzeLinksInterface` that uses a PSR-18 compliant HTTP client to interact with the [KurzeLinks.de API.](https://kurzelinks.de/kurz-url-api)

#### Example

```php
use tomkyle\KurzeLinks\Psr18KurzeLinks;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

$api = 'https://kurzelinks.de/api';
$key = 'your_api_key';

// Assume you have a PSR-18 compliant HTTP client, 
// and PSR-17 request and stream factories
$httpClient = new YourPsr18Client();
$requestFactory = new YourRequestFactory();
$streamFactory = new YourStreamFactory();

$kurzeLinks = new Psr18KurzeLinks($api, $key, $httpClient, $requestFactory, $streamFactory);

$shortUrl = $kurzeLinks->create('https://example.com');
echo $shortUrl;  // Outputs the shortened URL
```

### RateLimitKurzeLinks

The `RateLimitKurzeLinks` class is a decorator for any `KurzeLinksInterface` implementation. It introduces a rate limit by pausing execution between requests.

#### Example

```php
use tomkyle\KurzeLinks\Psr18KurzeLinks;
use tomkyle\KurzeLinks\RateLimitKurzeLinks;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

$api = 'https://kurzelinks.de/api';
$key = 'your_api_key';

// Assume you have a PSR-18 compliant HTTP client, request factory, and stream factory
$httpClient = new YourPsr18Client();
$requestFactory = new YourRequestFactory();
$streamFactory = new YourStreamFactory();

$innerKurzeLinks = new Psr18KurzeLinks($api, $key, $httpClient, $requestFactory, $streamFactory);
$rateLimitedKurzeLinks = new RateLimitKurzeLinks($innerKurzeLinks, 4000); // 4000ms sleep

$shortUrl = $rateLimitedKurzeLinks->create('https://example.com');
echo $shortUrl;  // Outputs the shortened URL
```

### Psr6CacheKurzeLinks

The `Psr6CacheKurzeLinks` class is a decorator that caches the results of the `create` method using a PSR-6 compatible cache pool.

#### Example

```php
use tomkyle\KurzeLinks\Psr18KurzeLinks;
use tomkyle\KurzeLinks\Psr6CacheKurzeLinks;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

$api = 'https://kurzelinks.de/api';
$key = 'your_api_key';

// Assume you have a PSR-18 compliant HTTP client, request factory, and stream factory
$httpClient = new YourPsr18Client();
$requestFactory = new YourRequestFactory();
$streamFactory = new YourStreamFactory();

$innerKurzeLinks = new Psr18KurzeLinks($api, $key, $httpClient, $requestFactory, $streamFactory);
$cachePool = new FilesystemAdapter();

$cachedKurzeLinks = new Psr6CacheKurzeLinks($innerKurzeLinks, $cachePool);

$shortUrl = $cachedKurzeLinks->create('https://example.com');
echo $shortUrl;  // Outputs the shortened URL, possibly from cache
```

### PassthroughKurzeLinks

The `PassthroughKurzeLinks` class is a simple implementation of `KurzeLinksInterface` that returns the original URL without shortening it. This can be useful for testing or as a default behavior.

#### Example

```php
use tomkyle\KurzeLinks\PassthroughKurzeLinks;

$passthroughKurzeLinks = new PassthroughKurzeLinks();

$shortUrl = $passthroughKurzeLinks->create('https://example.com');
echo $shortUrl;  // Outputs the original URL: https://example.com
```

### CallableKurzeLinks

The `CallableKurzeLinks` class is a decorator that allows a `KurzeLinksInterface` implementation to be invoked directly as a callable.

#### Example

```php
use tomkyle\KurzeLinks\CallableKurzeLinks;
use tomkyle\KurzeLinks\Psr18KurzeLinks;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

$api = 'https://kurzelinks.de/api';
$key = 'your_api_key';

// Assume you have a PSR-18 compliant HTTP client, request factory, and stream factory
$httpClient = new YourPsr18Client();
$requestFactory = new YourRequestFactory();
$streamFactory = new YourStreamFactory();

$innerKurzeLinks = new Psr18KurzeLinks($api, $key, $httpClient, $requestFactory, $streamFactory);
$callableKurzeLinks = new CallableKurzeLinks($innerKurzeLinks);

// Use as callable
$shortUrl = $callableKurzeLinks('https://example.com');
echo $shortUrl;  // Outputs the shortened URL

// Use create method directly
$shortUrl = $callableKurzeLinks->create('https://example.com');
echo $shortUrl;  // Outputs the shortened URL
```

## Best Practice: Usage Recommendation

To ensure efficient usage of the [kurzelinks.de](https://kurzelinks.de) API, especially considering the restrictive rate limits, it is highly recommended to utilize the `RateLimitKurzeLinks` and `Psr6CacheKurzeLinks` decorators together. These wrappers help manage API requests effectively by limiting the rate at which requests are sent and caching responses to avoid unnecessary duplicate requests.

### Why Use Rate Limiting?

The `RateLimitKurzeLinks` decorator enforces a delay between API requests. This is crucial when working with services that impose strict limits on the number of requests allowed per hour. By introducing a delay, you reduce the risk of exceeding these limits and receiving errors from the API due to overuse.

### Why Use Caching?

The `Psr6CacheKurzeLinks` decorator caches the results of the `create` method. This is particularly useful when the same URL is shortened multiple times within a short period. Instead of making multiple API requests, the cached result is returned, which saves on API quota and improves performance by reducing network latency.

### Recommended Implementation

Below is a recommended setup that combines both `RateLimitKurzeLinks` and `Psr6CacheKurzeLinks`:

```php
use tomkyle\KurzeLinks\Psr18KurzeLinks;
use tomkyle\KurzeLinks\RateLimitKurzeLinks;
use tomkyle\KurzeLinks\Psr6CacheKurzeLinks;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

$api = 'https://kurzelinks.de/api';
$key = 'your_api_key';

// Assume you have a PSR-18 compliant HTTP client, request factory, and stream factory
$httpClient = new YourPsr18Client();
$requestFactory = new YourRequestFactory();
$streamFactory = new YourStreamFactory();

$kurze_links = new Psr18KurzeLinks($api, $key, $httpClient, $requestFactory, $streamFactory);

// Wrap the cached implementation with rate limiting
$rate_limited = new RateLimitKurzeLinks(kurze_links, 4000); // 4000ms sleep

// Create a PSR-6 cache pool (e.g., using Symfony's FilesystemAdapter)
// and wrap the rate-limited implementation with caching
$cachePool = new FilesystemAdapter();
$cached = new Psr6CacheKurzeLinks($rate_limited, $cachePool);

// Use the cached, rate-limited implementation
$shortUrl = $cached->create('https://example.com');
echo $shortUrl;  // Outputs the shortened URL
```

## Interface

### KurzeLinksInterface

The `KurzeLinksInterface` defines the contract for creating short links.

#### Method

- `create(string $url): string`  
  Creates a short link representation for the given URL.

#### Example

Any class implementing this interface must define the `create` method:

```php
use tomkyle\KurzeLinks\KurzeLinksInterface;

class MyKurzeLinks implements KurzeLinksInterface
{
    public function create(string $url): string
    {
        // Your implementation here
    }
}
```

## License

This library is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.
