import { useState, useEffect } from 'react';
import { Web2 } from '../services/api/web2';

export interface Token {
    id: number
    enabled: boolean,
    address: string,
    name: string,
    code: string,
    createdAt: string
}


export default function TokenList () {

    const [enabled, setEnabled] = useState(false);
    const [tokens, setTokens] = useState([]);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        if (enabled) {
            window.location.replace(window.location.origin);
        }
      }, [enabled]);

    useEffect(() => {
        const web2 = new Web2();
        setLoading(true);
        web2.getTokens().then(
            async (r: Response) => {
                if(r.ok) {
                    const responseTokens = await r.json() as Token[];
                    setTokens(responseTokens);
                    setLoading(false);
                }
            }
        )
      }, []);

    if(loading) {
        return (
            <div className="text-center">
                <div className="spinner-border" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
            </div>
        )
    }
    else{
        if(tokens.length === 0) {
            return (
                <div className="row">
                    <div className="col-xl-6">
                        <div className="card mb-4">
                            <div className="card-header">
                                <i className="fas fa-chart-area me-1"></i>
                                    No Tokens yet
                            </div>
                            <div className="card-body">
                                <h5>There are no tokens issued so far. Create a new token using the "Create Token" menu option.</h5>
                            </div>
                        </div>
                    </div>
                </div>
            );
        }
        else{
            return (
                <div className="card mb-4">
                <div className="card-header">
                    <i className="fas fa-table me-1"></i>
                    Tokens list
                </div>
                <div className="card-body">
                    <div className="datatable-wrapper datatable-loading no-footer sortable searchable fixed-columns">
                        <div className="datatable-container" >
                            <table id="datatablesSimple" className="datatable-table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Enabled</th>
                                        <th scope="col">Address</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Code</th>
                                        <th scope="col">Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {tokens.map( ( token: Token) => (
                                        <tr key={token.id}>
                                            <td>{ token.id }</td>
                                            <td>{ token.enabled ? <i className="bi bi-check-square"></i> : <i className="bi bi-x-square"></i> }</td>
                                            <td>{ token.address}</td>
                                            <td>{ token.name}</td>
                                            <td>{ token.code}</td>
                                            <td>{ token.createdAt}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            );
        }
    }
    
}
