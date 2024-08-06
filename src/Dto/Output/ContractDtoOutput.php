<?php

namespace App\Dto\Output;

use App\Entity\Contract;

class ContractDtoOutput 
{
    public function __construct(
        public readonly string $id,
        public readonly string $address,
        public readonly string $token,
        public readonly string $tokenCode,
        public readonly float $rate,
        public readonly string $createdAt,
        public readonly bool $initialized,
        public readonly string $issuer,
        public readonly int $claimMonths,
        public readonly string $label,
        public readonly bool $fundsReached,
        public readonly ?string $description,
        public ?string $currentFunds
    ){}

    public static function fromEntity(Contract $contract): self
    {
        return new self(
            $contract->getId(),
            $contract->getAddress(),
            $contract->getToken()->getName() . ' - ' . $contract->getToken()->getCode(),
            $contract->getToken()->getCode(),
            $contract->getRate(),
            $contract->getCreatedAt()->format('Y-m-d H:i'),
            $contract->isInitialized(),
            $contract->getIssuer()->getName(),
            $contract->getClaimMonths(),
            $contract->getLabel(),
            $contract->isFundsReached(),
            $contract->getDescription(),
            null
        );
    }
}