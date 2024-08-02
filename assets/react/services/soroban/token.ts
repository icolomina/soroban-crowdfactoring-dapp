import BigNumber from "bignumber.js";

export class TokenParser {

    parseAmount(value: string, decimals: number): BigNumber {
        const tokenValue = TokenValue.fromString(value, decimals);
        const wholeValue = new BigNumber(tokenValue.whole);
        const fractionValue = new BigNumber(tokenValue.fraction);
      
        return wholeValue.shiftedBy(decimals).plus(fractionValue);
    }
}

export class TokenValue {
    constructor(
        public whole: string,
        public fraction: string,
        public readonly decimals: number
    ){}

    toString(): string {
        return this.whole + '.' + this.fraction;
    }

    static fromString(value: string, decimals: number): TokenValue {
        let tokenValue: TokenValue;
        if(value.indexOf('.') < 0) {
            tokenValue = new TokenValue(value, '', decimals);
        }
        else{
            const parts = value.split('.');
            tokenValue = new TokenValue(
                parts[0],
                parts[1],
                decimals
            );
        }
        

        tokenValue.fraction = tokenValue.fraction.replace(/0+$/, '')
        if (tokenValue.fraction === '') {
            tokenValue.fraction = '0';
        }
        
        while (tokenValue.fraction.length < tokenValue.decimals) {
            tokenValue.fraction += '0';
        }

        return tokenValue;
    }
}