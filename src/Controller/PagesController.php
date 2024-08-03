<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Entity\User;
use App\Stellar\Networks;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/pages')]
class PagesController extends AbstractController
{
    #[Route('/contracts', name: 'get_contracts_page', methods: ['GET'])]
    public function getContractsPage(): Response
    {
        return $this->render('contract/contracts_list.html.twig');
    }

    #[Route('/available-contracts', name: 'get_available_contracts_page', methods: ['GET'])]
    public function getAvailableContractsPage(): Response
    {
        return $this->render('contract/contracts_available_list.html.twig');
    }

    #[Route('/new-contract', name: 'get_new_contract_page', methods: ['GET'])]
    #[IsGranted(User::ROLE_FINANCIAL_ENTITY)]
    public function getCreateContractPage(): Response
    {
        return $this->render('contract/create_contract.html.twig');
    }

    #[Route('/edit-contract/{id}', name: 'get_edit_contract_page', methods: ['GET'])]
    #[IsGranted(User::ROLE_FINANCIAL_ENTITY)]
    public function getEditContractPage(int $id): Response
    {
        return $this->render('contract/contract_edit.html.twig', ['id' => $id]);
    }

    #[Route('/edit-user-contract/{id}', name: 'get_edit_use_contract_page', methods: ['GET'])]
    #[IsGranted(User::ROLE_SAVER)]
    public function getEditUserContractPage(int $id): Response
    {
        return $this->render('contract/user_contract_edit.html.twig', ['id' => $id]);
    }

    #[Route('/contract/{id}/new-deposit', name: 'get_contract_new_deposit_page', methods: ['GET'])]
    public function getCreateContractDepositPage(Contract $contract): Response
    {
        return $this->render('contract/create_contract_deposit.html.twig', [ 'contractAddress' => $contract->getAddress(), 'tokenDecimals' => $contract->getToken()->getDecimals(), 'url' => Networks::TESTNET->value ]);
    }

    #[Route('/user-contracts', name: 'get_user_contracts_page', methods: ['GET'])]
    public function getUserContractPages(): Response
    {
        return $this->render('contract/user_contracts_list.html.twig');
    }

    #[Route('/tokens', name: 'get_tokens_page', methods: ['GET'])]
    public function getTokensHtmlPage(): Response
    {
        return $this->render('token/token_list.html.twig');
    }
}