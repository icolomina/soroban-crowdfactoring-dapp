<?php

namespace App\Dto\Output;

use App\Entity\Token;

class TokenDtoOutput 
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $code,
        public readonly string $address,
        public readonly string $createdAt,
        public readonly bool $enabled,
        public readonly string $issuer
    ){}

    public static function fromEntity(Token $token): self
    {
        return new self(
            $token->getId(),
            $token->getName(),
            $token->getCode(),
            $token->getAddress(),
            $token->getCreatedAt()->format('Y-m-d H:i'),
            $token->isEnabled(),
            $token->getIssuer()
        );
    }
}