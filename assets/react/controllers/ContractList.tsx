import { useState, useEffect } from 'react';
import { Web2 } from '../services/api/web2';
import { getEditContractPage, getMakeDepositToContractPage } from '../services/router/router';

export interface Contract {
    id: number
    initialized: boolean,
    address: string,
    token: string,
    tokenCode: string,
    rate: number,
    createdAt: string,
    issuer: string,
    claimMonths: number,
    label: string
    fundsReached: boolean,
    description?: string
    currentFunds?: string
}


export default function ContractList () {

    const [initialized, setInitialized] = useState<boolean>(false);
    const [contracts, setContracts] = useState<Contract[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [loadingText, setLoadingText] = useState<string>('Loading');

    useEffect(() => {
        if (initialized) {
            window.location.replace(window.location.href);
        }
      }, [initialized]);

    useEffect(() => {
        const web2 = new Web2();
        setLoading(true);
        web2.getContracts().then(
            async (r: Response) => {
                if(r.ok) {
                    const responseContracts = await r.json() as Contract[];
                    setContracts(responseContracts);
                    setLoading(false);
                }
            }
        )
      }, []);

    const initializeContract = (id: number) => {
        const web2 = new Web2();
        setLoadingText('Initializing contract. This may take a few seconds. Please be patient..');
        setLoading(true);
        web2.initializeContract(id).then(
            (async (response) => {
                if(response.ok) {
                    setInitialized(true);
                }
            })
        )
    }

    const editContract = (id: number) => {
        window.location.replace(getEditContractPage(id));
    }

    if(loading) {
        return (
            <div className="text-center">
                <div className="spinner-border" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
                <p>{loadingText}</p>
            </div>
        )
    }
    else{
        if(contracts.length === 0) {
            return (
                <div className="row">
                    <div className="col-xl-6">
                        <div className="card mb-4">
                            <div className="card-header">
                                <i className="fas fa-chart-area me-1"></i>
                                    No Contracts yet
                            </div>
                            <div className="card-body">
                                <h5>There are no contracts yet</h5>
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
                        Contract List
                    </div>
                    <div className="card-body">
                        <div className="datatable-wrapper datatable-loading no-footer sortable searchable fixed-columns">
                            <div className="datatable-container" >
                                <table id="datatablesSimple" className="datatable-table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Initialized</th>
                                            <th scope="col">Funds Reached</th>
                                            <th scope="col">Issuer</th>
                                            <th scope="col">Address</th>
                                            <th scope="col">Token</th>
                                            <th scope="col">Rate</th>
                                            <th scope="col">Months for claim</th>
                                            <th scope="col">Created At</th>
                                            <th scope="col">Options</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {contracts.map( (c: Contract) => (
                                            <tr key={c.id}>
                                                <td>{ c.id }</td>
                                                <td>{ c.initialized ? <i className="bi bi-check-square"></i> : <i className="bi bi-x-square"></i> }</td>
                                                <td>{ c.fundsReached ? <i className="bi bi-check-square"></i> : <i className="bi bi-x-square"></i> }</td>
                                                <td>{ c.issuer }</td>
                                                <td>{ c.address }</td>
                                                <td>{ c.token }</td>
                                                <td>{ c.rate }</td>
                                                <td>{ c.claimMonths }</td>
                                                <td>{ c.createdAt }</td>
                                                <td>
                                                    <button type="button" disabled={c.initialized} className="btn btn-primary btn-sm" role="button" aria-disabled="true" onClick={() => initializeContract(c.id)}>Initialize</button>&nbsp;
                                                    <button type="button" className="btn btn-primary btn-sm" role="button" aria-disabled="true" onClick={() => editContract(c.id)}>Edit</button>
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
