@extends('layouts.app')

@section('title', 'Transaction Detail')
@section('page-title', 'Detail Transaksi')

@section('content')
    <div class="bg-white rounded-xl shadow p-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                <span class="material-symbols-outlined text-blue-600 mr-2">receipt_long</span>
                Detail Transaksi
            </h3>

            <a href="{{ route('transactions.index') }}" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                <span class="material-symbols-outlined text-[18px] mr-1">arrow_back</span> Kembali
            </a>
        </div>

        {{-- Detail Card --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Informasi Transaksi</h4>
                <ul class="text-sm text-gray-700 space-y-2">
                    <li><strong>Trx ID:</strong> {{ $trx->trx_id }}</li>
                    <li><strong>Payment Request ID:</strong> {{ $trx->payment_request_id }}</li>
                    <li><strong>Customer No:</strong> {{ $trx->customer_no }}</li>
                    <li><strong>VA Number:</strong> {{ $trx->va_number }}</li>
                    <li><strong>Status:</strong>
                        @php
                            $statusColor = match ($trx->status) {
                                'PAID' => 'bg-green-100 text-green-700',
                                'FAILED' => 'bg-red-100 text-red-700',
                                'PENDING' => 'bg-yellow-100 text-yellow-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                            {{ strtoupper($trx->status) }}
                        </span>
                    </li>
                    <li><strong>Tanggal Transaksi:</strong>
                        {{ $trx->trx_datetime ? $trx->trx_datetime->format('d M Y H:i') : '-' }}
                    </li>
                    <li><strong>Dibayar Pada:</strong>
                        {{ $trx->paid_at ? $trx->paid_at->format('d M Y H:i') : '-' }}
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Detail Pembayaran</h4>
                <ul class="text-sm text-gray-700 space-y-2">
                    <li><strong>Nominal:</strong> Rp {{ number_format($trx->paid_amount, 0, ',', '.') }}</li>
                    <li><strong>Total Amount:</strong> Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</li>
                    <li><strong>Currency:</strong> {{ $trx->currency }}</li>
                    <li><strong>Fee Type:</strong> {{ $trx->fee_type ?? '-' }}</li>
                    <li><strong>Transaction Fee:</strong> Rp {{ number_format($trx->tx_fee, 0, ',', '.') }}</li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-gray-700 mb-2 mt-4">Akun Debit (Pengirim)</h4>
                <ul class="text-sm text-gray-700 space-y-2">
                    <li><strong>Nomor Akun:</strong> {{ $trx->debit_from ?? '-' }}</li>
                    <li><strong>Nama:</strong> {{ $trx->debit_from_name ?? '-' }}</li>
                    <li><strong>Bank:</strong> {{ $trx->debit_from_bank ?? '-' }}</li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-gray-700 mb-2 mt-4">Akun Kredit (Tujuan)</h4>
                <ul class="text-sm text-gray-700 space-y-2">
                    <li><strong>Nomor Akun:</strong> {{ $trx->credit_to ?? '-' }}</li>
                    <li><strong>Nama:</strong> {{ $trx->credit_to_name ?? '-' }}</li>
                    <li><strong>Bank:</strong> {{ $trx->credit_to_bank ?? '-' }}</li>
                </ul>
            </div>

            <div class="md:col-span-2">
                <h4 class="font-semibold text-gray-700 mb-2 mt-4">Informasi Tambahan</h4>
                <ul class="text-sm text-gray-700 space-y-2">
                    <li><strong>Product Code:</strong> {{ $trx->product_code ?? '-' }}</li>
                    <li><strong>Product Value:</strong> {{ $trx->product_value ?? '-' }}</li>
                    <li><strong>Payment Ref:</strong> {{ $trx->payment_ref ?? '-' }}</li>
                    <li><strong>User ID:</strong> {{ $trx->user_id ?? '-' }}</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
