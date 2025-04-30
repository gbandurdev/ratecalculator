<?php

namespace App\DTO;

use InvalidArgumentException;

readonly class TransactionDTO
{
    public function __construct(
        public float  $amount,
        public string $currency,
        public string $bin
    ) {}

    public static function fromArray(array $data): self
    {
        if (!isset($data['bin'], $data['amount'], $data['currency'])) {
            throw new InvalidArgumentException('Invalid transaction data: missing amount, currency, or bin.');
        }

        return new self(
            (float) $data['amount'],
            (string) $data['currency'],
            (string) $data['bin']
        );
    }
}
