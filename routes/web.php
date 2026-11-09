<?php

use App\Http\Controllers\BukuController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Pengaturan Rute Aplikasi Web)
|--------------------------------------------------------------------------
| File ini mengatur seluruh alur navigasi URL aplikasi Laravel. Setiap rute mengarahkan
| permintaan HTTP dari browser ke Controller dan metode yang sesuai.
|
*/

// =========================================================================
// 1. RUTE AUTENTIKASI (LOGIN, REGISTER, FORGOT PASSWORD, LOGOUT)
// =========================================================================
/*
 * Satu baris perintah `Auth::routes()` mendaftarkan seluruh endpoint/alamat URL
 * autentikasi bawaan Laravel (login, register, logout, reset password) secara otomatis.
 *
 * Pada konfigurasi di bawah, opsional `'register' => false` dan `'reset' => false` diterapkan.
 * Pengaturan ini secara eksplisit menonaktifkan pembuatan rute pendaftaran akun baru dan lupa password.
 *
 * Alasan Keamanan & Hasil Pengujian Praktikum:
 * Langkah ini sangat krusial karena jika fitur pendaftaran hanya disembunyikan tombolnya di tampilan (UI),
 * pengguna luar masih bisa mengakses formulir registrasi dengan mengetikkan URL `/register` secara langsung di browser.
 *
 * Melalui pengujian pembuktian langsung di browser dengan mengakses alamat `http://localhost:8000/register`,
 * sistem mengembalikan kode respons **404 Not Found**. Ini membuktikan bahwa rute registrasi benar-benar tidak dibuat
 * di tingkat arsitektur sistem (back-end), sehingga keamanan akses aplikasi terjamin sepenuhnya.
 */
Auth::routes([
    'register' => false,
    'reset'    => false,
]);

// =========================================================================
// 2. RUTE NAVIGASI UTAMA & REDIRECT
// =========================================================================
// Jika pengguna mengakses URL utama (http://localhost:8000/),
// sistem akan otomatis mengalihkan (redirect) ke rute '/home'.
Route::redirect('/', '/home');

// =========================================================================
// 3. RUTE TERPROTEKSI (WAJIB LOGIN / AUTHENTICATED)
// =========================================================================
// Group middleware 'auth' memastikan pengguna harus login terlebih dahulu.
// Jika belum login, Laravel secara otomatis mengarahkan pengguna ke halaman '/login'.
Route::middleware('auth')->group(function () {

    /**
     * Dashboard Utama
     * Mengarahkan ke HomeController metode index untuk menampilkan halaman utama setelah login.
     */
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // ---------------------------------------------------------------------
    // CRUD DATA BUKU (BukuController)
    // ---------------------------------------------------------------------

    /**
     * Satu baris Route::resource() membuat 6 rute CRUD buku sekaligus
     * (menggantikan 6 rute buku yang sebelumnya ditulis satu per satu):
     *
     * GET     /buku              -> index    (buku.index)    : daftar buku
     * GET     /buku/create       -> create   (buku.create)   : formulir tambah
     * POST    /buku              -> store    (buku.store)    : simpan data + upload sampul
     * GET     /buku/{buku}/edit  -> edit     (buku.edit)     : formulir ubah
     * PUT     /buku/{buku}       -> update   (buku.update)   : simpan perubahan
     * DELETE  /buku/{buku}       -> destroy  (buku.destroy)  : hapus data + berkas sampul
     *
     * except('show') dipakai karena halaman detail buku tidak dibutuhkan.
     * Pembatasan admin dipasang di constructor BukuController (auth + admin kecuali index).
     */
    Route::resource('buku', BukuController::class)->except('show');

    // ---------------------------------------------------------------------
    // CRUD DATA USER (UserController)
    // ---------------------------------------------------------------------

    /**
     * Menampilkan daftar seluruh data pengguna.
     * HTTP Method : GET
     * URL         : /users
     */
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    /**
     * Menampilkan formulir tambah pengguna baru.
     * HTTP Method : GET
     * URL         : /users/create
     */
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');

    /**
     * Menyimpan data pengguna baru ke database.
     * HTTP Method : POST
     * URL         : /users
     */
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    /**
     * Menampilkan formulir edit data pengguna berdasarkan ID.
     * HTTP Method : GET
     * URL         : /users/{id}/edit
     */
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');

    /**
     * Memperbarui data pengguna di database berdasarkan ID.
     * HTTP Method : PUT
     * URL         : /users/{id}
     */
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');

    /**
     * Menghapus data pengguna dari database berdasarkan ID.
     * HTTP Method : DELETE
     * URL         : /users/{id}
     */
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
});