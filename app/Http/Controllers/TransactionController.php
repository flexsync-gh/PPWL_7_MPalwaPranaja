<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Transaction::with('category');

        // Search deskripsi transaksi
        if ($request->filled('cari')) {
            $query->where('description', 'like', '%' . $request->cari . '%');
        }

        // Filter berdasarkan kategori transaksi
        if ($request->filled('category_id') && $request->category_id != 'semua') {
            $query->where('categoryid', $request->category_id);
        }

        // 5 Transaksi terbaru (no filter)
        if (!$request->filled('cari') && (!$request->filled('category_id') || $request->category_id == 'semua')) {
            $transactions = $query->orderBy('id', 'desc')->take(5)->get();
        } else {
            $transactions = $query->orderBy('id', 'desc')->get();
        }

        // Stats transaksi
        $total_transaksi = Transaction::count();
        $total_nominal = Transaction::sum('amount');
        $nominal_tertinggi = Transaction::max('amount');
        $nominal_terendah = Transaction::min('amount');

        return view('transactions.index', compact(
            'transactions',
            'categories',
            'total_transaksi',
            'total_nominal',
            'nominal_tertinggi',
            'nominal_terendah'
        ));
    }

    // Create 
    public function create()
    {
        $categories = Category::all();
        $accounts = Account::all();
        return view('transactions.create', compact('categories', 'accounts'));
    }

    // Store
    public function store(Request $request)
    {
        $transaction = new Transaction();
        $transaction->userid = $request->userid ?? User::first()?->id ?? 1;
        $transaction->accountid = $request->accountid ?? Account::first()?->id ?? 1;
        $transaction->categoryid = $request->categoryid;
        $transaction->amount = $request->amount;
        $transaction->description = $request->description;
        $transaction->type = $request->type;
        $transaction->transaction_date = $request->transaction_date;
        $transaction->save();

        return redirect('/transactions');
    }

    // Update (Edit)
    public function edit($id)
    {
        $transaction = Transaction::find($id);
        $categories = Category::all();
        $accounts = Account::all();
        return view('transactions.edit', compact('transaction', 'categories', 'accounts'));
    }

    // Proses Update
    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);
        if ($request->filled('categoryid')) {
            $transaction->categoryid = $request->categoryid;
        }
        if ($request->filled('accountid')) {
            $transaction->accountid = $request->accountid;
        }
        $transaction->amount = $request->amount;
        $transaction->description = $request->description;
        $transaction->type = $request->type;
        $transaction->transaction_date = $request->transaction_date;
        $transaction->save();

        return redirect('/transactions');
    }

    // Proses Delete Transaksi
    public function destroy($id)
    {
        $transaction = Transaction::find($id);
        $transaction->delete();

        return redirect('/transactions');
    }
}