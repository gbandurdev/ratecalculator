<?php

namespace App\Tests;

use App\CommissionCalculator;
use App\DTO\TransactionDTO;
use App\TransactionProcessor;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class TransactionProcessorTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testProcessFile()
    {
        $calculator = $this->createMock(CommissionCalculator::class);

        $calculator->expects($this->once())
            ->method('calculateCommission')
            ->with($this->isInstanceOf(TransactionDTO::class))
            ->willReturn(1.0);

        $filePath = tempnam(sys_get_temp_dir(), 'test');
        $line = json_encode([
            'bin' => '123456',
            'amount' => 100.0,
            'currency' => 'EUR'
        ]);
        file_put_contents($filePath, $line . PHP_EOL);

        $processor = new TransactionProcessor($calculator);

        $this->expectOutputString("1\n");
        $processor->processFile($filePath);

        unlink($filePath);
    }

    /**
     * @throws Exception
     */
    public function testProcessFileWithInvalidJson()
    {
        $calculator = $this->createMock(CommissionCalculator::class);

        $filePath = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($filePath, "not-a-json-line\n");

        $processor = new TransactionProcessor($calculator);

        $this->expectOutputRegex('/Error: Invalid JSON format\./');

        $processor->processFile($filePath);

        unlink($filePath);
    }

    /**
     * @throws Exception
     */
    public function testProcessFileWithMissingFields()
    {
        $calculator = $this->createMock(CommissionCalculator::class);

        $filePath = tempnam(sys_get_temp_dir(), 'test');
        $line = json_encode(['bin' => '123456']); // Missing amount and currency
        file_put_contents($filePath, $line . PHP_EOL);

        $processor = new TransactionProcessor($calculator);

        $this->expectOutputRegex('/Error: Invalid transaction data: missing amount, currency, or bin\./');
        $processor->processFile($filePath);

        unlink($filePath);
    }

    /**
     * @throws Exception
     */
    public function testProcessFileHandlesExceptionGracefully()
    {
        $calculator = $this->createMock(CommissionCalculator::class);
        $calculator->method('calculateCommission')
            ->willThrowException(new RuntimeException('Something went wrong'));

        $filePath = tempnam(sys_get_temp_dir(), 'test');
        $line = json_encode([
            'bin' => '123456',
            'amount' => 100.0,
            'currency' => 'EUR'
        ]);
        file_put_contents($filePath, $line . PHP_EOL);

        $processor = new TransactionProcessor($calculator);

        $this->expectOutputRegex('/Error processing transaction: Something went wrong/');
        $processor->processFile($filePath);

        unlink($filePath);
    }
}
