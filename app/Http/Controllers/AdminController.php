<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Tampilkan halaman Admin Dashboard (Step 6 & Latihan 2 MIDDLEWARE_RBAC.pdf).
     */
    public function index()
    {
        $users = User::orderBy('id', 'desc')->get();
        $total_users = User::count();
        $total_admins = User::where('role', 'admin')->count();
        $total_reg_users = User::where('role', 'user')->count();

        $total_transactions = Transaction::count();
        $total_nominal = Transaction::sum('amount');
        $nominal_tertinggi = Transaction::max('amount');
        $nominal_terendah = Transaction::min('amount');

        return view('admin.index', compact(
            'users',
            'total_users',
            'total_admins',
            'total_reg_users',
            'total_transactions',
            'total_nominal',
            'nominal_tertinggi',
            'nominal_terendah'
        ));
    }

    /**
     * Mengubah role user (Fitur pembantu untuk mempermudah pengujian RBAC).
     */
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,user',
        ]);

        $user = User::findOrFail($id);

        // Mencegah admin mengubah role diri sendiri menjadi user jika sedang login
        if ($user->id === Auth::id() && $request->role !== 'admin') {
            return back()->with('error', 'Anda tidak dapat menurunkan role akun admin Anda sendiri saat sedang login.');
        }

        $user->role = $request->role;
        $user->save();

        return back()->with('pesan', "Role user {$user->name} berhasil diubah menjadi {$user->role}!");
    }
}
