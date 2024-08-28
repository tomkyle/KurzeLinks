
# tomkyle/kurzelinks

**tomkyle/kurzelinks** is a PHP library designed to create short links using the [kurzelinks.de](https://kurzelinks.de) service. This library provides different implementations of the `KurzeLinksInterface` to allow developers to integrate and extend the short link creation functionality with ease.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
  - [GuzzleKurzeLinks](#guzzlekurzelinks)
  - [RateLimitKurzeLinks](#ratelimitkurzelinks)
  - [Psr6CacheKurzeLinks](#psr6cachekurzelinks)
  - [PassthroughKurzeLinks](#passthroughkurzelinks)
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

### RateLimitKurzeLinks

The `RateLimitKurzeLinks` class is a decorator for any `KurzeLinksInterface` implementation. It introduces a rate limit by pausing execution between requests.

#### Example

```php
use tomkyle\KurzeLinks\GuzzleKurzeLinks;
use tomkyle\KurzeLinks\RateLimitKurzeLinks;

$api = 'https://kurzelinks.de/api';
$key = 'your_api_key';
$innerKurzeLinks = new GuzzleKurzeLinks($api, $key);
$rateLimitedKurzeLinks = new RateLimitKurzeLinks($innerKurzeLinks, 4000); // 4000ms sleep

$shortUrl = $rateLimitedKurzeLinks->create('https://example.com');
echo $shortUrl;  // Outputs the shortened URL
```

### Psr6CacheKurzeLinks

The `Psr6CacheKurzeLinks` class is a decorator that caches the results of the `create` method using a PSR-6 compatible cache pool.

#### Example

```php
use tomkyle\KurzeLinks\GuzzleKurzeLinks;
use tomkyle\KurzeLinks\Psr6CacheKurzeLinks;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

$api = 'https://kurzelinks.de/api';
$key = 'your_api_key';
$innerKurzeLinks = new GuzzleKurzeLinks($api, $key);
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
