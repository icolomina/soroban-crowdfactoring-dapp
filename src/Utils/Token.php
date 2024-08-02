<?php

namespace App\Utils;

class Token {

    public function normalizeTokenValue(string $value, int $tokenDecimals): string
    {
        $lenDif = strlen($value) - $tokenDecimals;

        if($lenDif < 0) {
            $value  = str_pad($value, ($lenDif * -1) + strlen($value), '0', STR_PAD_LEFT);
            $lenDif = strlen($value) - $tokenDecimals;
        }

        return substr($value, 0, $lenDif) . '.' . substr($value, $lenDif, strlen($value));   
    }
}