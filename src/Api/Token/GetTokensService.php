<?php

namespace App\Api\Token;

use App\Dto\Output\TokenDtoOutput;
use App\Entity\Token;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class GetTokensService {

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer
    ){}

    public function getTokens(): array
    {
        $tokens = $this->em->getRepository(Token::class)->findAll();
        $tokensOutput = [];
        foreach($tokens as $token){
            $tokensOutput[] = $this->serializer->normalize(TokenDtoOutput::fromEntity($token));
        }

        return $tokensOutput;
        
    }
}