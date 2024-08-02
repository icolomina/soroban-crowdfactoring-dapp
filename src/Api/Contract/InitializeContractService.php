<?php

namespace App\Api\Contract;

use App\Entity\Contract;
use App\Stellar\Soroban\Contract\InteractManager;
use Doctrine\ORM\EntityManagerInterface;

class InitializeContractService 
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly InteractManager $interactManager
    ){}

    public function initializeContract(Contract $contract): Contract
    {
        $this->interactManager->initContract($contract);

        $contract->setInitialized(true);
        $this->em->persist($contract);
        $this->em->flush();

        return $contract;

    }
}