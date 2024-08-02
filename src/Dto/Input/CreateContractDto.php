<?php

namespace App\Dto\Input;

class CreateContractDto {

    public function __construct(
        public readonly string $token,
        public readonly int|float $rate,
        public readonly int $claimMonths,
        public readonly string $label,
        public readonly ?string $description = null
    ){}
}