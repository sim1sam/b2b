<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'shipping_mark',
        'total_amount',
        'rounded_total',
        'invoice_date',
        'payment_status',
        'delivery_request_status',
        'delivery_status',
        'order_status',
        'dispute_status',
        'dispute_note',
        'dispute_opened_at',
        'admin_response',
        'dispute_resolved_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'rounded_total' => 'decimal:2',
        'invoice_date' => 'date',
        'dispute_opened_at' => 'datetime',
        'dispute_resolved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                $datePrefix = date('ymd'); // YYMMDD format
                
                // Get the last invoice number for today
                $lastInvoice = self::where('invoice_number', 'like', 'INV' . $datePrefix . '%')
                    ->orderBy('invoice_number', 'desc')
                    ->first();
                
                if ($lastInvoice) {
                    // Extract the sequential number and increment
                    $lastSequence = (int) substr($lastInvoice->invoice_number, -4);
                    $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $sequence = '0001';
                }
                
                $invoice->invoice_number = 'INV' . $datePrefix . $sequence;
            }
            
            if (!$invoice->invoice_date) {
                $invoice->invoice_date = now();
            }
        });
    }

    /**
     * Get the user (client) that owns this invoice.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the purchase requests for this invoice.
     */
    public function purchaseRequests()
    {
        return $this->belongsToMany(PurchaseRequest::class, 'invoice_purchase_request')
            ->withTimestamps();
    }

    /**
     * Get the fund transaction for this invoice payment.
     */
    public function paymentTransaction()
    {
        return $this->hasOne(FundTransaction::class, 'invoice_id');
    }
}
