<?php

namespace App\BinProvider;

use App\Http\ApiClient;
use JsonException;

final readonly class BinProvider implements BinProviderInterface
{
    public function __construct(
        private string $url,
        private string $apiKey,
        private ApiClient $client,
    ) {}

    /**
     * @throws JsonException
     */
    public function getBinDetails(string $bin): array
    {
        $headers = [
            'Authorization' => "Bearer {$this->apiKey}",
        ];

        return $this->client->get("{$this->url}{$bin}", $headers);
    }
}
