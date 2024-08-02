<?php

namespace App\Api\Contract;

use App\Api\Contract\Domain\CreateUserContractEntityService;
use App\Dto\Input\CreateUserContractDtoInput;
use App\Entity\Contract;
use App\Entity\User;
use App\Entity\UserContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CreateUserContractService 
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CreateUserContractEntityService $createUserContractEntityService,
    ){}

    public function createUserContract(CreateUserContractDtoInput $createUserContractDtoInput, User|UserInterface $user): UserContract
    {
        $contract     = $this->em->getRepository(Contract::class)->findOneByAddress($createUserContractDtoInput->contractAddress);
        $userContract = $this->createUserContractEntityService->createUserContractEntity($contract, $user, $createUserContractDtoInput);
        $this->em->persist($userContract);
        $this->em->flush();

        return $userContract;

    }
}