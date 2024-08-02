<?php

namespace App\Api\Contract;

use App\Dto\Output\UserContractDtoOutput;
use App\Entity\User;
use App\Entity\UserContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

class GetUserContractService {

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer
    ){}

    public function getUserContracts(User|UserInterface $user): array
    {
        $userContracts = $this->em->getRepository(UserContract::class)->findBy(['user' => $user]);
        
        $userContractsOutput = [];
        foreach($userContracts as $userContract){
            $userContractsOutput[] = $this->serializer->normalize(UserContractDtoOutput::fromEntity($userContract));
        }

        return $userContractsOutput;
    }
}