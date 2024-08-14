<?php

namespace App\Api\User;

use App\Api\User\Domain\CreateUserEntityService;
use App\Dto\Input\RegisterUserDtoInput;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class RegisterUserService {

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CreateUserEntityService $createUserEntityService
    ){}

    public function registerUser(RegisterUserDtoInput $registerUserDtoInput): void
    {
        $user = $this->createUserEntityService->createUserEntity($registerUserDtoInput);
        $existingUserSameEmail = $this->em->getRepository(User::class)->findOneByEmail($user->getEmail());
        if($existingUserSameEmail) {
            $violations = new ConstraintViolationList(
                [
                    new ConstraintViolation(
                        sprintf('A user with email %s already exists', $user->getEmail()),
                        null,
                        [], 
                        null,
                        'email',
                        $user->getEmail()
                    )
                ]
            );
            throw new ValidationFailedException(null, $violations);
        }
        $this->em->persist($user);
        $this->em->flush($user);
    }
}