import { useEffect, useState } from "react";
import { Web2 } from "../services/api/web2";
import { getEditUserContractPage } from "../services/router/router";

export interface UserContract {
    id: number
    contractIssuer: string,
    contractAddress: string,
    token: string,
    rate: number,
    createdAt: string,
    withdrawalDate: string,
    deposited: string,
    interest: string,
    total: number,
    hash?: string
}

export default function UserContractList() {
 
    const [userContracts, setUserContracts] = useState<UserContract[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [loadingText, setLoadingText] = useState<string>('Loading');

    useEffect(() => {
        const web2 = new Web2();
        setLoading(true);
        web2.getUserContracts().then(
            async (r: Response) => {
                if(r.ok) {
                    const responseUserContracts = await r.json() as UserContract[];
                    setUserContracts(responseUserContracts);
                    setLoading(false);
                }
            }
        )
      }, []
    );

    const editUserContract = (id: number) => {
        return window.location.replace(getEditUserContractPage(id));
    }

    if(loading) {
        return (
            <div className="text-center">
                <div className="spinner-border" role="status">
                    <span className="visually-hidden">{loadingText}</span>
                </div>
                <p>{loadingText}</p>
            </div>
        )
    }
    else{
        if(userContracts.length === 0) {
            return (
                <div className="row">
                    <div className="col-xl-6">
                        <div className="card mb-4">
                            <div className="card-header">
                                <i className="fas fa-chart-area me-1"></i>
                                    Empty Portflio
                            </div>
                            <div className="card-body">
                                <h5>You have not invested yet. Start visiting the Available offers</h5>
                            </div>
                        </div>
                    </div>
                </div>
            );
        }
        else{
            return (
                <div>
                    <div className="card mb-4">
                    <div className="card-header">
                        <i className="fas fa-table me-1"></i>
                        Portfolio offers
                    </div>
                    <div className="card-body">
                        <div className="datatable-wrapper datatable-loading no-footer sortable searchable fixed-columns">
                            <div className="datatable-container" >
                                <table id="datatablesSimple" className="datatable-table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Issuer</th>
                                            <th scope="col">Token</th>
                                            <th scope="col">Rate</th>
                                            <th scope="col">Created At</th>
                                            <th scope="col">Withdrawal date</th>
                                            <th scope="col">Deposited</th>
                                            <th scope="col">Expected interests</th>
                                            <th scope="col">Total to claim</th>
                                            <th scope="col">Options</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {userContracts.map( (uc: UserContract) => (
                                            <tr key={uc.id}>
                                                <td>{ uc.id }</td>
                                                <td>{ uc.contractIssuer }</td>
                                                <td>{ uc.token }</td>
                                                <td>{ uc.rate }</td>
                                                <td>{ uc.createdAt }</td>
                                                <td>{ uc.withdrawalDate }</td>
                                                <td>{ uc.deposited }</td>
                                                <td>{ uc.interest }</td>
                                                <td>{ uc.total }</td>
                                                <td>
                                                    <button className="btn btn-primary btn-sm" onClick={() => editUserContract(uc.id)} role="button" aria-disabled="true">Edit</button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            );
        }
    }


}