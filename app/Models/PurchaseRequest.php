<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\PurchaseOrderItem;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vendor_id',
        'request_number',
        'po_number',
        'amount',
        'amount_inr',
        'is_gst_payment',
        'amount_bdt',
        'description',
        'status',
        'payment_status',
        'payment_screenshot',
        'invoice',
        'tracking_id_file',
        'transportation_charge',
        'shipping_mark',
        'fund_transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_inr' => 'decimal:2',
        'amount_bdt' => 'decimal:2',
        'is_gst_payment' => 'boolean',
        'transportation_charge' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchaseRequest) {
            if (!$purchaseRequest->request_number) {
                $purchaseRequest->request_number = 'PR-' . strtoupper(Str::random(8));
            }
            
            // Generate PO Number: PO + YYMMDD + sequential number
            if (!$purchaseRequest->po_number) {
                $datePrefix = date('ymd'); // YYMMDD format
                
                // Get the last PO number for today
                $lastPO = self::where('po_number', 'like', 'PO' . $datePrefix . '%')
                    ->orderBy('po_number', 'desc')
                    ->first();
                
                if ($lastPO) {
                    // Extract the sequential number and increment
                    $lastSequence = (int) substr($lastPO->po_number, -4);
                    $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $sequence = '0001';
                }
                
                $purchaseRequest->po_number = 'PO' . $datePrefix . $sequence;
            }
        });
    }

    /**
     * Get the user that owns the purchase request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vendor for this purchase request.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the fund transaction associated with this purchase request.
     */
    public function fundTransaction()
    {
        return $this->belongsTo(FundTransaction::class);
    }

    /**
     * Get the items for this purchase request.
     */
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Get the invoices for this purchase request.
     */
    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_purchase_request')
            ->withTimestamps();
    }
}
