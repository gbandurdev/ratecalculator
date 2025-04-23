<?php

namespace App\Utils;

use JsonException;
use RuntimeException;

final class JsonUtils
{
    /**
     * Decode a JSON string into an associative array.
     *
     * @param string $json
     * @return array<string, mixed>
     * @throws RuntimeException If JSON decoding fails.
     * @throws JsonException
     */
    public static function decode(string $json): array
    {
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }
}