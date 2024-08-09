<?php

namespace App\Api\User;

use App\Api\User\Domain\CreateUserEntityService;
use App\Dto\Input\RegisterUserDtoInput;
use Doctrine\ORM\EntityManagerInterface;

class RegisterUserService {

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CreateUserEntityService $createUserEntityService
    ){}

    public function registerUser(RegisterUserDtoInput $registerUserDtoInput): void
    {
        $user = $this->createUserEntityService->createUserEntity($registerUserDtoInput);
        $this->em->persist($user);
        $this->em->flush($user);
    }
}