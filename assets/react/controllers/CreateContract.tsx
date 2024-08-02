import { useEffect, useState } from "react";
import { Web2 } from "../services/api/web2";
import { getContractListPage } from "../services/router/router";

interface FormData {
    token: string;
    rate: number,
    claimMonths: number,
    label: string,
    description: string|null
}

export default function CreateContract () {
    const [formData, setFormData] = useState<FormData>({ token: 'USDC', rate: 1, claimMonths: 3, label: '', description: ''});
    const [contractCreated, setContractCreated] = useState<boolean>(false);
    const [creatingContract, setCreatingContract] = useState<boolean>(false);

    useEffect(() => {
        if (contractCreated) {
            window.location.replace(getContractListPage());
        }
      }, [contractCreated]);

    const handleChange = (event: any) => {
        const { name, value } = event.target;
        setFormData ( (previousFormData) => ({ ...previousFormData, [name]: value}) )
    }

    const handleForm = async (event: any) => {
        setCreatingContract(true);
        const web2 = new Web2();
        web2.createContract(formData.token, Number(formData.rate), Number(formData.claimMonths), formData.label, formData.description).then(
            (async (response) => {
                if(response.ok) {
                    const r = await response.json();
                    setContractCreated(true);
                }
            })
        )
    }

    if(creatingContract) {
        return (
            <div className="text-center">
                <div className="spinner-border" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
                <p>Creating contract. This may take a few seconds</p>
            </div>
        )
    }
    else{
        return (
            <div>
                <div className="row mb-3">
                    <div className="col-md-12">
                        <div className="form-floating mb-3 mb-md-0">
                            <select className="form-select" aria-label="Default select example" id="token" value={formData.token} onChange={handleChange}>
                                <option value="USDC">Circle - USDC</option>
                            </select>
                            <label htmlFor="token" className="form-label">Token</label>
                        </div>
                    </div>
                </div>
    
                <div className="row mb-3" >
                    <div className="col-md-12">
                        <div className="form-floating mb-3 mb-md-0">
                            <input type="text" name="rate" id="rate" className="form-control" value={formData.rate} onChange={handleChange} />
                            <label htmlFor="rate" className="form-label">Interest Rate</label>
                        </div>
                    </div>
                </div>

                <div className="row mb-3" >
                    <div className="col-md-12">
                        <div className="form-floating mb-3 mb-md-0">
                            <select className="form-select" aria-label="Default select example" name="claimMonths" id="claimMonths" value={formData.claimMonths} onChange={handleChange}>
                                <option value="3">3 Months</option>
                                <option value="6">6 Months</option>
                                <option value="12">12 Months</option>
                            </select>
                            <label htmlFor="claimMonths" className="form-label">Deposit time in months</label>
                        </div>
                    </div>
                </div>

                <div className="row mb-3" >
                    <div className="col-md-12">
                        <div className="form-floating mb-3 mb-md-0">
                            <input type="text" name="label" id="label" className="form-control" value={formData.label} onChange={handleChange} />
                            <label htmlFor="label" className="form-label">Short description</label>
                        </div>
                    </div>
                </div>

                <div className="row mb-3" >
                    <div className="col-md-12">
                        <div className="form-floating mb-3 mb-md-0">
                            <textarea rows={6} cols={60} name="description" id="description" className="form-control" value={formData.description} onChange={handleChange} ></textarea>
                            <label htmlFor="description" className="form-label">Large description</label>
                        </div>
                    </div>
                </div>
    
                <div className="row mb-3">
                    <div className="col-md-12">
                        <button type="button" className="btn btn-primary" onClick={handleForm}>Create contract</button>
                    </div>
                </div>
            </div>
        );
    }
}