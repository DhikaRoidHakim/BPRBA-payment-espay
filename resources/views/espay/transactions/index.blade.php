@extends('layouts.app')

@section('title', 'Transaction List')
@section('page-title', 'Daftar Transaksi Pembayaran')

@section('content')
    <div class="bg-white rounded-xl shadow p-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                <span class="material-symbols-outlined text-blue-600 mr-2">receipt_long</span>
                Transaction List
            </h3>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table id="transactionTable" class="min-w-full text-sm text-gray-700 border border-gray-100 rounded-lg">
                <thead>
                    <tr class="bg-gray-50 text-left uppercase text-xs font-semibold tracking-wider text-gray-500">
                        <th class="py-3 px-4">Trx ID</th>
                        <th class="py-3 px-4">VA Number</th>
                        <th class="py-3 px-4">Customer No</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $trx)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="py-2 px-4 font-medium text-gray-800">{{ $trx->trx_id }}</td>
                            <td class="py-2 px-4 text-blue-600">{{ $trx->va_number }}</td>
                            <td class="py-2 px-4">{{ $trx->customer_no ?? '-' }}</td>
                            <td class="py-2 px-4">Rp {{ number_format($trx->paid_amount, 0, ',', '.') }}</td>
                            <td class="py-2 px-4">
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
                            </td>
                            <td class="py-2 px-4 text-gray-600">
                                {{ $trx->paid_at ? $trx->paid_at->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="py-2 px-4 text-center">
                                <a href="{{ route('transactions.show', $trx->id) }}"
                                    class="text-blue-600 hover:text-blue-800" title="Detail">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ✅ DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

    <script>
        $(document).ready(function() {
            setTimeout(() => {
                $('#transactionTable').DataTable({
                    responsive: true,
                    order: [
                        [5, 'desc']
                    ],
                    pageLength: 10,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
                        emptyTable: 'Belum ada transaksi yang tercatat.',
                        paginate: {
                            first: '<<',
                            last: '>>',
                            next: '>',
                            previous: '<'
                        }
                    }
                });
            }, 200);
        });
    </script>
@endsection
