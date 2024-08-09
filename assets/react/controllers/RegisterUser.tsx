import { useEffect, useState } from "react";
import { Web2 } from "../services/api/web2";
import { getLoginPage } from "../services/router/router";

interface FormData {
    email: string,
    name: string,
    password: string,
    userType: string
}

interface FormError {
    label: string,
    msg: string
}



export default function RegisterUser() {

    const [formData, setFormData] = useState<FormData>({ email: '', name: '', password: '', userType: 'ROLE_SAVER'});
    const [loading, setLoading] = useState<boolean>(false);
    const [userRegistered, setUserRegistered] = useState<boolean>(false);
    const [formErrors, setFormErrors] = useState<FormError[]>([]);

    useEffect(() => {
        if (userRegistered) {
            window.location.replace(getLoginPage());
        }
      }, [userRegistered]);

    const handleChange = (event: any) => {
        const { name, value } = event.target;
        setFormData ( (previousFormData) => ({ ...previousFormData, [name]: value}) )
    }

    const handleRegister = (event: any) => {
        const web2 = new Web2();
        setLoading(true);

        web2.registerUser(formData.email, formData.name, formData.password, formData.userType).then(
            (async (response) => {
                if(response.ok) {
                    setUserRegistered(true);
                }
                else{
                    const r = await response.json();
                    setFormErrors(r);
                    setLoading(false);
                    setUserRegistered(false);
                }
            })
        )
    }

    const goBackToLogin = () => {
        window.location.replace(getLoginPage());
    }

    if(loading) {
        return (
            <div className="text-center">
                <div className="spinner-border" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
                <p>Registering user. Please wait ...</p>
            </div>
        )
    }
    else {
        return (
            <div className="container">
                <div className="row justify-content-center">

                    <div className="col-lg-5">
                        <div className="card shadow-lg border-0 rounded-lg mt-5" >
                            <div className="card-header"><h3 className="text-center font-weight-light my-4">Register</h3></div>
                            <div className="card-body">

                            <div className="row mb-3" style={{ "display" : formErrors.length > 0 ? "block" : "hidden", color: "red"}}>
                                <div className="col-lg-12">
                                    <ul>
                                        { formErrors.map( (e: FormError) => (
                                            <li key={e.label}><small><strong>{e.label}</strong>: {e.msg}</small></li>
                                        ))}
                                    </ul>
                                </div>
                            </div>

                                <div className="form-floating mb-3">
                                    <input className="form-control" id="email" name="email" type="email" placeholder="write your email" value={formData.email} onChange={handleChange} />
                                    <label htmlFor="email">Email address</label>
                                </div>
                                <div className="form-floating mb-3">
                                    <input className="form-control" id="name" name="name" type="name" placeholder="write your name" value={formData.name} onChange={handleChange} />
                                    <label htmlFor="name">Name</label>
                                </div>
                                <div className="form-floating mb-3">
                                    <input className="form-control" id="password" type="password" name="password" placeholder="Write a password" value={formData.password} onChange={handleChange} />
                                    <label htmlFor="password">Password</label>
                                </div>
                                <div className="form-floating mb-3">
                                    <select className="form-select" aria-label="Default select example" id="userType" name="userType" value={formData.userType} onChange={handleChange}>
                                        <option value="ROLE_SAVER">Investor</option>
                                        <option value="ROLE_FINANCIAL_ENTITY">Company</option>
                                    </select>
                                    <label htmlFor="userType">Choose a user type</label>
                                </div>
                                <div className="d-flex align-items-center justify-content-between mt-4 mb-0">
                                    <button type="button" className="btn btn-primary" onClick={handleRegister}>Register</button>
                                    <button type="button" className="btn btn-info" onClick={goBackToLogin}>Go back to login</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}