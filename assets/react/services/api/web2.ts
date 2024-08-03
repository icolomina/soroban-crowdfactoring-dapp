import { 
    getCreateContractCall, 
    getCreateUserContractCall, 
    getEditContractCall, 
    getInitializeContractCall, 
    getRetrieveAvailableContractsCall, 
    getRetrieveContractsCall, 
    getRetrieveTokensCall, 
    getRetrieveUserContractCall, 
    getRetrieveUserContractsCall, 
    getStopContractDepositsCall
} from "../router/router";

export class Web2 {

    getTokens(): Promise<Response> {
        return fetch(getRetrieveTokensCall(), {
            method: "GET",
            mode: "same-origin",
            headers: {
                "Content-Type": "application/json",
            }
        });
    }

    getContracts(): Promise<Response> {
        return fetch(getRetrieveContractsCall(), {
            method: "GET",
            mode: "same-origin",
            headers: {
                "Content-Type": "application/json",
            }
        });
    }

    getAvailableContracts(): Promise<Response> {
        return fetch(getRetrieveAvailableContractsCall(), {
            method: "GET",
            mode: "same-origin",
            headers: {
                "Content-Type": "application/json",
            }
        });
    }

    getUserContracts(): Promise<Response> {
        return fetch(getRetrieveUserContractsCall(), {
            method: "GET",
            mode: "same-origin",
            headers: {
                "Content-Type": "application/json",
            }
        });
    }

    getUserContract(id: number): Promise<Response> {
        return fetch(getRetrieveUserContractCall(id), {
            method: "GET",
            mode: "same-origin",
            headers: {
                "Content-Type": "application/json",
            }
        });
    }

    getContract(id: number): Promise<Response> {
        return fetch(getEditContractCall(id), {
            method: "GET",
            mode: "same-origin",
            headers: {
                "Content-Type": "application/json",
            }
        });
    }

    createContract(token: string, rate: number, claimMonths: number, label: string, description: string|null): Promise<Response> {
        return fetch(getCreateContractCall(), {
            method: "POST",
            mode: "same-origin",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({'token': token, 'rate' : rate, 'claimMonths': claimMonths, 'label' : label, 'description' : description})
        });
    }

    stopContractDeposits(id: number): Promise<Response> {
        return fetch(getStopContractDepositsCall(id), {
            method: "PATCH",
            mode: "same-origin",
            headers: {
                "Content-Type": "application/json",
            }
        });
    }

    createUserContract(contracAddress: string, hash: string, deposited: string): Promise<Response> {
        return fetch(getCreateUserContractCall(), {
            method: "POST",
            mode: "same-origin",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({'contractAddress': contracAddress, 'hash': hash, 'deposited': deposited})
        });
    }

    initializeContract(contractId: number): Promise<Response> {
        
        return fetch(getInitializeContractCall(contractId), {
            method: "PATCH",
            mode: "same-origin",
            headers: {
                "Content-Type": "application/json",
            }
        });
    }
}