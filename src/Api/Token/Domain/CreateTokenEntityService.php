<?php 

namespace App\Api\Token\Domain;

use App\Dto\CreateTokenDto;
use App\Entity\Token;

class CreateTokenEntityService {

    public function createTokenEntity(CreateTokenDto $createTokenDto, string $address): Token
    {
        $token = new Token();
        $token->setCode($createTokenDto->code);
        $token->setDecimals($createTokenDto->decimals);
        $token->setName($createTokenDto->name);
        $token->setCreatedAt(new \DateTimeImmutable());
        $token->setAddress($address);

        return $token;
    }
}