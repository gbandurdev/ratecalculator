<?php

namespace App\Tests;

use App\BinProvider\BinProviderInterface;
use App\CommissionCalculator;
use App\DTO\TransactionDTO;
use App\RateProvider\RateProviderInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CommissionCalculatorTest extends TestCase
{
    private CommissionCalculator $calculator;
    private MockObject|BinProviderInterface $binProvider;
    private MockObject|RateProviderInterface $exchangeRateProvider;

    private array $euCountries = ['DE', 'FR', 'IT', 'ES', 'PT'];
    private float $euRate = 0.01;
    private float $nonEuRate = 0.02;
    private array $currencyDecimals = [
        'EUR' => 2,
        'USD' => 2,
        'JPY' => 0,
    ];

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->binProvider = $this->createMock(BinProviderInterface::class);
        $this->exchangeRateProvider = $this->createMock(RateProviderInterface::class);

        $this->calculator = new CommissionCalculator(
            $this->binProvider,
            $this->exchangeRateProvider,
            $this->euCountries,
            $this->euRate,
            $this->nonEuRate,
            $this->currencyDecimals
        );
    }

    public function testCalculateCommissionForEuCountry()
    {
        $this->binProvider->method('getBinDetails')->willReturn([
            'country' => ['alpha2' => 'DE']
        ]);

        $this->exchangeRateProvider->method('getExchangeRate')->willReturn(1.0);

        $transaction = new TransactionDTO(100.0, 'EUR', '123456');
        $commission = $this->calculator->calculateCommission($transaction);

        $this->assertEquals(1.0, $commission);
    }

    public function testCalculateCommissionForNonEuCountry()
    {
        $this->binProvider->method('getBinDetails')->willReturn([
            'country' => ['alpha2' => 'US']
        ]);

        $this->exchangeRateProvider->method('getExchangeRate')->willReturn(1.2);

        $transaction = new TransactionDTO(120.0, 'USD', '123456');
        $commission = $this->calculator->calculateCommission($transaction);

        $this->assertEquals(2.0, $commission);
    }

    public function testCalculateCommissionWithCeiling()
    {
        $this->binProvider->method('getBinDetails')->willReturn([
            'country' => ['alpha2' => 'DE']
        ]);

        $this->exchangeRateProvider->method('getExchangeRate')->willReturn(1.0);

        $transaction1 = new TransactionDTO(100.4618, 'EUR', '123456');
        $transaction2 = new TransactionDTO(100.4600, 'EUR', '123456');

        $this->assertEquals(1.01, $this->calculator->calculateCommission($transaction1));
        $this->assertEquals(1.01, $this->calculator->calculateCommission($transaction2));
    }
}
