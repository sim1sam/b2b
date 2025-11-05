<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_name',
        'rate_per_unit',
        'unit_type',
        'shipping_charge_per_kg',
        'is_active',
    ];

    protected $casts = [
        'rate_per_unit' => 'decimal:2',
        'shipping_charge_per_kg' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user (client) that owns this shipping charge.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
