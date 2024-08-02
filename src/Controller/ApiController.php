<?php

namespace App\Controller;

use App\Api\Contract\CreateContractService;
use App\Api\Contract\CreateUserContractService;
use App\Api\Contract\EditContractService;
use App\Api\Contract\GetUserContractService;
use App\Api\Contract\InitializeContractService;
use App\Api\Contract\StopContractDepositsService;
use App\Api\Token\GetTokensService;
use App\Contract\ContractManager;
use App\Dto\Input\CreateContractDto;
use App\Dto\Input\CreateUserContractDtoInput;
use App\Entity\Contract;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class ApiController extends AbstractController
{
    
    #[Route('/contracts', name: 'api_get_contracts', methods: ['GET'])]
    #[IsGranted(User::ROLE_FINANCIAL_ENTITY)]
    public function getContractsData(ContractManager $contractManager): JsonResponse
    {
        $user = $this->getUser();
        return new JsonResponse($contractManager->getContracts($user));
    }

    #[Route('/available-contracts', name: 'api_get_available_contracts', methods: ['GET'])]
    #[IsGranted(User::ROLE_SAVER)]
    public function getAvailableContractsData(ContractManager $contractManager): JsonResponse
    {
        $user = $this->getUser();
        return new JsonResponse($contractManager->getAvailableContracts($user));
    }
    
    #[Route('/contract', name: 'api_post_contract', methods: ['POST'])]
    #[IsGranted(User::ROLE_FINANCIAL_ENTITY)]
    public function postCreateContract(#[MapRequestPayload] CreateContractDto $createContractDto, CreateContractService $createContractService): JsonResponse
    {    
        $contract = $createContractService->createContract($createContractDto, $this->getUser());
        return new JsonResponse(['id' => $contract->getId()]);
    }

    #[Route('/contract/{id}', name: 'api_get_contract', methods: ['GET'])]
    #[IsGranted(User::ROLE_FINANCIAL_ENTITY)]
    public function getContract(Contract $contract, EditContractService $editContractService, SerializerInterface $serializer): JsonResponse
    {    
        $contractOutput = $editContractService->editContract($contract);
        return new JsonResponse($serializer->serialize($contractOutput, 'json'), 200, [], true);
    }

    #[Route('/contract/{id}/initialize', name: 'api_patch_contract_inicialize', methods: ['PATCH'])]
    #[IsGranted(User::ROLE_FINANCIAL_ENTITY)]
    public function initializeContract(Contract $contract, InitializeContractService $initializeContractService): JsonResponse
    {
        $initializeContractService->initializeContract($contract);
        return new JsonResponse(null, 204);
    }

    #[Route('/user-contracts', name: 'api_get_user_contracts', methods: ['GET'])]
    #[IsGranted(User::ROLE_SAVER)]
    public function getUserContracts(GetUserContractService $getUserContractService): JsonResponse
    {
        $user = $this->getUser();
        return new JsonResponse($getUserContractService->getUserContracts($user));
    }

    #[Route('/tokens', name: 'api_get_tokens', methods: ['GET'])]
    public function getTokens(GetTokensService $getTokensService): JsonResponse
    {
        $tokensOutput = $getTokensService->getTokens();
        return new JsonResponse($tokensOutput);
    }

    #[Route('/user-contract', name: 'api_post_user_contract', methods: ['POST'])]
    #[IsGranted(User::ROLE_SAVER)]
    public function postCreateUserContract(#[MapRequestPayload] CreateUserContractDtoInput $createUserContractDtoInput, CreateUserContractService $createUserContractService): JsonResponse
    {
        $user = $this->getUser();
        $createUserContractService->createUserContract($createUserContractDtoInput, $user);

        return new JsonResponse(null, 204);
    }

    #[Route('/contract/{id}/stop-deposits', name: 'api_patch_contract_stop_deposits', methods: ['PATCH'])]
    #[IsGranted(User::ROLE_FINANCIAL_ENTITY)]
    public function patchStopContractDeposits(Contract $contract, StopContractDepositsService $stopContractDepositsService): JsonResponse
    {
        $stopContractDepositsService->stopContractDeposits($contract);
        return new JsonResponse(null, 204);
    }

    
}