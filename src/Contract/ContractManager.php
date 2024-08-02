<?php

namespace App\Contract;

use App\Dto\Output\ContractDtoOutput;
use App\Entity\Contract;
use App\Entity\User;
use App\Entity\UserContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ContractManager {

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer
    ) { }

    public function getContracts(UserInterface|User $user): array
    {
        $contracts = $this->em->getRepository(Contract::class)->findBy(['issuer' => $user]);

        $contractsOutput = [];
        foreach($contracts as $contract){
            $contractsOutput[] = $this->serializer->normalize(ContractDtoOutput::fromEntity($contract));
        }

        return $contractsOutput;
    }   

    public function getAvailableContracts(UserInterface|User $user): array
    {
        $contracts = $this->em->getRepository(Contract::class)->findBy(['initialized' => true]);
        $userContracts = $user->getContracts();

        $contractsOutput = [];
        foreach($contracts as $contract){
            $contractsUser = array_filter($userContracts->toArray(), fn(UserContract $uc) => $uc->getContract()->getId() === $contract->getId());
            if(empty($contractsUser)){
                $contractsOutput[] = $this->serializer->normalize(ContractDtoOutput::fromEntity($contract));
            }
        }

        return $contractsOutput;
    }
}