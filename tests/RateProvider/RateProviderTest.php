<?php

namespace App\Tests\ExchangeRateProvider;

use App\RateProvider\RateProvider;
use App\Http\ApiClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use JsonException;
use PHPUnit\Framework\TestCase;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class RateProviderTest extends TestCase
{
    /**
     * @throws InvalidArgumentException|JsonException
     */
    public function testGetExchangeRateSuccess()
    {
        $mockResponse = json_encode([
            'rates' => [
                'USD' => 1.2
            ]
        ]);

        $mock = new MockHandler([
            new Response(200, [], $mockResponse),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handlerStack]);
        $apiClient = new ApiClient($httpClient);

        $cache = new ArrayAdapter();

        $provider = new RateProvider(
            'https://api.apilayer.com/exchangerates_data/latest',
            'api-key',
            $apiClient,
            $cache
        );

        $rate = $provider->getExchangeRate('USD');
        $this->assertEquals(1.2, $rate);

        // Run again to ensure it's coming from cache
        $rateCached = $provider->getExchangeRate('USD');
        $this->assertEquals(1.2, $rateCached);
    }

    /**
     * @throws InvalidArgumentException|JsonException
     */
    public function testGetExchangeRateFailure()
    {
        $mock = new MockHandler([
            new RequestException('Error', new Request('GET', 'test')),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handlerStack]);
        $apiClient = new ApiClient($httpClient);

        $cache = new ArrayAdapter();

        $provider = new RateProvider(
            'https://api.apilayer.com/exchangerates_data/latest',
            'api-key',
            $apiClient,
            $cache
        );

        $this->expectException(RuntimeException::class);
        $provider->getExchangeRate('USD');
    }
}
