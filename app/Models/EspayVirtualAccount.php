<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EspayVirtualAccount extends Model
{
    //
    protected $table = 'espay_virtualaccount';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'rq_uuid',
        'rq_datetime',
        'rs_datetime',
        'order_id',
        'ccy',
        'comm_code',
        'bank_code',
        'va_expired',
        'expired_date',
        'va_number',
        'error_code',
        'error_message',
        'description',
        'signature',
        'update_flag',
        'remark1',
        'remark2',
        'remark3',
        'remark4',
        'status',
    ];

    protected $casts = [
        'rq_datetime' => 'datetime',
        'rs_datetime' => 'datetime',
        'expired_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // UUID otomatis
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }

            // Hitung expired_date otomatis berdasarkan rq_datetime + va_expired
            if (empty($model->expired_date) && !empty($model->rq_datetime) && !empty($model->va_expired)) {
                $model->expired_date = Carbon::parse($model->rq_datetime)->addMinutes($model->va_expired);
            }
        });
    }

    /**
     * 🔄 VA Check Expired
     */
    public function isExpired(): bool
    {
        return Carbon::now()->greaterThan($this->expired_date);
    }
}
