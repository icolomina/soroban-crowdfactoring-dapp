<?php

namespace App\Api\Contract;

use App\Api\Contract\Domain\CreateContractEntityService;
use App\Dto\Input\CreateContractDto;
use App\Entity\Contract;
use App\Entity\Token;
use App\Entity\User;
use App\Stellar\AccountManager;
use App\Stellar\Soroban\Contract\DeployManager;
use App\Stellar\Soroban\Contract\InstallManager;
use App\Stellar\Soroban\Contract\WasmManager;
use Doctrine\ORM\EntityManagerInterface;
use Soneso\StellarSDK\Crypto\StrKey;
use Symfony\Component\Security\Core\User\UserInterface;

class CreateContractService 
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CreateContractEntityService $createContractEntityService,
        private readonly DeployManager $deployManager,
        private readonly InstallManager $installManager,
        private readonly WasmManager $wasmManager,
        private readonly AccountManager $accountManager
    ){}

    public function createContract(CreateContractDto $createContractDto, User|UserInterface $user): Contract
    {
        $token    = $this->em->getRepository(Token::class)->findOneByCode($createContractDto->token);
        $contract = $this->createContractEntityService->createContractEntity($createContractDto, $token, $user);

        $keyPair  = $this->accountManager->getSystemKeyPair();
        $account  = $this->accountManager->getAccount($keyPair);
        $wasmCode = $this->wasmManager->getWamsCode();

        $wasmid   = $this->deployManager->deployContract($wasmCode, $keyPair, $account);
        $contractAddress = $this->installManager->installContract($wasmid);

        $contract->setAddress(StrKey::encodeContractIdHex($contractAddress));
        $this->em->persist($contract);
        $this->em->flush();

        return $contract;

    }
}