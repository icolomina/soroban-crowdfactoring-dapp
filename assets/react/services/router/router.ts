
export function getRetrieveContractsCall(): string {
    return window.location.origin + '/api/contracts';
}

export function getRetrieveAvailableContractsCall(): string {
    return window.location.origin + '/api/available-contracts';
}

export function getRetrieveUserContractsCall(): string {
    return window.location.origin + '/api/user-contracts';
}

export function getRetrieveUserContractCall(id: number): string {
    return window.location.origin + '/api/user-contract/' + id;
}

export function getMarkUserContractAsWithdrawnCall(id: number): string {
    return window.location.origin + '/api/user-contract/' + id + '/mark-as-withdrawn';
}

export function getCreateContractCall(): string {
    return window.location.origin + '/api/contract';
}

export function getEditContractCall(id: number): string {
    return window.location.origin + '/api/contract/' + id;
}

export function getCreateUserContractCall(): string {
    return window.location.origin + '/api/user-contract';
}

export function getInitializeContractCall(id: number): string {
    return window.location.origin + '/api/contract/' + id + '/initialize';
}

export function getRetrieveTokensCall(): string {
    return window.location.origin + '/api/tokens';
}

export function getCreateDepositOnContractTrxCall(id: number): string {
    return window.location.origin + '/api/contract/' + id + '/deposit-trx';
}

export function getStopContractDepositsCall(id: number): string {
    return window.location.origin + '/api/contract/' + id + '/stop-deposits';
}

export function getContractListPage(): string {
    return window.location.origin + '/pages/contracts';
}

export function getUserContractsListPage(): string {
    return window.location.origin + '/pages/user-contracts';
}

export function getMakeDepositToContractPage(id: number): string {
    return window.location.origin + '/pages/contract/' + id + '/new-deposit';
}

export function getEditContractPage(id: number): string {
    return window.location.origin + '/pages/edit-contract/' + id;
}

export function getEditUserContractPage(id: number): string {
    return window.location.origin + '/pages/edit-user-contract/' + id;
}