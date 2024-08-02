<?php

namespace App\Dto\Stellar;

class TransactionResult {

    public function __construct(
        public readonly string $hash
    ){}
}