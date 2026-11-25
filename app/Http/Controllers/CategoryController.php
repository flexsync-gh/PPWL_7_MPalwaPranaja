<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $categories = $user->categories()
            ->withCount('transactions')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $categoryCount = $categories->count();
        $limit = Category::MAX_PER_USER;

        return view('categories.index', compact('categories', 'categoryCount', 'limit'));
    }

    public function create(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $categoryCount = $user->categories()->count();
        $limit = Category::MAX_PER_USER;
        $types = Category::TYPES;

        return view('categories.create', compact('categoryCount', 'limit', 'types'));
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->categories()->count() >= Category::MAX_PER_USER) {
            return redirect()->route('categories.index')
                ->with('error', 'Anda telah mencapai batas maksimal '.Category::MAX_PER_USER.' kategori.');
        }

        $validated = $this->validateCategory($request, $user->id);

        $user->categories()->create([
            'name' => trim($validated['name']),
            'type' => strtolower($validated['type']),
            'category_seo' => Str::slug(trim($validated['name'])),
        ]);

        return redirect()->route('categories.index')->with('pesan', 'Kategori berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        /** @var User $user */
        $user = Auth::user();
        $category = $user->categories()->findOrFail($id);
        $types = Category::TYPES;

        return view('categories.edit', compact('category', 'types'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $category = $user->categories()->findOrFail($id);

        $validated = $this->validateCategory($request, $user->id, $category->id);

        $category->update([
            'name' => trim($validated['name']),
            'type' => strtolower($validated['type']),
            'category_seo' => Str::slug(trim($validated['name'])),
        ]);

        return redirect()->route('categories.index')->with('pesan', 'Kategori berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $category = $user->categories()->findOrFail($id);

        if ($category->transactions()->exists() || $category->budgets()->exists()) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori ini masih digunakan pada transaksi atau budget sehingga tidak dapat dihapus.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('pesan', 'Kategori berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateCategory(Request $request, int $userId, ?int $ignoreId = null): array
    {
        $type = strtolower((string) $request->input('type', 'expense'));
        $uniqueRule = Rule::unique('categories', 'name')
            ->where('userid', $userId)
            ->where('type', $type);

        if ($ignoreId !== null) {
            $uniqueRule->ignore($ignoreId);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:100', $uniqueRule],
            'type' => ['required', Rule::in(array_keys(Category::TYPES))],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.max' => 'Nama kategori maksimal 100 karakter.',
            'name.unique' => 'Kategori dengan nama dan tipe tersebut sudah ada.',
            'type.required' => 'Tipe kategori wajib dipilih.',
            'type.in' => 'Tipe kategori harus income atau expense.',
        ]);
    }
}
