<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman Dashboard user / home.
     */
    public function index()
    {
        $user = Auth::user();
        $total_transaksi = Transaction::count();
        $total_nominal = Transaction::sum('amount');
        $total_kategori = Category::count();
        $recent_transactions = Transaction::with('category')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'user',
            'total_transaksi',
            'total_nominal',
            'total_kategori',
            'recent_transactions'
        ));
    }
}
