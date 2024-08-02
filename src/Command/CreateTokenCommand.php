<?php

namespace App\Command;

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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name : 'app:create-token'
)]
class CreateTokenCommand extends Command
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
        $this
            ->addOption('code', null, InputOption::VALUE_REQUIRED, 'Token code')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Token name')
            ->addOption('decimals', null, InputOption::VALUE_REQUIRED, 'Token decimals')
            ->addOption('issuer', null, InputOption::VALUE_REQUIRED, 'Issuer user email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Deploying and installing token contract ....');
        $keyPair = $this->accountManager->getSystemKeyPair();
        $account = $this->accountManager->getAccount($keyPair);
        $tokenWasmCode = $this->wasmManager->getTokenCode();
        $wasmTokenId = $this->deployManager->deployContract($tokenWasmCode, $keyPair, $account);
        $output->writeln('Token contract deployed - Wasm id: ' . $wasmTokenId);

        $tokenContractId = $this->installManager->installContract($wasmTokenId);
        $output->writeln('Token contract installed - Contract id: ' . $tokenContractId );

        $output->writeln('Initializing token contract ....');
        $this->interactManager->initToken($tokenContractId, (int)$input->getOption('decimals'), $input->getOption('name'), $input->getOption('code'));

        $token = new Token();
        $token->setAddress($tokenContractId);
        $token->setEnabled(true);
        $token->setCode($input->getOption('code'));
        $token->setCreatedAt(new \DateTimeImmutable());
        $token->setName($input->getOption('name'));
        $token->setDecimals((int)$input->getOption('decimals'));

        $issuer = $this->em->getRepository(User::class)->findOneByEmail($input->getOption('issuer'));
        $token->setIssuer($issuer);

        $this->em->persist($token);
        $this->em->flush();

        return Command::SUCCESS;
    }
}