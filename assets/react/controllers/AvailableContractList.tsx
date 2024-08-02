import { useEffect, useState } from "react";
import { Web2 } from "../services/api/web2";
import { Contract } from "./ContractList";
import { getMakeDepositToContractPage } from "../services/router/router";

export default function AvailableContractList () {

    const [availableContracts, setAvailableContracts] = useState<Contract[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [loadingText, setLoadingText] = useState<string>('Loading');

    useEffect(() => {
        const web2 = new Web2();
        setLoading(true);
        web2.getAvailableContracts().then(
            async (r: Response) => {
                if(r.ok) {
                    const responseContracts = await r.json() as Contract[];
                    setAvailableContracts(responseContracts);
                    setLoading(false);
                }
            }
        )
      }, []);

    const wantToInvest = (id: number) => {
        window.location.replace(getMakeDepositToContractPage(id));
    };

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
        if(availableContracts.length === 0) {
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
                <div className="row">
                { availableContracts.map( (c: Contract) => (
                    <div key={c.id} className="col-xl-4">
                        <div className="card mb-4">
                            <div className="card-header">
                            <svg className="svg-inline--fa fa-chart-area me-1" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chart-area" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M64 64c0-17.7-14.3-32-32-32S0 46.3 0 64V400c0 44.2 35.8 80 80 80H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H80c-8.8 0-16-7.2-16-16V64zm96 288H448c17.7 0 32-14.3 32-32V251.8c0-7.6-2.7-15-7.7-20.8l-65.8-76.8c-12.1-14.2-33.7-15-46.9-1.8l-21 21c-10 10-26.4 9.2-35.4-1.6l-39.2-47c-12.6-15.1-35.7-15.4-48.7-.6L135.9 215c-5.1 5.8-7.9 13.3-7.9 21.1v84c0 17.7 14.3 32 32 32z"></path></svg>
                            {c.issuer}
                            <button className="btn btn-success btn-sm float-end" onClick={() => wantToInvest(c.id)}>I want to invest</button>
                            </div>
                            <div className="card-body">
                            <div className="row">
                                <div className="col-lg-">
                                <figure>
                                    <blockquote className="blockquote">
                                        <p>{c.label}</p>
                                    </blockquote>
                                    <figcaption className="blockquote-footer">
                                        {c.description}
                                    </figcaption>
                                </figure>
                                </div>
                            </div>
                            <hr className="hr" />
                            <div className="row">
                                <div className="col-lg-4"><span className="badge text-bg-info">Rate: {c.rate}%</span></div>
                                <div className="col-lg-4"><span className="badge text-bg-info">Months: {c.claimMonths}</span></div>
                                <div className="col-lg-4"><span className="badge text-bg-info">Token: {c.tokenCode}</span></div>
                            </div>
                            </div>
                        </div>
                    </div>
                ))}
                </div>
            );
        }
    }
}