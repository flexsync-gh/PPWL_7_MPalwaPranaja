<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Daftar transaksi milik user dengan filter dan statistik pribadi.
     */
    public function index(Request $request): View
    {
        return view('transactions.index', $this->buildListingData($request));
    }

    /**
     * Pencarian dan filter lanjutan transaksi milik user.
     */
    public function search(Request $request): View
    {
        return view('transactions.search', $this->buildListingData($request));
    }

    /**
     * Form tambah transaksi baru untuk user yang sedang login.
     */
    public function create(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $categories = $user->categories()->orderBy('type')->orderBy('name')->get();
        $accounts = $user->accounts()->orderBy('name')->get();

        return view('transactions.create', compact('categories', 'accounts'));
    }

    /**
     * Simpan transaksi baru dan sinkronkan saldo akun terkait secara atomik.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $validated = $this->validateTransaction($request, $user->id);
        $normalizedType = strtolower($validated['type']);

        DB::transaction(function () use ($user, $validated, $normalizedType): void {
            $account = Account::where('id', $validated['accountid'])
                ->where('userid', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            Transaction::create([
                'userid' => $user->id,
                'accountid' => $account->id,
                'categoryid' => $validated['categoryid'],
                'amount' => $validated['amount'],
                'description' => $validated['description'],
                'type' => $normalizedType,
                'transaction_date' => $validated['transaction_date'],
            ]);

            $this->applyBalanceDelta($account, $normalizedType, (float) $validated['amount']);
        });

        return redirect('/transactions')->with('pesan', 'Data Transaksi Berhasil Disimpan');
    }

    /**
     * Tampilkan detail satu transaksi milik user.
     */
    public function show(int $id): View
    {
        $transaction = Transaction::with(['account', 'category'])
            ->where('userid', Auth::id())
            ->findOrFail($id);

        return view('transactions.show', compact('transaction'));
    }

    /**
     * Form edit transaksi milik user.
     */
    public function edit(int $id): View
    {
        /** @var User $user */
        $user = Auth::user();
        $transaction = $user->transactions()->findOrFail($id);
        $categories = $user->categories()->orderBy('type')->orderBy('name')->get();
        $accounts = $user->accounts()->orderBy('name')->get();

        return view('transactions.edit', compact('transaction', 'categories', 'accounts'));
    }

    /**
     * Perbarui transaksi milik user dan sesuaikan saldo akun lama/baru secara atomik.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $transaction = $user->transactions()->findOrFail($id);
        $validated = $this->validateTransaction($request, $user->id);
        $newType = strtolower($validated['type']);

        DB::transaction(function () use ($user, $transaction, $validated, $newType): void {
            $oldAccount = Account::where('id', $transaction->accountid)
                ->where('userid', $user->id)
                ->lockForUpdate()
                ->first();

            if ($oldAccount) {
                $this->reverseBalanceDelta($oldAccount, strtolower((string) $transaction->type), (float) $transaction->amount);
            }

            $newAccount = Account::where('id', $validated['accountid'])
                ->where('userid', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            $transaction->update([
                'accountid' => $newAccount->id,
                'categoryid' => $validated['categoryid'],
                'amount' => $validated['amount'],
                'description' => $validated['description'],
                'type' => $newType,
                'transaction_date' => $validated['transaction_date'],
            ]);

            $this->applyBalanceDelta($newAccount, $newType, (float) $validated['amount']);
        });

        return redirect('/transactions')->with('pesan', 'Data Transaksi Berhasil Diupdate');
    }

    /**
     * Hapus transaksi milik user dan kembalikan saldo akun terkait.
     */
    public function destroy(int $id): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $transaction = $user->transactions()->findOrFail($id);

        DB::transaction(function () use ($user, $transaction): void {
            $account = Account::where('id', $transaction->accountid)
                ->where('userid', $user->id)
                ->lockForUpdate()
                ->first();

            if ($account) {
                $this->reverseBalanceDelta($account, strtolower((string) $transaction->type), (float) $transaction->amount);
            }

            $transaction->delete();
        });

        return redirect('/transactions')->with('pesan', 'Data Transaksi Berhasil Dihapus');
    }

    /**
     * Bangun query terfilter, paginasi, dan statistik transaksi untuk user yang login.
     *
     * @return array<string, mixed>
     */
    private function buildListingData(Request $request): array
    {
        /** @var User $user */
        $user = Auth::user();
        $batas = 5;
        $cari = trim((string) ($request->input('kata') ?? $request->input('cari') ?? $request->input('q') ?? ''));

        $categories = $user->categories()->orderBy('name')->get();
        $accounts = $user->accounts()->orderBy('name')->get();
        $query = $user->transactions()->with(['category', 'account']);

        if ($cari !== '') {
            $query->where('description', 'like', '%'.$cari.'%');
        }

        if ($request->filled('account_id') && (int) $request->input('account_id') > 0) {
            $query->where('accountid', (int) $request->input('account_id'));
        }

        if ($request->filled('category_id') && $request->input('category_id') !== 'semua' && (int) $request->input('category_id') > 0) {
            $query->where('categoryid', (int) $request->input('category_id'));
        }

        $typeFilter = strtolower((string) $request->input('type', ''));
        if (in_array($typeFilter, ['income', 'expense'], true)) {
            $query->whereRaw('LOWER(type) = ?', [$typeFilter]);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', (string) $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', (string) $request->input('date_to'));
        }

        $transactions = $query
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($batas)
            ->withQueryString();

        $no = $batas * ($transactions->currentPage() - 1);

        $baseUserQuery = $user->transactions();
        $total_transaksi = (clone $baseUserQuery)->count();
        $total_nominal = (float) (clone $baseUserQuery)->sum('amount');
        $nominal_tertinggi = (float) ((clone $baseUserQuery)->max('amount') ?? 0);
        $nominal_terendah = (float) ((clone $baseUserQuery)->min('amount') ?? 0);
        $total_income = (float) (clone $baseUserQuery)->whereRaw('LOWER(type) = ?', ['income'])->sum('amount');
        $total_expense = (float) (clone $baseUserQuery)->whereRaw('LOWER(type) = ?', ['expense'])->sum('amount');
        $saldo_bersih = $total_income - $total_expense;

        return compact(
            'transactions',
            'categories',
            'accounts',
            'no',
            'cari',
            'total_transaksi',
            'total_nominal',
            'nominal_tertinggi',
            'nominal_terendah',
            'total_income',
            'total_expense',
            'saldo_bersih'
        );
    }

    /**
     * Validasi input transaksi dengan pesan Bahasa Indonesia dan verifikasi kepemilikan akun/kategori.
     *
     * @return array<string, mixed>
     */
    private function validateTransaction(Request $request, int $userId): array
    {
        return $request->validate([
            'transaction_date' => ['required', 'date'],
            'categoryid' => [
                'required',
                Rule::exists('categories', 'id')->where('userid', $userId),
            ],
            'accountid' => [
                'required',
                Rule::exists('accounts', 'id')->where('userid', $userId),
            ],
            'type' => ['required', Rule::in(['income', 'expense', 'Income', 'Expense'])],
            'amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['required', 'string', 'max:255'],
        ], [
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date' => 'Format tanggal transaksi tidak valid.',
            'categoryid.required' => 'Kategori wajib dipilih.',
            'categoryid.exists' => 'Kategori yang dipilih tidak valid untuk akun Anda.',
            'accountid.required' => 'Akun wajib dipilih.',
            'accountid.exists' => 'Akun yang dipilih tidak valid untuk akun Anda.',
            'type.required' => 'Tipe transaksi wajib dipilih.',
            'type.in' => 'Tipe transaksi harus Income atau Expense.',
            'amount.required' => 'Nominal wajib diisi.',
            'amount.numeric' => 'Nominal harus berupa angka.',
            'amount.gt' => 'Nominal harus lebih besar dari 0.',
            'description.required' => 'Deskripsi transaksi wajib diisi.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi maksimal 255 karakter.',
        ]);
    }

    private function applyBalanceDelta(Account $account, string $type, float $amount): void
    {
        $delta = $type === 'income' ? $amount : -$amount;
        $account->balance = (float) $account->balance + $delta;
        $account->save();
    }

    private function reverseBalanceDelta(Account $account, string $type, float $amount): void
    {
        $delta = $type === 'income' ? -$amount : $amount;
        $account->balance = (float) $account->balance + $delta;
        $account->save();
    }
}
