import { Contract, ScInt, SorobanRpc, Transaction, TransactionBuilder, xdr } from "@stellar/stellar-sdk";
import { Address } from "@stellar/stellar-sdk";
import { TokenParser } from "../soroban/token";
import { StellarWalletsKit, WalletNetwork } from "@creit.tech/stellar-wallets-kit";
import { Api } from '@stellar/stellar-sdk/lib/rpc';

export const SendTxStatus: {
    [index: string]: SorobanRpc.Api.SendTransactionStatus;
  } = {
    Pending: "PENDING",
    Duplicate: "DUPLICATE",
    Retry: "TRY_AGAIN_LATER",
    Error: "ERROR",
  };


export class ScContract {

    private server: SorobanRpc.Server;
    private baseFee: string = '100';

    async init(url: string): Promise<void> {
        this.server = new SorobanRpc.Server(url);
        const healthResponse = await this.server.getHealth();
        if(healthResponse.status !== 'healthy') {
            // lanzamos exception
        }
    }

    async sendDeposit(wallet: StellarWalletsKit, contract: string, amount: string) {

        const tokenParser  = new TokenParser();

        const address     = await wallet.getPublicKey();
        const account     = await this.server.getAccount(address);
        const c           = new Contract(contract);
        const amountScVal = new ScInt(tokenParser.parseAmount(amount, 4).toString()).toI128();
        const scAddress   = new Address(address).toScVal();
        
        const tx = new TransactionBuilder(account, { fee: this.baseFee, networkPassphrase: 'Test SDF Network ; September 2015'})
            .addOperation(c.call('user_deposit', scAddress, amountScVal ))
            .setTimeout(30)
            .build()
        ;

        const readyTx = await this.prepareTransaction(tx, address, c.contractId(), wallet);
        const result = await wallet.signTx({xdr: readyTx.toXDR(), publicKeys: [address], network: WalletNetwork.TESTNET});
        return this.server.sendTransaction(TransactionBuilder.fromXDR(result.result, 'Test SDF Network ; September 2015'));
    }

    private async prepareTransaction(tx: Transaction, address: string, contractId: string, wallet: StellarWalletsKit): Promise<Transaction> {
        const simulatedTx = await this.server.simulateTransaction(tx);
        return SorobanRpc.assembleTransaction(tx, simulatedTx).build();


        /*const rawInvokeHostFunctionOp = preparedTx.operations[0] as Operation.InvokeHostFunction;
        const auth: xdr.SorobanAuthorizationEntry[] = rawInvokeHostFunctionOp.auth ? rawInvokeHostFunctionOp.auth : [];
        if(auth.length > 0) {
            const sorobanAuth = new SorobanAuth(auth);
            const signedAuthEntries = await sorobanAuth.signEntries(address, contractId, this.server, wallet);
            const tbuilder = TransactionBuilder.cloneFrom(preparedTx);
            tbuilder.clearOperations().addOperation(
                Operation.invokeHostFunction({
                  ...rawInvokeHostFunctionOp,
                  auth: signedAuthEntries,
                }),
              )
            ;

            preparedTx = tbuilder.build();
        }*/

        //return preparedTx;
    }

    public async queryTransaction(sendTransactionResponse: Api.SendTransactionResponse): Promise<string> {
        if (sendTransactionResponse.status === SendTxStatus.Pending) {
            let txResponse = await this.server.getTransaction(sendTransactionResponse.hash);

            while (txResponse.status === SorobanRpc.Api.GetTransactionStatus.NOT_FOUND) {
                txResponse = await this.server.getTransaction(sendTransactionResponse.hash);
                await new Promise((resolve) => setTimeout(resolve, 1000));
            }
          
            return txResponse.status;
          }
    }

}