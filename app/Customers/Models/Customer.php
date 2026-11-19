<?php

namespace App\Customers\Models;

use App\Orders\Models\Order;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class Customer extends Model
{
    /**
     * @return HasMany<CustomerPhone, $this>
     */
    public function phones(): HasMany
    {
        return $this->hasMany(CustomerPhone::class)->orderBy('id');
    }

    /**
     * @return HasMany<CustomerEmail, $this>
     */
    public function emails(): HasMany
    {
        return $this->hasMany(CustomerEmail::class)->orderBy('id');
    }

    /**
     * @return HasMany<CustomerAddress, $this>
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class)->orderBy('id');
    }

    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }
}
