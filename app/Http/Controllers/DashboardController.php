<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EspayVirtualAccount;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total pembayaran sukses (PAID)
        $totalPaid = Transaction::where('status', 'PAID')->sum('paid_amount');

        // Total transaksi gagal
        $totalFailed = Transaction::where('status', 'FAILED')->count();

        // Total transaksi pending
        $totalPending = Transaction::where('status', 'PENDING')->count();

        // Jumlah transaksi keseluruhan
        $totalTransaction = Transaction::count();

        // Statistik per hari (misal untuk grafik line chart)
        $dailyPayments = Transaction::select(
            DB::raw('DATE(paid_at) as date'),
            DB::raw('SUM(paid_amount) as total')
        )
            ->where('status', 'PAID')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();

        return view('dashboard', compact(
            'totalPaid',
            'totalFailed',
            'totalPending',
            'totalTransaction',
            'dailyPayments'
        ));
    }
}
