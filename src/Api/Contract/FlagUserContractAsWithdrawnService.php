<?php

namespace App\Api\Contract;

use App\Entity\UserContract;
use Doctrine\ORM\EntityManagerInterface;

class FlagUserContractAsWithdrawnService {

    public function __construct(
        private readonly EntityManagerInterface $em
    ){}

    public function flagAsWithdrawn(UserContract $userContract): void
    {
        $userContract->setWithdrawn(true);
        $this->em->persist($userContract);
        $this->em->flush();
    }
}