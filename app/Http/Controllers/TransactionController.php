<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    //
    public function index()
    {
        $transactions = Transaction::all();
        return view('espay.transactions.index', compact('transactions'));
    }

    public function show($id)
    {
        $trx = Transaction::findOrFail($id);
        return view('espay.transactions.show', compact('trx'));
    }
}
