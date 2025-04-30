<?php
namespace App\DTO;

use InvalidArgumentException;

readonly class BinDetailsDTO
{
    public function __construct(
        public string $countryCode
    ) {}

    public static function fromArray(array $data): self
    {
        if (!isset($data['country']['alpha2'])) {
            throw new InvalidArgumentException('Invalid BIN data.');
        }

        return new self($data['country']['alpha2']);
    }
}
