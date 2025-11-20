<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use App\Notifications\PaymentReceivedNotification;
use App\Models\User;

class EspayController extends Controller
{
    //
    /**
     * Menerima notifikasi pembayaran dari Espay
     */
    public function receive(Request $request)
    {
        Log::info('=== [ESPAY CALLBACK RECEIVED] ===');
        Log::info('Headers:', $request->headers->all());
        Log::info('Payload:', $request->all());

        $trxId         = $request->input('trxId');
        $paymentId     = $request->input('paymentRequestId');
        $vaNumber      = $request->input('virtualAccountNo');
        $customerNo    = $request->input('customerNo');
        $paidAmount    = $request->input('paidAmount.value', 0);
        $totalAmount   = $request->input('totalAmount.value', 0);
        $currency      = $request->input('paidAmount.currency', 'IDR');
        $trxDateTime   = $request->input('trxDateTime');
        $status        = $request->input('additionalInfo.transactionStatus', 'PENDING');

        $info = $request->input('additionalInfo', []);

        $transaction = Transaction::updateOrCreate(
            ['trx_id' => $trxId],
            [
                'payment_request_id' => $paymentId,
                'va_number'          => $vaNumber,
                'customer_no'        => $customerNo,
                'paid_amount'        => $paidAmount,
                'total_amount'       => $totalAmount,
                'currency'           => $currency,
                'status'             => $status === 'S' ? 'PAID' : 'FAILED',
                'trx_datetime'       => $trxDateTime,
                'paid_at'            => now(),

                'member_code'        => $info['memberCode'] ?? null,
                'debit_from'         => $info['debitFrom'] ?? null,
                'debit_from_name'    => $info['debitFromName'] ?? null,
                'debit_from_bank'    => $info['debitFromBank'] ?? null,
                'credit_to'          => $info['creditTo'] ?? null,
                'credit_to_name'     => $info['creditToName'] ?? null,
                'credit_to_bank'     => $info['creditToBank'] ?? null,
                'product_code'       => $info['productCode'] ?? null,
                'product_value'      => $info['productValue'] ?? null,
                'fee_type'           => $info['feeType'] ?? null,
                'tx_fee'             => $info['txFee'] ?? 0,
                'payment_ref'        => $info['paymentRef'] ?? null,
                'user_id'            => $info['userId'] ?? null,
            ]
        );


        // Mengirimkan Notifikasi Ke admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notify(new PaymentReceivedNotification($transaction));
        }
        Log::info("Transaction saved/updated successfully", ['trx_id' => $trxId]);

        $response = [
            'responseCode' => '2002500',
            'responseMessage' => 'Payment received successfully',
            'virtualAccountData' => [
                'partnerServiceId'   => $request->input('partnerServiceId'),
                'customerNo'         => $customerNo,
                'virtualAccountNo'   => $vaNumber,
                'virtualAccountName' => 'Customer ' . $customerNo,
                'paymentRequestId'   => $paymentId,
                'totalAmount'        => $request->input('totalAmount'),
                'billDetails'        => $request->input('billDetails', []),
            ],
            'additionalInfo' => [
                'reconcileId'       => 'RC-' . now()->format('YmdHis'),
                'reconcileDatetime' => now()->toIso8601String(),
            ],
        ];

        // Log Response
        Log::info('=== [ESPAY Response Payment] ===');
        Log::info('Payload:', $response);
        return response()->json($response, 200);
    }

    public function testingWithBody(Request $request)
    {

        Log::info('=== [TESTING WITH BODY] ===');
        Log::info('Headers:', $request->headers->all());
        Log::info('Payload:', $request->all());

        $nama = $request->input('nama', 'default');

        return response()->json([
            'status' => 'success',
            'message' => 'Testing with body',
            'nama' => $nama,
        ]);
    }
}
