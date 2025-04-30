<?php

namespace App;

use App\DTO\TransactionDTO;
use App\DTO\BinDetailsDTO;
use App\BinProvider\BinProviderInterface;
use App\RateProvider\RateProviderInterface;

readonly class CommissionCalculator
{
    public function __construct(
        private BinProviderInterface  $binProvider,
        private RateProviderInterface $exchangeRateProvider,
        private array                 $euCountries,
        private float                 $euCommissionRate,
        private float                 $nonEuCommissionRate,
        private array                 $currencyDecimals
    ) {}

    public function calculateCommission(TransactionDTO $transaction): float
    {
        $rawBinData = $this->binProvider->getBinDetails($transaction->bin);
        $binDetails = BinDetailsDTO::fromArray($rawBinData);

        $isEu = $this->isEu($binDetails->countryCode);
        $rate = $this->exchangeRateProvider->getExchangeRate($transaction->currency);
        $amountFixed = $this->convertToEur($transaction->amount, $transaction->currency, $rate);

        $commissionRate = $isEu ? $this->euCommissionRate : $this->nonEuCommissionRate;
        $commission = $amountFixed * $commissionRate;

        return $this->roundCommission($commission, $transaction->currency);
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
