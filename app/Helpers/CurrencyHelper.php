<?php

if (! function_exists('platform_currency')) {
    function platform_currency(): string
    {
        return strtoupper((string) config('currency.code', 'USD')) ?: 'USD';
    }
}

if (! function_exists('currency_symbol')) {
    function currency_symbol(): string
    {
        return (string) config('currency.symbol', '$');
    }
}

if (! function_exists('currency_label')) {
    function currency_label(): string
    {
        return (string) config('currency.label', 'USD');
    }
}
