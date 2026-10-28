<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
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

    /**
     * Tampilkan halaman Profile (Latihan 1 MIDDLEWARE_RBAC.pdf).
     */
    public function profile()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }
}
