<?php

namespace App\RateProvider;

use App\Http\ApiClient;
use JsonException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;

final readonly class RateProvider implements RateProviderInterface
{
    private const int CACHE_TTL = 3600;
    private const string CACHE_PREFIX = 'exchange_rate_';

    public function __construct(
        private string $url,
        private string $apiKey,
        private ApiClient $client,
        private CacheItemPoolInterface $cache,
    ) {}

    /**
     * Get the exchange rate for a specific currency.
     *
     * @param string $currency The 3-letter currency code (e.g., "USD", "EUR").
     * @return float The current exchange rate.
     * @throws InvalidArgumentException If the cache fetch fails.
     * @throws RuntimeException|JsonException If the rate is not found in the API response.
     */
    public function getExchangeRate(string $currency): float
    {
        $cacheKey = $this->getCacheKey($currency);

        $item = $this->cache->getItem($cacheKey);
        if ($item->isHit()) {
            return (float) $item->get();
        }

        $rate = $this->fetchRateFromApi($currency);

        $item->set($rate)->expiresAfter(self::CACHE_TTL);
        $this->cache->save($item);

        return $rate;
    }

    private function getCacheKey(string $currency): string
    {
        return self::CACHE_PREFIX . strtolower($currency);
    }

    /**
     * Fetch the exchange rate from the external API.
     *
     * @param string $currency
     * @return float
     * @throws RuntimeException|JsonException
     */
    private function fetchRateFromApi(string $currency): float
    {
        $response = $this->client->get($this->url, [
                'apikey' => $this->apiKey,
        ]);

        $rate = $response['rates'][$currency] ?? null;

        if (!is_numeric($rate)) {
            throw new RuntimeException("Exchange rate for '{$currency}' not found in API response.");
        }

        return (float) $rate;
    }
}