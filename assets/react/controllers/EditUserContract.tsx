import { useEffect, useState } from "react";
import { UserContract } from "./UserContractList";
import { Web2 } from "../services/api/web2";
import { useWallet } from "../services/wallet/wallet";
import { FREIGHTER_ID, ISupportedWallet, WalletNetwork, XBULL_ID } from "@creit.tech/stellar-wallets-kit";
import { ScContract } from "../services/api/contract";
import { Api } from '@stellar/stellar-sdk/lib/rpc';
import { SorobanRpc } from "@stellar/stellar-sdk";
import { getUserContractsListPage } from "../services/router/router";

interface EditUserContractProps {
    id: number,
    url: string
}

export default function EditUserContract(props: EditUserContractProps) {

    const [userContract, setUserContract] = useState<UserContract>(null);
    const [loading, setLoading] = useState<boolean>(false);
    let [wallet, walletSelected] = useWallet([XBULL_ID, FREIGHTER_ID], FREIGHTER_ID, WalletNetwork.TESTNET);

    useEffect(() => {
        const web2 = new Web2();
        setLoading(true);
        web2.getUserContract(props.id).then(
            async (r: Response) => {
                if(r.ok) {
                    const responseUserContract = await r.json() as UserContract;
                    setUserContract(responseUserContract);
                    setLoading(false);
                }
            }
        )
      }, []
    );

    const withdrawFunds = () => {
        if(!walletSelected) {
            wallet.openModal({
                onWalletSelected: async (option: ISupportedWallet) => {
                    wallet.setWallet(option.id);
                    await wallet.getPublicKey();
                    walletSelected = true;
                }
            });

            const contract = new ScContract();
            contract.withdrawFunds(wallet, userContract.contractAddress)
                .then(
                    async (trxResponse: Api.SendTransactionResponse) => {
                        const transactionStatus = await contract.queryTransaction(trxResponse);
                        if(transactionStatus == SorobanRpc.Api.GetTransactionStatus.SUCCESS) {
                            window.location.replace(getUserContractsListPage());
                        }
                    }
                )
                .catch( error => console.log(error)
            )
        }
    }

    const canWithdraw = () => {
        const today = new Date();
        const withdrawDate = new Date(userContract.withdrawalDate);

        return today >= withdrawDate;
    }

    if(loading || !userContract) {
        return (
            <div className="text-center">
                <div className="spinner-border" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
                <p>Retrieving deposit information</p>
            </div>
        )
    }
    else {
        return (
            <div className="col-xl-6" >
                <div className="card mb-4" >
                    <div className="card-header" >
                        Deposit details
                </div>
                <div className="card-body">
                    <div className="row">
                        <div className="col-lg-12">
                            <ul>
                                <li><strong>Isuuer</strong>: { userContract.contractIssuer }</li>
                                <li><strong>Token</strong>: { userContract.token } </li>
                                <li><strong>Rate</strong>: { userContract.rate }</li>
                                <li><strong>Withdrawal Date</strong>: { userContract.withdrawalDate } </li>
                                <li><strong>Deposited</strong>: { userContract.deposited }</li>
                                <li><strong>Interest</strong>: { userContract.interest } </li>
                                <li><strong>Total</strong>: { userContract.total }</li>
                            </ul>
                        </div>
                        <hr className="hr" />
                        <div className="col-lg-12">
                            <button type="button" disabled={canWithdraw() ? false : true} className="btn btn-warning btn-sm" role="button" aria-disabled="true" onClick={withdrawFunds}>Withdraw funds</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        )
    }
}