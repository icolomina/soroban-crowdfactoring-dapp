import { 
    FREIGHTER_ID, 
    FreighterModule, 
    HANA_ID, 
    HanaModule, 
    LOBSTR_ID, 
    LobstrModule, 
    ModuleInterface, 
    RABET_ID, 
    RabetModule, 
    StellarWalletsKit, 
    WalletNetwork, 
    XBULL_ID, 
    xBullModule 
} from "@creit.tech/stellar-wallets-kit";

export const useWallet = (types: string[], selectedType: string, network: WalletNetwork): any[] => {
    const modules: ModuleInterface[]  = [];
    types.forEach( (t: string) => {
        switch(t) {
            case XBULL_ID:
                modules.push(new xBullModule())
                break;
            case FREIGHTER_ID:
                modules.push(new FreighterModule());
                break;
            case LOBSTR_ID:
                modules.push(new LobstrModule());
                break;
            case HANA_ID:
                modules.push(new HanaModule());
                break;
            case RABET_ID:
                modules.push(new RabetModule());
                break;
        }
    });

    const kit = new StellarWalletsKit({network: network, selectedWalletId: selectedType, modules: modules});

    return [
        kit,
        false,
    ];
}

