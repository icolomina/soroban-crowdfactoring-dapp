import { Address, hash, Keypair, nativeToScVal, SorobanRpc, StrKey, xdr } from "@stellar/stellar-sdk";

export class SorobanAuth {

    constructor(
        private auth: xdr.SorobanAuthorizationEntry[]
    ) {}

    async signEntries(address: string, contractId: string, server: SorobanRpc.Server, wallet: any): Promise<xdr.SorobanAuthorizationEntry[]>
    {
        if(this.auth.length === 0) {
            return this.auth;
        }

        const signedAuthEntries = [];
        for(const entry of this.auth) {
            if (entry.credentials().switch() !== xdr.SorobanCredentialsType.sorobanCredentialsAddress()) {
                signedAuthEntries.push(entry);
            } 
            else {
                const entryAddress = entry.credentials().address().address().accountId();
                if(address === StrKey.encodeEd25519PublicKey(entryAddress.ed25519())) {
                    let expirationLedgerSeq = 0;
                    const key = this.buildLedgerKey(contractId);

                    const entryRes = await server.getLedgerEntries(key);
                    if(!entryRes.entries || entryRes.entries.length === 0) {
                        throw new Error('CANNOT_FETCH_LEDGER_ENTRY');
                    }
                    
                    expirationLedgerSeq = entryRes.entries[0].liveUntilLedgerSeq || 0;
                    const addrAuth = entry.credentials().address();
                    addrAuth.signatureExpirationLedger(expirationLedgerSeq);

                    const networkId = hash(Buffer.from('Test SDF Network ; September 2015'));
                    const preimage  = this.buildPreImage(networkId, addrAuth.nonce(), entry.rootInvocation(), expirationLedgerSeq);
                    const signature = await wallet.signAuthEntry(preimage.toXDR().toString('base64'));
                    const publicKey = Address.fromScAddress(addrAuth.address()).toString();

                    if (!Keypair.fromPublicKey(publicKey).verify(hash(preimage.toXDR()), Buffer.from(signature))) {
                        throw new Error(`signature doesn't match payload`);
                    }
                    
                    const sigScVal = this.signatureToScVal(signature, publicKey);
                        
                    addrAuth.signature(xdr.ScVal.scvVec([sigScVal]));
                    signedAuthEntries.push(entry);
                }
            }
            
        }

        return signedAuthEntries;
    }

    private buildLedgerKey(contractId: string): xdr.LedgerKey
    {
        return xdr.LedgerKey.contractData(
            new xdr.LedgerKeyContractData({
                contract: new Address(contractId).toScAddress(),
                key: xdr.ScVal.scvLedgerKeyContractInstance(),
                durability: xdr.ContractDataDurability.persistent(),
            }),
        );
    }

    private buildPreImage(networkId: Buffer, nonce: xdr.Int64, invocation: xdr.SorobanAuthorizedInvocation, signatureExpirationLedger: number): xdr.HashIdPreimage
    {
        return xdr.HashIdPreimage.envelopeTypeSorobanAuthorization(
            new xdr.HashIdPreimageSorobanAuthorization({
                networkId,
                nonce: nonce,
                invocation: invocation,
                signatureExpirationLedger: signatureExpirationLedger,
            }
        ))
    }

    private signatureToScVal(signature: string, publicKey: string): xdr.ScVal
    {
        return nativeToScVal(
            {
              public_key: StrKey.decodeEd25519PublicKey(publicKey),
              signature,
            },
            {
              type: {
                public_key: ["symbol", null],
                signature: ["symbol", null],
              } as any,
            }
        );
           
    }
}