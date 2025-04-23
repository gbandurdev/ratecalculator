<?php

namespace App;

use App\BinProvider\BinProviderInterface;
use App\RateProvider\RateProviderInterface;
use RuntimeException;

readonly class CommissionCalculator
{
    public function __construct(
        private BinProviderInterface  $binProvider,
        private RateProviderInterface $exchangeRateProvider,
        private array                 $euCountries,
        private float                 $euCommissionRate, // Property promotion for EU commission rate
        private float                 $nonEuCommissionRate, // Property promotion for non-EU commission rate
        private array                 $currencyDecimals
    ) {}

    public function calculateCommission(float $amount, string $currency, string $bin): float
    {
        $binDetails = $this->binProvider->getBinDetails($bin);
        $countryCode = $binDetails['country']['alpha2'] ?? null;

        if ($countryCode === null) {
            throw new RuntimeException('Invalid BIN details: Missing country code.');
        }

        $isEu = $this->isEu($countryCode);
        $rate = $this->exchangeRateProvider->getExchangeRate($currency);
        $amountFixed = $this->convertToEur($amount, $currency, $rate);

        $commissionRate = $isEu ? $this->euCommissionRate : $this->nonEuCommissionRate;
        $commission = $amountFixed * $commissionRate;

        return $this->roundCommission($commission, $currency);
    }

    private function isEu(string $countryCode): bool
    {
        return in_array($countryCode, $this->euCountries, true);
    }

    private function convertToEur(float $amount, string $currency, float $rate): float
    {
        if ($currency === 'EUR' || $rate == 0) {
            return $amount;
        }
        return $amount / $rate;
    }

    private function roundCommission(float $amount, string $currency): float
    {
        $decimals = $this->getCurrencyDecimals($currency);
        $multiplier = pow(10, $decimals);
        return ceil($amount * $multiplier) / $multiplier;
    }

    private function getCurrencyDecimals(string $currency): int
    {
        $currency = strtoupper($currency);
        return $this->currencyDecimals[$currency] ?? 2;
    }
}
