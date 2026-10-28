<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // pagination transaksi
    public function index(Request $request)
    {
        $batas = 5;
        $categories = Category::all();
        $query = Transaction::with('category');

        // filter kategori
        if ($request->filled('category_id') && $request->category_id != 'semua') {
            $query->where('categoryid', $request->category_id);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate($batas)->withQueryString();
        $no = $batas * ($transactions->currentPage() - 1);

        // stats transaksi
        $total_transaksi = Transaction::count();
        $total_nominal = Transaction::sum('amount');
        $nominal_tertinggi = Transaction::max('amount');
        $nominal_terendah = Transaction::min('amount');

        return view('transactions.index', compact(
            'transactions',
            'categories',
            'no',
            'total_transaksi',
            'total_nominal',
            'nominal_tertinggi',
            'nominal_terendah'
        ));
    }

    // fitur searching
    public function search(Request $request)
    {
        $batas = 5;
        $cari = $request->kata ?? $request->cari;
        $categories = Category::all()->sortBy('name');
        $query = Transaction::with('category');

        if (!empty($cari)) {
            $query->where('description', 'like', "%" . $cari . "%");
        }

        // filter kategori
        if ($request->filled('category_id') && $request->category_id != 'semua') {
            $query->where('categoryid', $request->category_id);
        }

        $transactions = $query->orderBy('id', 'desc')->paginate($batas)->withQueryString();
        $no = $batas * ($transactions->currentPage() - 1);

        $total_transaksi = Transaction::count();
        $total_nominal = Transaction::sum('amount');
        $nominal_tertinggi = Transaction::max('amount');
        $nominal_terendah = Transaction::min('amount');

        return view('transactions.search', compact(
            'transactions',
            'categories',
            'no',
            'cari',
            'total_transaksi',
            'total_nominal',
            'nominal_tertinggi',
            'nominal_terendah'
        ));
    }

    // create 
    public function create()
    {
        $categories = Category::all();
        $accounts = Account::all();
        return view('transactions.create', compact('categories', 'accounts'));
    }

    // validasi input & flash message
    public function store(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'categoryid'       => 'required',
            'accountid'        => 'required',
            'type'             => 'required',
            'amount'           => 'required|numeric',
            'description'      => 'required|string|max:255',
        ], [
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date'     => 'Format tanggal transaksi tidak valid.',
            'categoryid.required'       => 'Kategori wajib dipilih.',
            'accountid.required'        => 'Akun wajib dipilih.',
            'type.required'             => 'Tipe transaksi wajib dipilih.',
            'amount.required'           => 'Nominal wajib diisi.',
            'amount.numeric'            => 'Nominal harus berupa angka.',
            'description.required'      => 'Deskripsi transaksi wajib diisi.',
            'description.string'        => 'Deskripsi harus berupa teks.',
            'description.max'           => 'Deskripsi maksimal 255 karakter.',
        ]);

        $transaction = new Transaction();
        $transaction->userid = $request->userid ?? User::first()?->id ?? 1;
        $transaction->accountid = $request->accountid ?? Account::first()?->id ?? 1;
        $transaction->categoryid = $request->categoryid;
        $transaction->amount = $request->amount;
        $transaction->description = $request->description;
        $transaction->type = $request->type;
        $transaction->transaction_date = $request->transaction_date;
        $transaction->save();

        return redirect('/transactions')->with('pesan', 'Data Transaksi Berhasil Disimpan');
    }

    // update
    public function edit($id)
    {
        $transaction = Transaction::find($id);
        $categories = Category::all();
        $accounts = Account::all();
        return view('transactions.edit', compact('transaction', 'categories', 'accounts'));
    }

    // flash message
    public function update(Request $request, $id)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'categoryid'       => 'required',
            'accountid'        => 'required',
            'type'             => 'required',
            'amount'           => 'required|numeric',
            'description'      => 'required|string|max:255',
        ], [
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date'     => 'Format tanggal transaksi tidak valid.',
            'categoryid.required'       => 'Kategori wajib dipilih.',
            'accountid.required'        => 'Akun wajib dipilih.',
            'type.required'             => 'Tipe transaksi wajib dipilih.',
            'amount.required'           => 'Nominal wajib diisi.',
            'amount.numeric'            => 'Nominal harus berupa angka.',
            'description.required'      => 'Deskripsi transaksi wajib diisi.',
            'description.string'        => 'Deskripsi harus berupa teks.',
            'description.max'           => 'Deskripsi maksimal 255 karakter.',
        ]);

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

        return redirect('/transactions')->with('pesan', 'Data Transaksi Berhasil Diupdate');
    }

    // flash message delete
    public function destroy($id)
    {
        $transaction = Transaction::find($id);
        if ($transaction) {
            $transaction->delete();
        }

        return redirect('/transactions')->with('pesan', 'Data Transaksi Berhasil Dihapus');
    }
}