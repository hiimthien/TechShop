<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = ['user_id', 'address_id', 'total_price', 'shipping_fee', 'ghn_order_code', 'expected_delivery_at', 'status', 'coupon_id', 'discount_amount'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }


    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}