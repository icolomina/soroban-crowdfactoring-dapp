<?php

namespace App\Dto\Input;

class CreateUserContractDtoInput {

    public function __construct(
        public readonly string $contractAddress,
        public readonly string $hash,
        public readonly string $deposited
    ){}
}