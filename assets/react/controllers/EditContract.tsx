import { useEffect, useState } from "react";
import { Contract } from "./ContractList";
import { Web2 } from "../services/api/web2";
import { getContractListPage } from "../services/router/router";

interface EditContractProps {
    id: number
}

export default function EditContract(props: EditContractProps) {

    const [contract, setContract] = useState<Contract>(null);
    const [loading, setLoading] = useState<boolean>(false);
    const [depositsStopped, setDepositsStopped] = useState<boolean>(false);

    useEffect(() => {
        if(depositsStopped) {
            window.location.replace(getContractListPage());
        }

    }, [depositsStopped])

    useEffect(() => {
        const web2 = new Web2();
        setLoading(true);
        web2.getContract(props.id).then(
            async (r: Response) => {
                if(r.ok) {
                    const responseContract = await r.json() as Contract;
                    setContract(responseContract);
                    setLoading(false);
                }
            }
        )
      }, []);

      const stopContractDeposits = () => {
        const web2 = new Web2();
        web2.stopContractDeposits(contract.id).then(
            (r: Response) => {
                if(r.ok) {
                    setDepositsStopped(true);
                }
            }
        )
      }

      if(loading || !contract) {
        return (
            <div className="text-center">
                <div className="spinner-border" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
                <p>Retrieving contract current funds</p>
            </div>
        )
      }
      else {
        return (
            <div className="col-xl-6" >
                <div className="card mb-4" >
                    <div className="card-header" >
                        Contract details
                </div>
                <div className="card-body">
                    <div className="row">
                        <div className="col-lg-12">
                            <ul>
                                <li><strong>Isuuer</strong>: { contract.issuer }</li>
                                <li><strong>Contract Address</strong>: { contract.address } </li>
                                <li><strong>Token</strong>: { contract.token } </li>
                                <li><strong>Rate</strong>: { contract.rate }</li>
                                <li><strong>Months for claim</strong>: { contract.claimMonths } </li>
                                <li><strong>Current Funds</strong>: { contract.currentFunds } </li>
                                <li><strong>Deposits status</strong>:&nbsp;
                                    {
                                        contract.fundsReached
                                            ? 'This contract has reached expected funds and does not admit more deposits'
                                            : 'This contract is still receiving deposits'
                                    }
                                </li>
                            </ul>
                        </div>
                        <hr className="hr" />
                        <div className="col-lg-12">
                            { contract.description }
                        </div>
                        <hr className="hr" />
                        <div className="col-lg-12">
                            <button type="button" disabled={contract.fundsReached} className="btn btn-warning btn-sm" role="button" aria-disabled="true" onClick={stopContractDeposits}>Stop Receiving Deposits</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        )
    }
}