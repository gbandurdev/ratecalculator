<?php

namespace App;

use App\DTO\TransactionDTO;

class TransactionProcessor
{
    private CommissionCalculator $calculator;

    public function __construct(CommissionCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function processFile(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $transactionData = json_decode($line, true);

            if ($transactionData === null) {
                echo "Error: Invalid JSON format.\n";
                continue;
            }

            if (
                !isset($transactionData['amount']) ||
                !isset($transactionData['currency']) ||
                !isset($transactionData['bin'])
            ) {
                echo "Error: Invalid transaction data: missing amount, currency, or bin.\n";
                continue;
            }

            try {
                $transaction = new TransactionDTO(
                    (float) $transactionData['amount'],
                    (string) $transactionData['currency'],
                    (string) $transactionData['bin']
                );

                $commission = $this->calculator->calculateCommission($transaction);
                echo $commission . "\n";

            } catch (\Throwable $e) {
                echo 'Error processing transaction: ' . $e->getMessage() . "\n";
            }
        }
    }
}
