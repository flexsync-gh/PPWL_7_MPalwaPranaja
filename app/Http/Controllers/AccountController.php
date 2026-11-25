<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $accounts = $user->accounts()
            ->withCount('transactions')
            ->orderBy('name')
            ->get();

        $totalBalance = (float) $accounts->sum('balance');
        $accountCount = $accounts->count();
        $limit = Account::MAX_PER_USER;

        return view('accounts.index', compact('accounts', 'totalBalance', 'accountCount', 'limit'));
    }

    public function create(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $accountCount = $user->accounts()->count();
        $limit = Account::MAX_PER_USER;
        $types = Account::TYPES;

        return view('accounts.create', compact('accountCount', 'limit', 'types'));
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->accounts()->count() >= Account::MAX_PER_USER) {
            return redirect()->route('accounts.index')
                ->with('error', 'Anda telah mencapai batas maksimal '.Account::MAX_PER_USER.' akun keuangan.');
        }

        $validated = $this->validateAccount($request, $user->id);

        $user->accounts()->create([
            'name' => trim($validated['name']),
            'type' => strtolower($validated['type']),
            'balance' => $validated['balance'],
        ]);

        return redirect()->route('accounts.index')->with('pesan', 'Akun keuangan berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        /** @var User $user */
        $user = Auth::user();
        $account = $user->accounts()->findOrFail($id);
        $types = Account::TYPES;

        return view('accounts.edit', compact('account', 'types'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $account = $user->accounts()->findOrFail($id);

        $validated = $this->validateAccount($request, $user->id, $account->id);

        $account->update([
            'name' => trim($validated['name']),
            'type' => strtolower($validated['type']),
            'balance' => $validated['balance'],
        ]);

        return redirect()->route('accounts.index')->with('pesan', 'Akun keuangan berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $account = $user->accounts()->findOrFail($id);

        if ($account->transactions()->exists()) {
            return redirect()->route('accounts.index')
                ->with('error', 'Akun ini sudah digunakan pada transaksi sehingga tidak dapat dihapus.');
        }

        $account->delete();

        return redirect()->route('accounts.index')->with('pesan', 'Akun keuangan berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateAccount(Request $request, int $userId, ?int $ignoreId = null): array
    {
        $uniqueRule = Rule::unique('accounts', 'name')->where('userid', $userId);
        if ($ignoreId !== null) {
            $uniqueRule->ignore($ignoreId);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:100', $uniqueRule],
            'type' => ['required', Rule::in(array_keys(Account::TYPES))],
            'balance' => ['required', 'numeric', 'min:0'],
        ], [
            'name.required' => 'Nama akun wajib diisi.',
            'name.max' => 'Nama akun maksimal 100 karakter.',
            'name.unique' => 'Anda sudah memiliki akun dengan nama tersebut.',
            'type.required' => 'Tipe akun wajib dipilih.',
            'type.in' => 'Tipe akun harus Tunai, Bank, atau E-Wallet.',
            'balance.required' => 'Saldo akun wajib diisi.',
            'balance.numeric' => 'Saldo akun harus berupa angka.',
            'balance.min' => 'Saldo akun minimal 0.',
        ]);
    }
}
