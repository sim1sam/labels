<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'customer_name',
        'mobile_number',
        'address',
    ];

    /**
     * Get the parcels for the customer.
     */
    public function parcels()
    {
        return $this->hasMany(Parcel::class, 'customer_name', 'customer_name')
                    ->orWhere('mobile_number', $this->mobile_number);
    }

    /**
     * Find or create customer by name and mobile
     */
    public static function findOrCreate($customerName, $mobileNumber, $address)
    {
        // First try to find by mobile number (most unique)
        $customer = static::where('mobile_number', $mobileNumber)->first();
        
        if (!$customer) {
            // If not found by mobile, try by name and mobile combination
            $customer = static::where('customer_name', $customerName)
                            ->where('mobile_number', $mobileNumber)
                            ->first();
        }
        
        if (!$customer) {
            // Create new customer
            $customer = static::create([
                'customer_name' => $customerName,
                'mobile_number' => $mobileNumber,
                'address' => $address,
            ]);
        } else {
            // Update existing customer with new information if provided
            $updateData = [];
            if ($address && $address !== $customer->address) {
                $updateData['address'] = $address;
            }
            
            if (!empty($updateData)) {
                $customer->update($updateData);
            }
        }
        
        return $customer;
    }

    /**
     * Get customer's full address
     */
    public function getFullAddressAttribute()
    {
        return $this->address;
    }
}