<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'shipping_charge_id',
        'item_name',
        'quantity',
        'weight',
        'rate_per_unit',
        'unit_type',
        'item_cost',
        'shipping_cost',
        'total_cost',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'weight' => 'decimal:2',
        'rate_per_unit' => 'decimal:2',
        'item_cost' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    /**
     * Get the purchase request that owns this item.
     */
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    /**
     * Get the shipping charge associated with this item.
     */
    public function shippingCharge()
    {
        return $this->belongsTo(ShippingCharge::class);
    }
}
