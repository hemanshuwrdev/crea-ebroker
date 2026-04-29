<?php

namespace App\Models;

use App\Traits\HasAppTimezone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasAppTimezone, HasFactory;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'user_id',
        'package_id',
        'amount',
        'payment_gateway',
        'payment_type',
        'order_id',
        'payment_status',
        'transaction_id',
        'invoice_no',
    ];

    public static function generateInvoiceNo()
    {
        $lastInvoice = static::whereNotNull('invoice_no')
            ->orderByRaw('CAST(invoice_no AS UNSIGNED) DESC')
            ->value('invoice_no');
        $next = $lastInvoice ? ((int) $lastInvoice) + 1 : 1;

        return str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            $bankReceiptFiles = $model->bank_receipt_files()->get();
            foreach ($bankReceiptFiles as $bankReceiptFile) {
                $bankReceiptFile->delete();
            }
        });
    }

    /**
     * Get the customer that owns the UserPackage
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }

    /**
     * Get the package that owns the UserPackage
     */
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id')->withTrashed();
    }

    /**
     * Get the bank receipt files for the payment transaction
     */
    public function bank_receipt_files()
    {
        return $this->hasMany(BankReceiptFile::class, 'payment_transaction_id');
    }
}
