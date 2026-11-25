<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman katalog buku dengan fitur pencarian, filter, sorting, dan paginasi.
     */
    public function index(Request $request)
    {
        $query = Buku::query();
        // TODO 3: Lengkapi logika index():
        // 1. Buat query pencarian berdasarkan judul, penulis, atau penerbit jika ada input 'search'
        $query->when($request->input('search'), function ($query) use ($request) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%")
                    ->orWhere('penerbit', 'like', "%{$search}%");
            });
        });
        // 2. Tambahkan filter penerbit dan filter ketersediaan stok jika ada input 'penerbit' / 'status'
        $query->when($request->input('penerbit'), function ($query) use ($request) {
            $query->where('penerbit', $request->input('penerbit'));
        });
        // 3. Tambahkan pengurutan data berdasarkan input 'sort' (judul, tahun, stok, default: latest)
        $query->when($request->input('sort'), function ($query) use ($request) {
            $sort = $request->input('sort');
            switch ($sort) {
                case 'judul':
                    $query->orderBy('judul');
                    break;
                case 'tahun':
                    $query->orderBy('tahun_terbit');
                    break;
                case 'stok':
                    $query->orderBy('stok');
                    break;
                default:
                    $query->latest();
            }
        });
        // 4. Lakukan paginasi 8 data dengan withQueryString()
        $buku = $query->paginate(8)->withQueryString();
        // 5. Ambil list penerbit unik untuk opsi dropdown filter
        $listPenerbit = Buku::select('penerbit')
        ->distinct()
        ->pluck('penerbit');
        // 6. Return view 'home' dengan data buku dan list penerbit
        return view('home', compact('buku', 'listPenerbit'));
    
    }

    /**
     * Menampilkan halaman detail informasi sebuah buku untuk publik/mahasiswa.
     */
    public function show(Buku $buku)
    {
        // TODO 3: Kembalikan view 'home_show' membawa data $buku
        return view('home_show', compact('buku'));
    }
}
