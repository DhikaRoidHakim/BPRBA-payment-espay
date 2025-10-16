@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
    <div class="space-y-6">

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            {{-- Total Paid --}}
            <div class="bg-white rounded-xl shadow p-6 flex items-center">
                <div class="p-3 bg-green-100 text-green-600 rounded-full mr-4">
                    <span class="material-symbols-outlined text-3xl">payments</span>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Total Pembayaran</p>
                    <h2 class="text-xl font-semibold text-gray-800">
                        Rp {{ number_format($totalPaid, 0, ',', '.') }}
                    </h2>
                </div>
            </div>

            {{-- Total Transaksi --}}
            <div class="bg-white rounded-xl shadow p-6 flex items-center">
                <div class="p-3 bg-blue-100 text-blue-600 rounded-full mr-4">
                    <span class="material-symbols-outlined text-3xl">receipt_long</span>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Total Transaksi</p>
                    <h2 class="text-xl font-semibold text-gray-800">{{ $totalTransaction }}</h2>
                </div>
            </div>

            {{-- Pending --}}
            <div class="bg-white rounded-xl shadow p-6 flex items-center">
                <div class="p-3 bg-yellow-100 text-yellow-600 rounded-full mr-4">
                    <span class="material-symbols-outlined text-3xl">hourglass_empty</span>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Pending</p>
                    <h2 class="text-xl font-semibold text-gray-800">{{ $totalPending }}</h2>
                </div>
            </div>

            {{-- Failed --}}
            <div class="bg-white rounded-xl shadow p-6 flex items-center">
                <div class="p-3 bg-red-100 text-red-600 rounded-full mr-4">
                    <span class="material-symbols-outlined text-3xl">error</span>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Gagal</p>
                    <h2 class="text-xl font-semibold text-gray-800">{{ $totalFailed }}</h2>
                </div>
            </div>
        </div>

        {{-- Grafik Pembayaran --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <span class="material-symbols-outlined text-blue-600 mr-2">bar_chart</span>
                Statistik Pembayaran (7 Hari Terakhir)
            </h3>

            <canvas id="paymentChart" height="120"></canvas>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('paymentChart').getContext('2d');
        const paymentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($dailyPayments->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))),
                datasets: [{
                    label: 'Total Pembayaran (Rp)',
                    data: @json($dailyPayments->pluck('total')),
                    borderWidth: 2,
                    borderColor: '#2563EB',
                    backgroundColor: 'rgba(37, 99, 235, 0.2)',
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
