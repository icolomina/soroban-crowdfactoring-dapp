<?php 

namespace App\Api\Contract\Domain;

use App\Dto\Input\CreateContractDto;
use App\Entity\Contract;
use App\Entity\Token;
use App\Entity\User;

class CreateContractEntityService {

    public function createContractEntity(CreateContractDto $createContractDto, Token $token, User $user): Contract
    {
        $contract = new Contract();
        $contract->setCreatedAt(new \DateTimeImmutable());
        $contract->setIssuer($user);
        $contract->setRate($createContractDto->rate);
        $contract->setToken($token);
        $contract->setInitialized(false);
        $contract->setClaimMonths($createContractDto->claimMonths);
        $contract->setLabel($createContractDto->label);
        $contract->setDescription($createContractDto->description);

        return $contract;
    }
}