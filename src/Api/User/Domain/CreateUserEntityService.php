<?php

namespace App\Api\User\Domain;

use App\Dto\Input\RegisterUserDtoInput;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserEntityService {

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ){}

    public function createUserEntity(RegisterUserDtoInput $registerUserDtoInput): User
    {
        $user = new User();
        $user->setEmail($registerUserDtoInput->email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $registerUserDtoInput->password));
        $user->setName($registerUserDtoInput->name);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setRoles([$registerUserDtoInput->userType]);

        return $user;
    }
}