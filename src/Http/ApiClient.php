<?php

namespace App\Http;

use App\Utils\JsonUtils;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use RuntimeException;

readonly class ApiClient
{
    public function __construct(
        private Client $client,
    ) {}

    /**
     * Send a GET request and return the JSON-decoded response.
     *
     * @param string $url
     * @param array<string, string> $headers
     * @return array<string, mixed>
     * @throws RuntimeException|JsonException
     */
    public function get(string $url, array $headers = []): array
    {
        try {
            $response = $this->client->get($url, ['headers' => $headers]);

            return JsonUtils::decode($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            throw new RuntimeException('API request failed: ' . $e->getMessage(), 0, $e);
        }
    }
}