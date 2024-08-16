<?php

namespace App\Dto\Output;

use App\Entity\UserContract;

class UserContractDtoOutput 
{
    public function __construct(
        public readonly string $id,
        public readonly string $contractIssuer,
        public readonly string $contractLabel,
        public readonly string $contractAddress,
        public readonly string $token,
        public readonly float  $rate,
        public readonly string $createdAt,
        public readonly string $withdrawalDate,
        public readonly string $deposited,
        public readonly string $interest,
        public readonly string $total,
        public readonly ?string $hash
    ){}

    public static function fromEntity(UserContract $userContract): self
    {
        $claimMonths    = $userContract->getContract()->getClaimMonths();
        $withdrawalDate = (new \DateTime())->add(\DateInterval::createFromDateString("+ {$claimMonths} months"))->format('Y-m-d');

        return new self(
            $userContract->getId(),
            $userContract->getContract()->getIssuer()->getName(),
            $userContract->getContract()->getLabel(),
            $userContract->getContract()->getAddress(),
            $userContract->getContract()->getToken()->getName() . ' - ' . $userContract->getContract()->getToken()->getCode(),
            $userContract->getContract()->getRate(),
            $userContract->getCreatedAt()->format('Y-m-d H:i'),
            $withdrawalDate,
            $userContract->getBalance(),
            $userContract->getInterests(),
            $userContract->getTotal(),
            $userContract->getHash()
        );
    }
}