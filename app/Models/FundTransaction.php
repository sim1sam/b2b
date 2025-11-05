<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'status',
        'payment_screenshot',
        'notes',
        'admin_note',
        'purchase_request_id',
        'invoice_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the purchase request associated with this transaction.
     */
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    /**
     * Get the invoice associated with this transaction.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Calculate user's current balance.
     */
    public static function getBalance($userId)
    {
        $approved = self::where('user_id', $userId)
            ->where('status', 'approved')
            ->sum('amount');
        
        return $approved;
    }

    /**
     * Get available balance (deposits - withdrawals - purchases - invoice payments).
     */
    public static function getAvailableBalance($userId)
    {
        $deposits = self::where('user_id', $userId)
            ->where('type', 'deposit')
            ->where('status', 'approved')
            ->sum('amount');
        
        $withdrawals = self::where('user_id', $userId)
            ->where('type', 'withdrawal')
            ->where('status', 'approved')
            ->sum('amount');
        
        $purchases = self::where('user_id', $userId)
            ->where('type', 'purchase')
            ->where('status', 'approved')
            ->sum('amount');
        
        $invoicePayments = self::where('user_id', $userId)
            ->where('type', 'invoice_payment')
            ->where('status', 'approved')
            ->sum('amount');
        
        return $deposits - $withdrawals - $purchases - $invoicePayments;
    }
}
