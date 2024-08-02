import { useState, useEffect } from 'react';
import { ScContract } from '../services/api/contract';
import { FREIGHTER_ID, ISupportedWallet, WalletNetwork, XBULL_ID } from '@creit.tech/stellar-wallets-kit';
import { useWallet } from '../services/wallet/wallet';
import { Api } from '@stellar/stellar-sdk/lib/rpc';
import { Web2 } from '../services/api/web2';
import { TokenParser } from '../services/soroban/token';
import { SorobanRpc } from '@stellar/stellar-sdk';
import { getUserContractsListPage } from '../services/router/router';

interface FormData {
    amount: string;
}

interface DepositFormProps {
    url: string,
    contractAddress: string
    tokenDecimals: number
}


export default function DepositForm (props: DepositFormProps) {

    const [formData, setFormData] = useState<FormData>({ amount: '0' });
    const [loading, setLoading] = useState<boolean>(false);
    const [depositSuccess, setDepositSuccess] = useState<boolean>(false);
    const [wallet, walletSelected]: any[] = useWallet([XBULL_ID, FREIGHTER_ID], FREIGHTER_ID, WalletNetwork.TESTNET);
    const contract = new ScContract();

    useEffect(() => {
        if (depositSuccess) {
            window.location.replace(getUserContractsListPage());
        }
      }, [depositSuccess]);

    useEffect(() => {
        if (!walletSelected) {
            wallet.openModal({
                onWalletSelected: async (option: ISupportedWallet) => {
                    wallet.setWallet(option.id);
                    await wallet.getPublicKey();
                }
            });
        }
    }, [depositSuccess]);


    const handleChange = (event: any) => {
        const { name, value } = event.target;
        setFormData ( (previousFormData) => ({ ...previousFormData, [name]: value}) )
    }

    const handleForm = async (event: any) => {
        contract.init(props.url);
        
        const tokenParser = new TokenParser();
        formData.amount = tokenParser.parseAmount(formData.amount, props.tokenDecimals).toString();
        console.log(formData, props);
        contract.sendDeposit(wallet, props.contractAddress, formData.amount)
            .then(
                async (trxResponse: Api.SendTransactionResponse) => {
                    const transactionStatus = await contract.queryTransaction(trxResponse);
                    if(transactionStatus == SorobanRpc.Api.GetTransactionStatus.SUCCESS) {
                        const web2 = new Web2();
                        web2.createUserContract(props.contractAddress, trxResponse.hash, formData.amount).then(
                            () => {
                                setDepositSuccess(true);
                            }
                        )
                    }
                }
            )
            .catch( error => console.log(error))
        
    }

    if(loading) {
        return (
            <div className="text-center">
                <div className="spinner-border" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
                <p>Please wait ...</p>
            </div>
        )
    }
    else{
        return (
            <div>
                <div className="row mb-3">
                    <div className="col-md-12">
                        <div className="form-floating mb-3 mb-md-0">
                            <input type="text" name="amount" id="amount" className="form-control" value={formData.amount} onChange={handleChange} />
                            <label htmlFor="amount" className="form-label">Amount</label>
                        </div>
                    </div>
                </div>
                <div className="row mb-3">
                    <div className="col-md-12">
                        <button type="button" className="btn btn-primary" onClick={handleForm}>Send deposit</button>
                    </div>
                </div>
            </div>
        );
    }
    
}
