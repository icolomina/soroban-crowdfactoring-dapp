<?php

namespace App\Api\Contract;

use App\Entity\Contract;
use App\Stellar\Soroban\Contract\InteractManager;
use Doctrine\ORM\EntityManagerInterface;

class StopContractDepositsService {

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly InteractManager $interactManager
    ){}

    public function stopContractDeposits(Contract $contract): Contract
    {
        $this->interactManager->stopDeposits($contract);

        $contract->setFundsReached(true);
        $this->em->persist($contract);
        $this->em->flush();

        return $contract;

    }
}