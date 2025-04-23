<?php

namespace App\RateProvider;

/**
 * Contract for classes that provide exchange rates.
 */
interface RateProviderInterface
{
    /**
     * Get the exchange rate for a given currency.
     *
     * @param string $currency The 3-letter ISO currency code (e.g., "USD", "EUR").
     * @return float The exchange rate relative to a base (typically EUR or USD).
     */
    public function getExchangeRate(string $currency): float;
}