<?php

namespace App\Command;

use App\Entity\Contract;
use App\Entity\Token;
use App\Entity\User;
use App\Stellar\AccountManager;
use App\Stellar\Soroban\Contract\DeployManager;
use App\Stellar\Soroban\Contract\InstallManager;
use App\Stellar\Soroban\Contract\InteractManager;
use App\Stellar\Soroban\Contract\WasmManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name : 'app:setup'
)]
class SetupCommand extends Command
{
    public function __construct(
        private readonly AccountManager $accountManager,
        private readonly WasmManager $wasmManager,
        private readonly DeployManager $deployManager,
        private readonly InstallManager $installManager,
        private readonly InteractManager $interactManager,
        private readonly EntityManagerInterface $em
    ){
        parent::__construct();
    }

    public function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Deploying and installing token contract ....');
        $keyPair = $this->accountManager->getSystemKeyPair();
        $account = $this->accountManager->getAccount($keyPair);
        /*$tokenWasmCode = $this->wasmManager->getTokenCode();
        $wasmTokenId = $this->deployManager->deployContract($tokenWasmCode, $keyPair, $account);
        $output->writeln('Token contract deployed - Wasm id: ' . $wasmTokenId);

        $tokenContractId = $this->installManager->installContract($wasmTokenId);
        $output->writeln('Token contract installed - Contract id: ' . $tokenContractId );

        $output->writeln('Initializing token contract ....');
        $code =  mb_strtoupper(substr(str_shuffle(uniqid($tokenContractId)), 0, 4));
        $this->interactManager->initToken($tokenContractId, 4, 'MyToken', $code);

        $token = new Token();
        $token->setAddress($tokenContractId);
        $token->setEnabled(true);
        $token->setCode($code);
        $token->setCreatedAt(new \DateTimeImmutable());
        $token->setName('MyToken');

        $this->em->persist($token);
        $this->em->flush();*/

        $token = $this->em->getRepository(Token::class)->findOneByCode('USDC');
        $issuer = $this->em->getRepository(User::class)->findOneByEmail('valkara@gmail.com');

        $output->writeln('Creating Paid Account Contract');
        $contractWasmCode = $this->wasmManager->getWamsCode();
        $wasmContractId = $this->deployManager->deployContract($contractWasmCode, $keyPair, $account);
        $output->writeln('Paid account contract deployed - Wasm id: ' . $wasmContractId);

        $paidAccountContractId = $this->installManager->installContract($wasmContractId);
        $output->writeln('Paid account contract installed - Contract id: ' . $paidAccountContractId );

        $contract = new Contract();
        $contract->setAddress($paidAccountContractId);
        $contract->setToken($token);
        $contract->setRate(4);
        $contract->setCreatedAt(new \DateTimeImmutable());
        $contract->setInitialized(false);
        $contract->setIssuer($issuer);
        $contract->setClaimMonths(6);

        $this->em->persist($contract);
        $this->em->flush();


        $output->writeln('Initializing Paid Account Contract ....');
        $this->interactManager->initContract($contract);

        $contract->setInitialized(true);
        $this->em->persist($contract);
        $this->em->flush();

        return Command::SUCCESS;
    }
}