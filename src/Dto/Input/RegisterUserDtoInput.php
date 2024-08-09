<?php

namespace App\Dto\Input;

use Symfony\Component\Validator\Constraints\NotBlank;

class RegisterUserDtoInput {

    public function __construct(
        #[NotBlank(message: 'Email cannot be empty')] 
        public readonly string $email,

        #[NotBlank(message: 'Name cannot be empty')]
        public readonly string $name,

        #[NotBlank(message: 'Password cannot be empty')]
        public readonly string $password,

        #[NotBlank(message: 'User type cannot be empty')]
        public readonly string $userType,
    ){}
}