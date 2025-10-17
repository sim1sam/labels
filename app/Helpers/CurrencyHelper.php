<?php

namespace App\Helpers;

use App\Models\Setting;

class CurrencyHelper
{
    /**
     * Get the current currency
     */
    public static function getCurrency()
    {
        return Setting::getCurrency();
    }

    /**
     * Format amount with currency
     */
    public static function format($amount, $decimals = 0)
    {
        $currency = self::getCurrency();
        return number_format($amount, $decimals) . ' ' . $currency;
    }

    /**
     * Get currency symbol (if needed in future)
     */
    public static function getSymbol()
    {
        $currency = self::getCurrency();
        $symbols = [
            'BDT' => '৳',
            'INR' => '₹',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
        ];
        
        return $symbols[$currency] ?? $currency;
    }
}
