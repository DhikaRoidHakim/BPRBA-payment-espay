<?php

namespace App\Jobs;

use App\Models\EspayVirtualAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Batchable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UpdateVaExpiredJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $vaId;

    public function __construct($vaId)
    {
        $this->vaId = $vaId;
    }

    public function handle()
    {
        $va = EspayVirtualAccount::find($this->vaId);
        if (!$va) return;

        $rq_uuid = (string) Str::uuid();
        $rq_datetime = now()->format('Y-m-d H:i:s');
        $signatureKey = config('espay.signature_key', '');
        $action = 'SENDINVOICE';

        $raw_string = strtoupper("##{$signatureKey}##{$rq_uuid}##{$rq_datetime}##{$va->order_id}####IDR##{$va->comm_code}##{$action}##");
        $signature  = hash('sha256', $raw_string);

        $va_expired_minutes = 60 * 24 * 30; 
        $new_expired = now()->addMinutes($va_expired_minutes);

        $payload = [
            'rq_uuid'     => $rq_uuid,
            'rq_datetime' => $rq_datetime,
            'order_id'    => $va->order_id,
            'amount'      => '', 
            'ccy'         => $va->ccy ?? 'IDR',
            'comm_code'   => $va->comm_code,
            'bank_code'   => '013',
            'va_expired'  => (int) $va_expired_minutes,
            'signature'   => $signature,
            'update'      => 'Y',
        ];

        Log::info('[ESPAY MASS UPDATE REQUEST]', $payload);

        $response = Http::asForm()->post('https://sandbox-api.espay.id/rest/merchantpg/sendinvoice', $payload);

        Log::info('[ESPAY MASS UPDATE RESPONSE]', [
            'id' => $va->id,
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        if ($response->successful()) {
            $result = $response->json();
            $va->update([
                'va_expired'   => $va_expired_minutes,
                'expired_date' => $new_expired,
                'status'       => ($result['error_code'] ?? '00') === '00' ? 'ACTIVE' : 'FAILED',
                'error_code'   => $result['error_code'] ?? null,
                'error_message'=> $result['error_message'] ?? null,
                'description'  => $result['description'] ?? null,
                'update_flag'  => 'Y',
                'rq_uuid'      => $rq_uuid,
                'rq_datetime'  => $rq_datetime,
                'rs_datetime'  => $result['rs_datetime'] ?? now(),
            ]);
        } else {
            Log::error('[ESPAY MASS UPDATE FAILED]', [
                'id' => $va->id,
                'body' => $response->body()
            ]);
        }
    }
}
