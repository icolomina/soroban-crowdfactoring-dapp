<?php

namespace App\Dto\Input;

use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateContractDto {

    public function __construct(
        #[NotBlank(message: 'Token cannot be empty')] 
        public readonly string $token,

        #[NotBlank(message: 'Rate cannot be empty')]
        public readonly string $rate,

        #[NotBlank(message: 'Months cannot be empty')]
        #[GreaterThan(0, message: 'Months must be greater than 0')]
        public readonly int $claimMonths,

        #[NotBlank(message: 'Label cannot be empty')]
        public readonly string $label,

        #[NotBlank(message: 'Descrption cannot be empty')]
        public readonly ?string $description = null
    ){}
}