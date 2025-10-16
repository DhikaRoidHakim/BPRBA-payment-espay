<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //
    protected $table = 'transaction';
    protected $fillable = [
        'trx_id',
        'payment_request_id',
        'va_number',
        'customer_no',
        'paid_amount',
        'total_amount',
        'currency',
        'status',
        'trx_datetime',
        'paid_at',
        'member_code',
        'debit_from',
        'debit_from_name',
        'debit_from_bank',
        'credit_to',
        'credit_to_name',
        'credit_to_bank',
        'product_code',
        'product_value',
        'fee_type',
        'tx_fee',
        'payment_ref',
        'user_id',
    ];
    protected $casts = [
        'paid_at' => 'datetime',
        'trx_datetime' => 'datetime',
    ];
}
