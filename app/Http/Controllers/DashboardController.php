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
        return view('dashboard', [
            'totalPaid' => $this->getTotalPaid(),
            'totalFailed' => $this->getTotalFailed(),
            'totalPending' => $this->getTotalPending(),
            'totalTransaction' => $this->getTotalTransactions(),
            'dailyPayments' => $this->getDailyPayments(),
        ]);
    }

    private function getTotalPaid()
    {
        return Transaction::where('status', 'PAID')->sum('paid_amount');
    }

    private function getTotalFailed()
    {
        return Transaction::where('status', 'FAILED')->count();
    }

    private function getTotalPending()
    {
        return Transaction::where('status', 'PENDING')->count();
    }

    private function getTotalTransactions()
    {
        return Transaction::count();
    }

    private function getDailyPayments()
    {
        return Transaction::select(
            DB::raw('DATE(paid_at) as date'),
            DB::raw('SUM(paid_amount) as total')
        )
            ->where('status', 'PAID')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();
    }
}

