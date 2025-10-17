<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key
     */
    public static function set($key, $value, $description = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'description' => $description]
        );
    }

    /**
     * Get currency setting
     */
    public static function getCurrency()
    {
        return static::get('currency', 'BDT');
    }

    /**
     * Set currency setting
     */
    public static function setCurrency($currency)
    {
        return static::set('currency', $currency, 'Default currency for the application');
    }

    /**
     * Get available currencies
     */
    public static function getAvailableCurrencies()
    {
        return [
            'BDT' => 'Bangladeshi Taka',
            'INR' => 'Indian Rupee',
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
        ];
    }
}