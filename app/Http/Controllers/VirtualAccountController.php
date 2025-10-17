<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EspayVirtualAccount;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class VirtualAccountController extends Controller
{
    /**
     * 🔹 List VA
     */
    public function index()
    {
        $va = EspayVirtualAccount::latest()->get();
        return view('espay.va.index', compact('va'));
    }

    /**
     * 🔹 Form Create
     */
    public function create()
    {
        return view('espay.va.create');
    }

    /**
     * 🔹 Create VA 
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id'   => 'required|string',
                'ccy'        => 'nullable|string|max:5',
                'comm_code'  => 'required|string|max:50',
                'remark1'    => 'nullable|string|max:255',
                'remark2'    => 'nullable|string|max:255',
                'remark3'    => 'nullable|string|max:255',
                'remark4'    => 'nullable|string|max:255',
                'bank_code'  => 'nullable|string|max:10',
                'va_expired' => 'nullable|integer',
            ]);

            $rq_uuid      = (string) Str::uuid();
            $rq_datetime  = now()->format('Y-m-d H:i:s');
            $signatureKey = config('espay.signature_key', '');
            $action       = 'SENDINVOICE';
            $order_id     = $validated['order_id'];
            $ccy          = $validated['ccy'] ?? 'IDR';
            $comm_code    = $validated['comm_code'];
            $bank_code    = $validated['bank_code'] ?? '013';
            $va_expired   = (int) $validated['va_expired'] ?? 30;
            $amount       = '';


            $raw_string = strtoupper("##{$signatureKey}##{$rq_uuid}##{$rq_datetime}##{$order_id}##{$amount}##{$ccy}##{$comm_code}##{$action}##");
            $signature  = hash('sha256', $raw_string);


            $expired_date = Carbon::parse($rq_datetime)->addMinutes($va_expired);

            $payload = [
                'rq_uuid'     => $rq_uuid,
                'rq_datetime' => $rq_datetime,
                'order_id'    => $order_id,
                'amount'      => '',
                'ccy'         => $ccy,
                'comm_code'   => $comm_code,
                'remark1'     => $validated['remark1'] ?? null,
                'remark2'     => $validated['remark2'] ?? null,
                'remark3'     => $validated['remark3'] ?? null,
                'remark4'     => $validated['remark4'] ?? null,
                'update'      => 'N',
                'bank_code'   => $bank_code,
                'va_expired'  => $va_expired,
                'signature'   => $signature,
            ];

            Log::info('[ESPAY REQUEST - CREATE]', $payload);

            $response = Http::asForm()->post('https://sandbox-api.espay.id/rest/merchantpg/sendinvoice', $payload);
            if ($response->failed()) {
                Log::error('[ESPAY ERROR]', ['body' => $response->body()]);
                return back()->with('error', 'Gagal membuat VA di Espay Sandbox.');
            }

            $result = $response->json();
            Log::info('[ESPAY RESPONSE - CREATE]', $result);

            $va = EspayVirtualAccount::create([
                'rq_uuid'       => $result['rq_uuid'] ?? $rq_uuid,
                'rq_datetime'   => $rq_datetime,
                'rs_datetime'   => $result['rs_datetime'] ?? now(),
                'order_id'      => $order_id,
                'ccy'           => $ccy,
                'comm_code'     => $comm_code,
                'bank_code'     => $result['bank_code'] ?? $bank_code,
                'va_expired'    => $va_expired,
                'expired_date'  => $expired_date,
                'va_number'     => $result['va_number'] ?? null,
                'error_code'    => $result['error_code'] ?? null,
                'error_message' => $result['error_message'] ?? null,
                'description'   => $result['description'] ?? null,
                'status'        => ($result['error_code'] ?? '00') === '00' ? 'ACTIVE' : 'FAILED',
                'signature'     => $signature,
                'remark1'       => $validated['remark1'] ?? null,
                'remark2'       => $validated['remark2'] ?? null,
                'remark3'       => $validated['remark3'] ?? null,
                'remark4'       => $validated['remark4'] ?? null,
                'update_flag'   => 'N',
            ]);

            return redirect()->route('va.index')->with('success', 'VA berhasil dibuat di Espay Sandbox.');
        } catch (\Throwable $th) {
            Log::error('Espay SendInvoice Error', ['error' => $th->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan: ' . $th->getMessage());
        }
    }

    /**
     * 🔹 Form Edit VA
     */
    public function edit($id)
    {
        $va = EspayVirtualAccount::findOrFail($id);
        return view('espay.va.edit', compact('va'));
    }

    /**
     * 🔹 Update VA di Espay 
     */
    public function update(Request $request, $id)
    {
        // TODO: Testing Update VA Espay
        try {
            $validated = $request->validate([
                'remark1'    => 'nullable|string|max:255',
                'remark2'    => 'nullable|string|max:255',
                'remark3'    => 'nullable|string|max:255',
                'remark4'    => 'nullable|string|max:255',
                'va_expired' => 'nullable|integer',
            ]);

            $va = EspayVirtualAccount::findOrFail($id);

            $rq_uuid      = (string) Str::uuid();
            $rq_datetime  = now()->format('Y-m-d H:i:s');
            $signatureKey = config('espay.signature_key', '');
            $action       = 'SENDINVOICE';


            $order_id   = $va->order_id;
            $ccy        = $va->ccy ?? 'IDR';
            $comm_code  = $va->comm_code;
            $bank_code  = $validated['bank_code'] ?? $va->bank_code;
            $va_expired = $validated['va_expired'] ?? $va->va_expired;
            $amount     = '';


            $raw_string = strtoupper("##{$signatureKey}##{$rq_uuid}##{$rq_datetime}##{$order_id}##{$amount}##{$ccy}##{$comm_code}##{$action}##");
            $signature  = hash('sha256', $raw_string);


            $expired_date = Carbon::parse($rq_datetime)->addMinutes((int) $va_expired);

            $payload = [
                'rq_uuid'     => $rq_uuid,
                'rq_datetime' => $rq_datetime,
                'order_id'    => $order_id,
                'amount'      => '',
                'ccy'         => $ccy,
                'comm_code'   => $comm_code,
                'remark1'     => $validated['remark1'] ?? $va->remark1,
                'remark2'     => $validated['remark2'] ?? $va->remark2,
                'remark3'     => $validated['remark3'] ?? $va->remark3,
                'remark4'     => $validated['remark4'] ?? $va->remark4,
                'update'      => 'Y',
                'bank_code'   => '013',
                'va_expired'  => (int) $va_expired,
                'signature'   => $signature,
            ];

            Log::info('[ESPAY REQUEST - UPDATE]', $payload);

            // $response = Http::asForm()->post('https://sandbox-api.espay.id/rest/merchantpg/sendinvoice', $payload);
            $response = Http::withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->send('POST', 'https://sandbox-api.espay.id/rest/merchantpg/sendinvoice', [
                'body' => http_build_query($payload, '', '&', PHP_QUERY_RFC3986),
            ]);
            if ($response->failed()) {
                Log::error('[ESPAY UPDATE ERROR]', ['body' => $response->body()]);
                return back()->with('error', 'Gagal mengupdate VA di Espay Sandbox.');
            }

            $result = $response->json();
            Log::info('[ESPAY RESPONSE - UPDATE]', $result);

            $va->update([
                'rq_uuid'       => $rq_uuid,
                'rq_datetime'   => $rq_datetime,
                'rs_datetime'   => $result['rs_datetime'] ?? now(),
                'bank_code'     => $bank_code,
                'va_expired'    => (int) $va_expired,
                'expired_date'  => $expired_date,
                'error_code'    => $result['error_code'] ?? null,
                'error_message' => $result['error_message'] ?? null,
                'description'   => $result['description'] ?? null,
                'signature'     => $signature,
                'update_flag'   => 'Y',
                'status'        => ($result['error_code'] ?? '00') === '00' ? 'ACTIVE' : 'FAILED',
                'remark1'       => $validated['remark1'] ?? $va->remark1,
                'remark2'       => $validated['remark2'] ?? $va->remark2,
                'remark3'       => $validated['remark3'] ?? $va->remark3,
                'remark4'       => $validated['remark4'] ?? $va->remark4,
                'update_at'     => now(),
            ]);

            return redirect()->route('va.index')->with('success', 'VA berhasil diperbarui di Espay Sandbox.');
        } catch (\Throwable $th) {
            Log::error('Espay UpdateInvoice Error', ['error' => $th->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat update: ' . $th->getMessage());
        }
    }

    /**
     * 🔹 Hapus VA
     */

    public function destroy($id)
    {
        Log::info('[ESPAY DELETE - START]');
        $va = EspayVirtualAccount::findOrFail($id);

        $partnerServiceId = "ESPAY";
        $customerNo = config('espay.merchant_code', '');
        $virtualAccountNo = $va->order_id;

        $timestamp = now()->toIso8601String();

        $prefix = Carbon::today()->format('Ymd');
        $randomNumber = mt_rand(10000, 99999);

        $xExternalId = $prefix . $randomNumber;

        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
        ];

        try {
            $minifiedBody = json_encode($body, JSON_UNESCAPED_SLASHES);
            $hashedBody = hash('sha256', $minifiedBody);
            $method = 'DELETE';
            $relativeUrl = '/apimerchant/v1.0/transfer-va/delete-va';
            $stringToSign = "{$method}:{$relativeUrl}:{$hashedBody}:{$timestamp}";

            $privateKeyPath = storage_path('app/private.pem');
            $privateKey = openssl_pkey_get_private(file_get_contents($privateKeyPath));

            if (!$privateKey) {
                throw new \Exception('Private key tidak valid.');
            }

            openssl_sign($stringToSign, $binarySignature, $privateKey, OPENSSL_ALGO_SHA256);
            $xSignature = base64_encode($binarySignature);

            Log::info('[ESPAY SIGNATURE KEY]', ['signature' => $xSignature]);

            $response = Http::withHeaders([
                'Content-Type'   => 'application/json',
                'X-TIMESTAMP'    => $timestamp,
                'X-SIGNATURE'    => $xSignature,
                'X-EXTERNAL-ID'  => $xExternalId,
                'X-PARTNER-ID' => $customerNo,
                'CHANNEL-ID' => $partnerServiceId,
            ])->delete('https://sandbox-api.espay.id/apimerchant/v1.0/transfer-va/delete-va', $body);


            // dd($response->body(), $timestamp, $xSignature, $xExternalId, $partnerServiceId, $customerNo, $virtualAccountNo, $minifiedBody, $stringToSign);
            Log::info('[ESPAY RESPONSE - DELETE]');
            Log::info($response->body());

            if ($response->failed()) {
                Log::error('Espay DeleteInvoice Error', ['body' => $response->body()]);
                return back()->with('error', 'Gagal menghapus VA di Espay: ' . $response->body());
            }

            $va->delete();

            return redirect()->route('va.index')->with('success', 'Virtual Account berhasil dihapus di Espay Sandbox.');
        } catch (\Throwable $th) {
            Log::error('Espay DeleteInvoice Error', ['error' => $th->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat delete: ' . $th->getMessage());
        }
    }
}
