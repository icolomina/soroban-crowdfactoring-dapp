<?php

namespace App\Stellar\Soroban\Contract;

use App\Entity\Contract;
use App\Stellar\AccountManager;
use App\Stellar\Networks;
use App\Stellar\Soroban\ServerManager;
use App\Stellar\Soroban\Transaction\SorobanTransactionManager;
use App\Utils\Token;
use Doctrine\ORM\EntityManagerInterface;
use Soneso\StellarSDK\Crypto\KeyPair;
use Soneso\StellarSDK\InvokeContractHostFunction;
use Soneso\StellarSDK\InvokeHostFunctionOperationBuilder;
use Soneso\StellarSDK\Responses\Account\AccountResponse;
use Soneso\StellarSDK\Soroban\Address;
use Soneso\StellarSDK\Soroban\Responses\GetTransactionResponse;
use Soneso\StellarSDK\TransactionBuilder;
use Soneso\StellarSDK\Xdr\XdrInt128Parts;
use Soneso\StellarSDK\Xdr\XdrSCVal;

class InteractManager {

    public function __construct(
        private readonly AccountManager $accountManager,
        private readonly ServerManager $serverManager,
        private readonly SorobanTransactionManager $sorobanTransactionManager,
        private readonly EntityManagerInterface $em,
        private readonly Token $token
    ){}

    public function initToken(string $tokenAddress, int $decimal, string $name, string $symbol): void
    {
        $keyPairSubmiter = $this->accountManager->getSystemKeyPair();
        $accountSubmiter = $this->accountManager->getAccount($keyPairSubmiter);

        $invokeContractHostFunction = new InvokeContractHostFunction($tokenAddress, "initialize", [
            Address::fromAccountId($accountSubmiter->getAccountId())->toXdrSCVal(),
            XdrSCVal::forU32($decimal),
            XdrSCVal::forString($name),
            XdrSCVal::forString($symbol)
        ]);

        $transactionResponse = $this->processTransaction($invokeContractHostFunction, $accountSubmiter, $keyPairSubmiter);
        $resultValue = $transactionResponse->getResultValue();
        if($resultValue->getError()) {
            throw new \RuntimeException('Token initialize call execution failed: ' . $resultValue->getError()->getCode()->getValue());
        }
    }

    public function mintUserWithToken(string $tokenAddress, string $userAddress, int $amount): void
    {
        $keyPairSubmiter = $this->accountManager->getSystemKeyPair();
        $accountSubmiter = $this->accountManager->getAccount($keyPairSubmiter);

        $invokeContractHostFunction = new InvokeContractHostFunction($tokenAddress, "mint", [
            Address::fromAccountId($userAddress)->toXdrSCVal(),
            XdrSCVal::forI128(new XdrInt128Parts($amount, $amount))
        ]);

        $transactionResponse = $this->processTransaction($invokeContractHostFunction, $accountSubmiter, $keyPairSubmiter);
        $resultValue = $transactionResponse->getResultValue();
        if($resultValue->getError()) {
            throw new \RuntimeException('Token mint call execution failed: ' . $resultValue->getError()->getCode()->getValue());
        }
    }

    public function initContract(Contract $contract): void
    {
        $keyPairSubmiter = $this->accountManager->getSystemKeyPair();
        $accountSubmiter = $this->accountManager->getAccount($keyPairSubmiter);

        $claimMonts    = $contract->getClaimMonths();
        $nowDt         = new \DateTimeImmutable();
        $monthsAfterDt = $nowDt->add(\DateInterval::createFromDateString("+ {$claimMonts} months"));
        $days          = $monthsAfterDt->diff($nowDt)->days;
        $rate          = $contract->getRate() * 100;

        $invokeContractHostFunction = new InvokeContractHostFunction($contract->getAddress(), "init", [
            Address::fromAccountId($accountSubmiter->getAccountId())->toXdrSCVal(),
            Address::fromContractId($contract->getToken()->getAddress())->toXdrSCVal(),
            XdrSCVal::forU32((int)$rate),
            XdrSCVal::forU64($days)
        ]);

        $transactionResponse = $this->processTransaction($invokeContractHostFunction, $accountSubmiter, $keyPairSubmiter);
        $resultValue = $transactionResponse->getResultValue();
        if($resultValue->getError()) {
            throw new \RuntimeException('Contract cannot been initialized: ' . $resultValue->getError()->getCode()->getValue());
        }
    }

    public function adminDepositToContract(Contract $contract, int $amount): void
    {
        $keyPairSubmiter = $this->accountManager->getSystemKeyPair();
        $accountSubmiter = $this->accountManager->getAccount($keyPairSubmiter);

        $invokeContractHostFunction = new InvokeContractHostFunction($contract->getAddress(), "admin_deposit", [
            XdrSCVal::forU32($amount)
        ]);

        $transactionResponse = $this->processTransaction($invokeContractHostFunction, $accountSubmiter, $keyPairSubmiter);
        $resultValue = $transactionResponse->getResultValue();
        if($resultValue->getError()) {
            throw new \RuntimeException('Contract cannot been initialized: ' . $resultValue->getError()->getCode()->getValue());
        }
    }

    public function adminWithdrawalFromContract(Contract $contract, int $amount): void
    {
        $keyPairSubmiter = $this->accountManager->getSystemKeyPair();
        $accountSubmiter = $this->accountManager->getAccount($keyPairSubmiter);

        $invokeContractHostFunction = new InvokeContractHostFunction($contract->getAddress(), "admin_withdrawal", [
            XdrSCVal::forU32($amount)
        ]);

        $transactionResponse = $this->processTransaction($invokeContractHostFunction, $accountSubmiter, $keyPairSubmiter);
        $resultValue = $transactionResponse->getResultValue();
        if($resultValue->getError()) {
            throw new \RuntimeException('Contract cannot been initialized: ' . $resultValue->getError()->getCode()->getValue());
        }
    }

    public function getContractBalance(Contract $contract): mixed
    {
        $keyPairSubmiter = $this->accountManager->getSystemKeyPair();
        $accountSubmiter = $this->accountManager->getAccount($keyPairSubmiter);

        $invokeContractHostFunction = new InvokeContractHostFunction($contract->getAddress(), "get_contract_balance");
        $transactionResponse = $this->processTransaction($invokeContractHostFunction, $accountSubmiter, $keyPairSubmiter);
        $resultValue = $transactionResponse->getResultValue();
        if($resultValue->getError()) {
            throw new \RuntimeException('Contract cannot been initialized: ' . $resultValue->getError()->getCode()->getValue());
        }


        $result = $resultValue->getI128()->getLo();
        if($result === 0) {
            return '0';
        }
        
        $unsacaledBalance = $resultValue->getI128()->getLo() / pow(10, 6);
        return $this->token->normalizeTokenValue((string)$unsacaledBalance . '00', $contract->getToken()->getDecimals());
    }

    public function stopDeposits(Contract $contract): void
    {
        $keyPairSubmiter = $this->accountManager->getSystemKeyPair();
        $accountSubmiter = $this->accountManager->getAccount($keyPairSubmiter);

        $invokeContractHostFunction = new InvokeContractHostFunction($contract->getAddress(), "stop_deposits");
        $transactionResponse = $this->processTransaction($invokeContractHostFunction, $accountSubmiter, $keyPairSubmiter);
        $resultValue = $transactionResponse->getResultValue();
        if($resultValue->getError()) {
            throw new \RuntimeException('Unable to stop contract deposits: ' . $resultValue->getError()->getCode()->getValue());
        }
    }

    private function processTransaction(InvokeContractHostFunction $invokeContractHostFunction, AccountResponse $account, KeyPair $keyPair): GetTransactionResponse
    {
        $builder = new InvokeHostFunctionOperationBuilder($invokeContractHostFunction);
        $operation = $builder->build();
        $transaction = (new TransactionBuilder($account))->addOperation($operation)->build();

        $server = $this->serverManager->getServer(Networks::TESTNET);
        $this->sorobanTransactionManager->simulate($server, $transaction, $keyPair, true);

        $sendResponse = $server->sendTransaction($transaction);
        return $this->sorobanTransactionManager->waitForTransaction($server, $sendResponse);
    }

}