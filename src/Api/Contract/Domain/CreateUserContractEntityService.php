<?php 

namespace App\Api\Contract\Domain;

use App\Dto\Input\CreateUserContractDtoInput;
use App\Entity\Contract;
use App\Entity\User;
use App\Entity\UserContract;

class CreateUserContractEntityService {

    public function createUserContractEntity(Contract $contract, User $user, CreateUserContractDtoInput $createUserContractDtoInput): UserContract
    {
        $deposited = $this->normalizeTokenValue($createUserContractDtoInput->deposited, $contract->getToken()->getDecimals());

        $userContract = new UserContract();
        $userContract->setContract($contract);
        $userContract->setCreatedAt(new \DateTimeImmutable());
        $userContract->setBalance((float)$deposited);
        $userContract->setUser($user);
        $userContract->setHash($createUserContractDtoInput->hash);

        $interest = round( ($userContract->getBalance() * ($userContract->getContract()->getRate() / 100)), $userContract->getContract()->getToken()->getDecimals());
        $total    = $userContract->getBalance() + $interest;

        $userContract->setInterests($interest);
        $userContract->setTotal($total);

        return $userContract;
    }

    private function normalizeTokenValue(string $value, int $tokenDecimals): string
    {
        $lenDif = strlen($value) - $tokenDecimals;

        if($lenDif < 0) {
            $value  = str_pad($value, ($lenDif * -1) + strlen($value), '0', STR_PAD_LEFT);
            $lenDif = strlen($value) - $tokenDecimals;
        }

        return substr($value, 0, $lenDif) . '.' . substr($value, $lenDif, strlen($value));
    }
}