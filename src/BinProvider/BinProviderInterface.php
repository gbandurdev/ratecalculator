<?php

namespace App\BinProvider;

interface BinProviderInterface
{
    /**
     * Get BIN (Bank Identification Number) details from a provider.
     *
     * @param string $bin The BIN number (usually the first 6–8 digits of a card).
     * @return array Associative array with BIN data (country, scheme, type, etc).
     */
    public function getBinDetails(string $bin): array;
}