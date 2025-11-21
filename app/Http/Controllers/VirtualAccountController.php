<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EspayVirtualAccount;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\ImageService;
use Illuminate\Support\Facades\Bus;
use App\Jobs\UpdateVaExpiredJob;

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
     * 🔹 With Image 
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
            //LOG respone code
            Log::info('[ESPAY RESPONSE CODE - CREATE]', ['code' => $response->status()]);
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

            // Log Activity
            activity('create-va')
                ->performedOn($va)
                ->causedBy(Auth::user())
                ->withProperties(['order_id' => $order_id, 'va_number' => $result['va_number']])
                ->log('VA berhasil dibuat di Espay Sandbox.');

            // Generate Image
            $imageService = new ImageService();
            $imageService->generateVaImage($order_id, $result['va_number']);
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

            // Production
            $createVAUrl = 'https://api.espay.id/rest/merchantpg/sendinvoice';

            // Sandbox
            // $createVAUrlSanbox = 'https://sandbox-api.espay.id/rest/merchantpg/sendinvoice';

            $response = Http::withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->send('POST', $createVAUrl, [
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
            'partnerServiceId'   => $partnerServiceId,
            'customerNo'         => $customerNo,
            'virtualAccountNo'   => $virtualAccountNo,
        ];

        try {
            // 🔹 Encode body + hash buat signing
            $minifiedBody = json_encode($body, JSON_UNESCAPED_SLASHES);
            $hashedBody = hash('sha256', $minifiedBody);
            $method = 'DELETE';
            $relativeUrl = '/apimerchant/v1.0/transfer-va/delete-va';
            $stringToSign = "{$method}:{$relativeUrl}:{$hashedBody}:{$timestamp}";

            // 🔹 Load private key
            $privateKeyPath = storage_path('app/private.pem');
            $privateKey = openssl_pkey_get_private(file_get_contents($privateKeyPath));

            if (!$privateKey) {
                throw new \Exception('Private key tidak valid.');
            }

            openssl_sign($stringToSign, $binarySignature, $privateKey, OPENSSL_ALGO_SHA256);
            $xSignature = base64_encode($binarySignature);

            // 🔹 Log request
            Log::info('[ESPAY DELETE - REQUEST]', [
                'url' => 'https://sandbox-api.espay.id/apimerchant/v1.0/transfer-va/delete-va',
                'method' => $method,
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'X-TIMESTAMP'   => $timestamp,
                    'X-SIGNATURE'   => $xSignature,
                    'X-EXTERNAL-ID' => $xExternalId,
                    'X-PARTNER-ID'  => $customerNo,
                    'CHANNEL-ID'    => $partnerServiceId,
                ],
                'body' => $body,
                'string_to_sign' => $stringToSign,
            ]);

            // 🔹 Eksekusi request
            $response = Http::withHeaders([
                'Content-Type'   => 'application/json',
                'X-TIMESTAMP'    => $timestamp,
                'X-SIGNATURE'    => $xSignature,
                'X-EXTERNAL-ID'  => $xExternalId,
                'X-PARTNER-ID'   => $customerNo,
                'CHANNEL-ID'     => $partnerServiceId,
            ])->delete('https://sandbox-api.espay.id/apimerchant/v1.0/transfer-va/delete-va', $body);

            // 🔹 Log response
            Log::info('[ESPAY DELETE - RESPONSE]', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->json() ?? $response->body(),
            ]);

            if ($response->failed()) {
                Log::error('[ESPAY DELETE - FAILED]', ['body' => $response->body()]);
                return back()->with('error', 'Gagal menghapus VA di Espay: ' . $response->body());
            }

            // 🔹 Hapus dari DB kalau sukses
            $va->delete();

            Log::info('[ESPAY DELETE - SUCCESS]', ['va_id' => $id]);

            return redirect()->route('va.index')->with('success', 'Virtual Account berhasil dihapus di Espay Sandbox.');
        } catch (\Throwable $th) {
            Log::error('[ESPAY DELETE - ERROR]', ['error' => $th->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat delete: ' . $th->getMessage());
        }
    }


    /**
     * 🔹 Mass Update VA Expired
     */
    public function massUpdate(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        $batch = Bus::batch([])->name('Mass Update VA')->dispatch();

        foreach ($request->ids as $id) {
            $batch->add(new UpdateVaExpiredJob($id));
        }

        return response()->json(['batch_id' => $batch->id]);
    }
}
