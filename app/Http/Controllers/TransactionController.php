<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Table;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('table')->latest()->get();
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $tables = Table::all(); // Ambil daftar meja
        return view('transactions.create', compact('tables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'total_price' => 'required|numeric|min:0',
        ]);

        Transaction::create([
            'table_id' => $request->table_id,
            'total_price' => $request->total_price,
            'payment_status' => 'pending',
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function show(Transaction $transaction)
    {
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $tables = Table::all();
        return view('transactions.edit', compact('transaction', 'tables'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'total_price' => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,paid',
        ]);

        $transaction->update($request->all());

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
