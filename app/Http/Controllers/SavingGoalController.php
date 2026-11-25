<?php

namespace App\Http\Controllers;

use App\Models\SavingGoal;
use App\Models\SavingTransaction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SavingGoalController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $goals = $user->savingGoals()
            ->orderBy('status')
            ->orderBy('target_date')
            ->orderByDesc('id')
            ->get();

        foreach ($goals as $goal) {
            $goal->syncStatus();
        }

        return view('savings.index', compact('goals'));
    }

    public function create(): View
    {
        return view('savings.create');
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $validated = $this->validateGoal($request);

        $targetAmount = (float) $validated['target_amount'];
        $currentAmount = (float) ($validated['current_amount'] ?? 0);
        $status = $currentAmount >= $targetAmount ? 'completed' : 'active';

        $user->savingGoals()->create([
            'name' => trim($validated['name']),
            'target_amount' => $targetAmount,
            'current_amount' => $currentAmount,
            'target_date' => $validated['target_date'],
            'status' => $status,
        ]);

        return redirect()->route('savings.index')->with('pesan', 'Target tabungan berhasil dibuat.');
    }

    public function show(int $saving): View
    {
        /** @var User $user */
        $user = Auth::user();
        $goal = $user->savingGoals()->findOrFail($saving);
        $goal->syncStatus();

        $transactions = $goal->savingTransactions()
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();

        return view('savings.show', compact('goal', 'transactions'));
    }

    public function edit(int $saving): View
    {
        /** @var User $user */
        $user = Auth::user();
        $goal = $user->savingGoals()->findOrFail($saving);

        return view('savings.edit', compact('goal'));
    }

    public function update(Request $request, int $saving): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $goal = $user->savingGoals()->findOrFail($saving);
        $validated = $this->validateGoal($request, true);

        $targetAmount = (float) $validated['target_amount'];
        $currentAmount = (float) $validated['current_amount'];
        $status = $currentAmount >= $targetAmount ? 'completed' : ($validated['status'] ?? 'active');

        $goal->update([
            'name' => trim($validated['name']),
            'target_amount' => $targetAmount,
            'current_amount' => $currentAmount,
            'target_date' => $validated['target_date'],
            'status' => $status,
        ]);

        $goal->syncStatus();

        return redirect()->route('savings.show', $goal->id)->with('pesan', 'Target tabungan berhasil diperbarui.');
    }

    public function destroy(int $saving): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $goal = $user->savingGoals()->findOrFail($saving);
        $goal->delete();

        return redirect()->route('savings.index')->with('pesan', 'Target tabungan berhasil dihapus.');
    }

    public function createTransaction(int $saving): View
    {
        /** @var User $user */
        $user = Auth::user();
        $goal = $user->savingGoals()->findOrFail($saving);

        return view('savings.add-transaction', compact('goal'));
    }

    public function storeTransaction(Request $request, int $saving): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $goal = $user->savingGoals()->findOrFail($saving);

        $validated = $request->validate([
            'type' => ['required', Rule::in(['deposit', 'withdraw'])],
            'amount' => ['required', 'numeric', 'gt:0'],
            'transaction_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'type.required' => 'Tipe transaksi tabungan wajib dipilih.',
            'type.in' => 'Tipe transaksi tabungan harus deposit atau withdraw.',
            'amount.required' => 'Nominal wajib diisi.',
            'amount.numeric' => 'Nominal harus berupa angka.',
            'amount.gt' => 'Nominal harus lebih besar dari 0.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date' => 'Format tanggal transaksi tidak valid.',
        ]);

        $amountValue = (float) $validated['amount'];
        $type = $validated['type'];

        $failedMessage = DB::transaction(function () use ($user, $goal, $amountValue, $type, $validated): ?string {
            $lockedGoal = SavingGoal::where('id', $goal->id)
                ->where('userid', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            $currentAmount = (float) $lockedGoal->current_amount;
            $newAmount = $type === 'deposit' ? $currentAmount + $amountValue : $currentAmount - $amountValue;

            if ($newAmount < 0) {
                return 'Penarikan gagal karena melebihi saldo tabungan yang terkumpul.';
            }

            SavingTransaction::create([
                'saving_goal_id' => $lockedGoal->id,
                'userid' => $user->id,
                'amount' => $amountValue,
                'type' => $type,
                'note' => $validated['note'] ?? null,
                'transaction_date' => $validated['transaction_date'],
            ]);

            $lockedGoal->update([
                'current_amount' => $newAmount,
                'status' => $newAmount >= (float) $lockedGoal->target_amount ? 'completed' : 'active',
            ]);

            return null;
        });

        if ($failedMessage !== null) {
            return back()->withInput()->withErrors(['amount' => $failedMessage]);
        }

        return redirect()->route('savings.show', $goal->id)->with('pesan', 'Transaksi tabungan berhasil dicatat.');
    }

    public function destroyTransaction(int $transaction): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $savingTrx = SavingTransaction::where('id', $transaction)
            ->where('userid', $user->id)
            ->firstOrFail();

        $goalId = $savingTrx->saving_goal_id;

        $errorMessage = DB::transaction(function () use ($user, $savingTrx, $goalId): ?string {
            $lockedGoal = SavingGoal::where('id', $goalId)
                ->where('userid', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            $amount = (float) $savingTrx->amount;
            $currentAmount = (float) $lockedGoal->current_amount;
            $newAmount = $savingTrx->type === 'deposit' ? $currentAmount - $amount : $currentAmount + $amount;

            if ($newAmount < 0) {
                return 'Transaksi ini tidak dapat dihapus karena akan membuat saldo tabungan negatif.';
            }

            $savingTrx->delete();

            $lockedGoal->update([
                'current_amount' => $newAmount,
                'status' => $newAmount >= (float) $lockedGoal->target_amount ? 'completed' : 'active',
            ]);

            return null;
        });

        if ($errorMessage !== null) {
            return redirect()->route('savings.show', $goalId)->with('error', $errorMessage);
        }

        return redirect()->route('savings.show', $goalId)->with('pesan', 'Transaksi tabungan berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateGoal(Request $request, bool $includeStatus = false): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'target_amount' => ['required', 'numeric', 'gt:0'],
            'current_amount' => ['required', 'numeric', 'min:0'],
            'target_date' => ['required', 'date'],
        ];

        if ($includeStatus) {
            $rules['status'] = ['required', Rule::in(['active', 'completed'])];
        }

        return $request->validate($rules, [
            'name.required' => 'Nama target tabungan wajib diisi.',
            'name.max' => 'Nama target tabungan maksimal 100 karakter.',
            'target_amount.required' => 'Target nominal wajib diisi.',
            'target_amount.numeric' => 'Target nominal harus berupa angka.',
            'target_amount.gt' => 'Target nominal harus lebih besar dari 0.',
            'current_amount.required' => 'Saldo awal/saat ini wajib diisi.',
            'current_amount.numeric' => 'Saldo saat ini harus berupa angka.',
            'current_amount.min' => 'Saldo saat ini minimal 0.',
            'target_date.required' => 'Tanggal target wajib diisi.',
            'target_date.date' => 'Format tanggal target tidak valid.',
        ]);
    }
}
