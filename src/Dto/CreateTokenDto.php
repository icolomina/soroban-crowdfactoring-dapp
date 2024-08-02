<?php

namespace App\Dto;

class CreateTokenDto {

    public function __construct(
        public readonly string $name,
        public readonly string $code,
        public readonly int $decimals
    ){}
}