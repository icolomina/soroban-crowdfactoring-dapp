<?php

namespace App\Api\Token;

use App\Api\Token\Domain\CreateTokenEntityService;
use App\Dto\CreateTokenDto;
use App\Entity\Token;
use App\Stellar\AccountManager;
use App\Stellar\Soroban\Contract\DeployManager;
use App\Stellar\Soroban\Contract\InstallManager;
use App\Stellar\Soroban\Contract\InteractManager;
use App\Stellar\Soroban\Contract\WasmManager;
use Doctrine\ORM\EntityManagerInterface;

class CreateTokenService {

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly DeployManager $deployManager,
        private readonly InstallManager $installManager,
        private readonly InteractManager $interactManager,
        private readonly AccountManager $accountManager,
        private readonly WasmManager $wasmManager,
        private readonly CreateTokenEntityService $createTokenEntityService
    ) {}

    public function createToken(CreateTokenDto $createTokenDto)
    {
        $keyPair = $this->accountManager->getSystemKeyPair();
        $account = $this->accountManager->getAccount($keyPair);
        $tokenWasmCode = $this->wasmManager->getTokenCode();
        $wasmId = $this->deployManager->deployContract($tokenWasmCode, $keyPair, $account);
        $contractId = $this->installManager->installContract($wasmId);

        $token = $this->createTokenEntityService->createTokenEntity($createTokenDto, $contractId);
        $this->em->persist($token);
        $this->em->flush();
    }

    public function initializeToken(Token $token): void
    {
        $this->interactManager->initToken($token->getAddress(), $token->getDecimals(), $token->getName(), $token->getCode());
        $token->setEnabled(true);

    }
}