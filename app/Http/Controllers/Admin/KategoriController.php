<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** FR-02 — CRUD kategori. */
class KategoriController extends Controller
{
    public function index(): View
    {
        $kategoris = Kategori::withCount('bukus')->orderBy('nama')->paginate(15);

        return view('admin.kategori', compact('kategoris'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:80', 'unique:kategoris,nama'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        Kategori::create($data);

        return back()->with('sukses', 'Kategori ditambahkan.');
    }

    public function update(Request $request, Kategori $kategori): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:80', Rule::unique('kategoris', 'nama')->ignore($kategori->id)],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $kategori->update($data + ['slug' => null]);

        return back()->with('sukses', 'Kategori diperbarui.');
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        if ($kategori->bukus()->exists()) {
            return back()->with('gagal', 'Kategori masih dipakai oleh '.$kategori->bukus()->count().' buku. Pindahkan buku tersebut dulu.');
        }

        $kategori->delete();

        return back()->with('sukses', 'Kategori dihapus.');
    }
}
