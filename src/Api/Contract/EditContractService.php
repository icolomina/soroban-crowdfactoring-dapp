<?php

namespace App\Api\Contract;

use App\Dto\Output\ContractDtoOutput;
use App\Entity\Contract;
use App\Stellar\Soroban\Contract\InteractManager;

class EditContractService {

    public function __construct(
        private readonly InteractManager $interactManager
    ) {}

    public function editContract(Contract $contract): ContractDtoOutput
    {
        $balance = $this->interactManager->getContractBalance($contract);
        $contractOutput =  ContractDtoOutput::fromEntity($contract);
        $contractOutput->currentFunds = $balance;

        return $contractOutput;
    }
}