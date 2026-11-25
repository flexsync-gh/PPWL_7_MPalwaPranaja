<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman Dashboard pribadi pengguna dengan ringkasan keuangan, budget, dan tabungan.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $budgetMonth = (int) date('n');
        $budgetYear = (int) date('Y');

        $total_transaksi = $user->transactions()->count();
        $total_nominal = (float) $user->transactions()->sum('amount');
        $total_income = (float) $user->transactions()->whereRaw('LOWER(type) = ?', ['income'])->sum('amount');
        $total_expense = (float) $user->transactions()->whereRaw('LOWER(type) = ?', ['expense'])->sum('amount');
        $saldo_bersih = $total_income - $total_expense;
        $total_balance = (float) $user->accounts()->sum('balance');
        $total_akun = $user->accounts()->count();
        $total_kategori = $user->categories()->count();

        $recent_transactions = $user->transactions()
            ->with(['category', 'account'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $active_goals = $user->savingGoals()->where('status', 'active')->count();
        $total_saved = (float) $user->savingGoals()->sum('current_amount');
        $saving_goals = $user->savingGoals()
            ->orderBy('status')
            ->orderBy('target_date')
            ->take(3)
            ->get();

        $monthlyBudgets = $user->budgets()
            ->with('category')
            ->where('month', $budgetMonth)
            ->where('year', $budgetYear)
            ->get();

        $monthly_budget_total = (float) $monthlyBudgets->sum('amount');
        $monthly_budget_spent = 0.0;

        if ($monthlyBudgets->isNotEmpty()) {
            $budgetCategoryIds = $monthlyBudgets->pluck('categoryid')->all();
            $monthly_budget_spent = (float) $user->transactions()
                ->whereIn('categoryid', $budgetCategoryIds)
                ->whereRaw('LOWER(type) = ?', ['expense'])
                ->whereMonth('transaction_date', $budgetMonth)
                ->whereYear('transaction_date', $budgetYear)
                ->sum('amount');
        }

        $monthly_budget_percent = $monthly_budget_total > 0
            ? min(100.0, ($monthly_budget_spent / $monthly_budget_total) * 100)
            : 0.0;

        return view('dashboard', compact(
            'user',
            'total_transaksi',
            'total_nominal',
            'total_income',
            'total_expense',
            'saldo_bersih',
            'total_balance',
            'total_akun',
            'total_kategori',
            'recent_transactions',
            'active_goals',
            'total_saved',
            'saving_goals',
            'monthly_budget_total',
            'monthly_budget_spent',
            'monthly_budget_percent',
            'budgetMonth',
            'budgetYear'
        ));
    }
}
