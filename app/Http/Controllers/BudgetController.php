<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        $month = (int) $request->input('month', date('n'));
        $year = (int) $request->input('year', date('Y'));

        if ($month < 1 || $month > 12) {
            $month = (int) date('n');
        }

        if ($year < 2000 || $year > 2100) {
            $year = (int) date('Y');
        }

        $budgets = $user->budgets()
            ->with('category')
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->sortBy(fn (Budget $b) => $b->category?->name ?? '')
            ->values();

        $spentByCategory = $user->transactions()
            ->selectRaw('categoryid, SUM(amount) as total_spent')
            ->whereRaw('LOWER(type) = ?', ['expense'])
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->groupBy('categoryid')
            ->pluck('total_spent', 'categoryid');

        $totalBudget = 0.0;
        $totalSpent = 0.0;
        $overBudgetCount = 0;

        $budgetItems = $budgets->map(function (Budget $budget) use ($spentByCategory, &$totalBudget, &$totalSpent, &$overBudgetCount): array {
            $amount = (float) $budget->amount;
            $spent = (float) ($spentByCategory[$budget->categoryid] ?? 0);
            $remaining = $amount - $spent;
            $usage = $amount > 0 ? ($spent / $amount) * 100 : 0.0;

            $totalBudget += $amount;
            $totalSpent += $spent;

            if ($usage >= 100) {
                $overBudgetCount++;
                $status = 'Melebihi Budget';
                $statusClass = 'status-over';
            } elseif ($usage >= 80) {
                $status = 'Hampir Habis';
                $statusClass = 'status-warning';
            } else {
                $status = 'Aman';
                $statusClass = 'status-safe';
            }

            return [
                'budget' => $budget,
                'amount' => $amount,
                'spent' => $spent,
                'remaining' => $remaining,
                'usage' => $usage,
                'barWidth' => min(100.0, $usage),
                'status' => $status,
                'statusClass' => $statusClass,
            ];
        });

        $totalRemaining = $totalBudget - $totalSpent;
        $monthOptions = Budget::monthOptions();

        return view('budgets.index', compact(
            'budgetItems',
            'month',
            'year',
            'monthOptions',
            'totalBudget',
            'totalSpent',
            'totalRemaining',
            'overBudgetCount'
        ));
    }

    public function create(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $categories = $user->categories()
            ->whereRaw('LOWER(type) = ?', ['expense'])
            ->orderBy('name')
            ->get();
        $monthOptions = Budget::monthOptions();
        $defaultMonth = (int) date('n');
        $defaultYear = (int) date('Y');

        return view('budgets.create', compact('categories', 'monthOptions', 'defaultMonth', 'defaultYear'));
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $validated = $this->validateBudget($request, $user->id);

        $user->budgets()->create([
            'categoryid' => $validated['categoryid'],
            'amount' => $validated['amount'],
            'month' => $validated['month'],
            'year' => $validated['year'],
        ]);

        return redirect()->route('budgets.index', [
            'month' => $validated['month'],
            'year' => $validated['year'],
        ])->with('pesan', 'Budget bulanan berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        /** @var User $user */
        $user = Auth::user();
        $budget = $user->budgets()->findOrFail($id);
        $categories = $user->categories()
            ->whereRaw('LOWER(type) = ?', ['expense'])
            ->orderBy('name')
            ->get();
        $monthOptions = Budget::monthOptions();

        return view('budgets.edit', compact('budget', 'categories', 'monthOptions'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $budget = $user->budgets()->findOrFail($id);
        $validated = $this->validateBudget($request, $user->id, $budget->id);

        $budget->update([
            'categoryid' => $validated['categoryid'],
            'amount' => $validated['amount'],
            'month' => $validated['month'],
            'year' => $validated['year'],
        ]);

        return redirect()->route('budgets.index', [
            'month' => $validated['month'],
            'year' => $validated['year'],
        ])->with('pesan', 'Budget bulanan berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $budget = $user->budgets()->findOrFail($id);
        $month = $budget->month;
        $year = $budget->year;

        $budget->delete();

        return redirect()->route('budgets.index', [
            'month' => $month,
            'year' => $year,
        ])->with('pesan', 'Budget bulanan berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateBudget(Request $request, int $userId, ?int $ignoreId = null): array
    {
        $month = (int) $request->input('month');
        $year = (int) $request->input('year');

        $uniquePeriodRule = Rule::unique('budgets', 'categoryid')
            ->where('userid', $userId)
            ->where('month', $month)
            ->where('year', $year);

        if ($ignoreId !== null) {
            $uniquePeriodRule->ignore($ignoreId);
        }

        return $request->validate([
            'categoryid' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where('userid', $userId)
                    ->whereIn('type', ['expense', 'Expense']),
                $uniquePeriodRule,
            ],
            'amount' => ['required', 'numeric', 'gt:0'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2000,2100'],
        ], [
            'categoryid.required' => 'Kategori pengeluaran wajib dipilih.',
            'categoryid.exists' => 'Pilih kategori pengeluaran yang valid milik Anda.',
            'categoryid.unique' => 'Budget untuk kategori, bulan, dan tahun tersebut sudah ada.',
            'amount.required' => 'Nominal budget wajib diisi.',
            'amount.numeric' => 'Nominal budget harus berupa angka.',
            'amount.gt' => 'Nominal budget harus lebih besar dari 0.',
            'month.required' => 'Bulan wajib dipilih.',
            'month.between' => 'Bulan harus antara 1 sampai 12.',
            'year.required' => 'Tahun wajib diisi.',
            'year.between' => 'Tahun harus antara 2000 sampai 2100.',
        ]);
    }
}
