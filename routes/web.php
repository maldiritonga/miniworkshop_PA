<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/produk/{id}', [ProdukController::class, 'show'])->name('produk.show');

Route::middleware('auth')->group(function () {
    Route::get('/keranjang', [CartController::class, 'index'])->name('keranjang.index');
    Route::post('/keranjang/add/{id}', [CartController::class, 'add'])->name('keranjang.add');
    Route::delete('/keranjang/remove/{id}', [CartController::class, 'remove'])->name('keranjang.remove');

    Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/buy-now/{id}', [\App\Http\Controllers\CheckoutController::class, 'buyNow'])->name('buy-now');
    
    // Biteship Shipping Rates API
    Route::post('/checkout/shipping-rates', function(Illuminate\Http\Request $request, App\Services\BiteshipService $biteship) {
        $weight = $request->input('weight', 1000);
        $rates = $biteship->getRates($request->address, $weight);
        return response()->json(['rates' => $rates]);
    })->name('checkout.shipping-rates');
});

Route::get('/', function (Illuminate\Http\Request $request) {
    $now = \Carbon\Carbon::now();
    $barangBaru = \App\Models\Produk::with('kategori')
        ->where('status_produk', 'aktif')
        ->where('created_at', '>=', $now->subDays(7))
        ->latest()
        ->get();

    $katalogQuery = \App\Models\Produk::with('kategori')
        ->where('status_produk', 'aktif');

    if ($request->filled('kategori')) {
        $katalogQuery->where('id_kategori', $request->kategori);
    }

    $katalogProduk = $katalogQuery->latest()->paginate(15);
    $katalogProduk->appends($request->query());

    $kategoriList = \App\Models\Kategori::all();

    return view('pelanggan.dashboard', compact('barangBaru', 'katalogProduk', 'kategoriList'));
})->name('home');

Route::get('/about', function () {
    return view('pelanggan.about');
})->name('about');

Route::middleware('auth')->group(function () {
    Route::get('/pesanan-saya', [App\Http\Controllers\OrderController::class, 'index'])->name('pesanan.saya');
    Route::get('/pesanan-saya/{id}', [App\Http\Controllers\OrderController::class, 'show'])->name('pesanan.show');
    Route::get('/pesanan-saya/{id}/invoice', [App\Http\Controllers\OrderController::class, 'invoice'])->name('pesanan.invoice');
    Route::get('/pesanan-saya/{id}/label-retur/{id_retur}', [App\Http\Controllers\OrderController::class, 'labelRetur'])->name('pesanan.label-retur');
    Route::post('/pesanan-saya/{id}/upload-bukti', [App\Http\Controllers\OrderController::class, 'uploadBukti'])->name('pesanan.upload-bukti');
    Route::post('/pesanan-saya/{id}/batalkan', [App\Http\Controllers\OrderController::class, 'batalkan'])->name('pesanan.batalkan');
    Route::post('/pesanan-saya/{id}/konfirmasi-diterima', [App\Http\Controllers\OrderController::class, 'konfirmasiDiterima'])->name('pesanan.konfirmasi-diterima');
    Route::post('/pesanan-saya/{id}/retur', [App\Http\Controllers\ReturController::class, 'store'])->name('retur.store');
    Route::post('/pesanan-saya/{id}/retur/{id_retur}/rekening', [App\Http\Controllers\ReturController::class, 'kirimRekening'])->name('retur.kirim-rekening');
    Route::post('/pesanan-saya/{id}/retur/{id_retur}/selesai', [App\Http\Controllers\ReturController::class, 'konfirmasiSelesai'])->name('retur.konfirmasi-selesai');
});

Route::get('/dashboard', function (Illuminate\Http\Request $request) {
    $now = \Carbon\Carbon::now();
    $barangBaru = \App\Models\Produk::with('kategori')
        ->where('status_produk', 'aktif')
        ->where('created_at', '>=', $now->subDays(7))
        ->latest()
        ->get();

    $katalogQuery = \App\Models\Produk::with('kategori')
        ->where('status_produk', 'aktif');

    if ($request->filled('kategori')) {
        $katalogQuery->where('id_kategori', $request->kategori);
    }

    $katalogProduk = $katalogQuery->latest()->paginate(15);
    $katalogProduk->appends($request->query());

    $kategoriList = \App\Models\Kategori::all();

    return view('pelanggan.dashboard', compact('barangBaru', 'katalogProduk', 'kategoriList'));
})->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/foto', [ProfileController::class, 'uploadFoto'])->name('profile.foto');
    Route::delete('/profile/foto', [ProfileController::class, 'deleteFoto'])->name('profile.foto.delete');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifikasi
    Route::post('/notifikasi/{id}/baca', function ($id) {
        $notif = Auth::user()->notifications()->findOrFail($id);
        $notif->markAsRead();
        return redirect($notif->data['url'] ?? route('pesanan.saya'));
    })->name('notifikasi.baca');
    Route::post('/notifikasi/baca-semua', function () {
        Auth::user()->unreadNotifications->markAsRead();
        return redirect()->back();
    })->name('notifikasi.baca-semua');

    // Alamat
    Route::post('/alamat', [\App\Http\Controllers\AlamatController::class, 'store'])->name('alamat.store');
    Route::put('/alamat/{id}', [\App\Http\Controllers\AlamatController::class, 'update'])->name('alamat.update');
    Route::delete('/alamat/{id}', [\App\Http\Controllers\AlamatController::class, 'destroy'])->name('alamat.destroy');
    Route::post('/alamat/{id}/utama', [\App\Http\Controllers\AlamatController::class, 'setUtama'])->name('alamat.utama');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('produk', \App\Http\Controllers\Admin\ProdukController::class);
    Route::get('/offline-transaction', [\App\Http\Controllers\Admin\OfflineTransactionController::class, 'index'])->name('offline-transaction.index');
    Route::post('/offline-transaction', [\App\Http\Controllers\Admin\OfflineTransactionController::class, 'store'])->name('offline-transaction.store');
    Route::resource('pesanan', \App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::post('pesanan/{id}/konfirmasi-pembayaran', [\App\Http\Controllers\Admin\OrderController::class, 'konfirmasiPembayaran'])->name('pesanan.konfirmasi-pembayaran');
    Route::post('pesanan/{id}/request-pickup', [\App\Http\Controllers\Admin\OrderController::class, 'requestPickup'])->name('pesanan.request-pickup');
    Route::get('pesanan/{id}/invoice', [\App\Http\Controllers\Admin\OrderController::class, 'invoice'])->name('pesanan.invoice');
    Route::get('pesanan/{id}/label-pengiriman', [\App\Http\Controllers\Admin\OrderController::class, 'labelPengiriman'])->name('pesanan.label-pengiriman');

    // Khusus Admin (tidak bisa diakses kasir)
    Route::middleware('superadmin')->group(function() {
        Route::get('/laporan', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('laporan.index');
        Route::resource('akun-kasir', \App\Http\Controllers\Admin\AkunKasirController::class)->names('akun-kasir');
        Route::get('akun-pelanggan', [\App\Http\Controllers\Admin\AkunPelangganController::class, 'index'])->name('akun-pelanggan.index');
        Route::post('akun-pelanggan/{id}/toggle-block', [\App\Http\Controllers\Admin\AkunPelangganController::class, 'toggleBlock'])->name('akun-pelanggan.toggle-block');
    });

    // Retur - bisa diakses admin dan kasir
    Route::get('/retur', [\App\Http\Controllers\Admin\ReturController::class, 'index'])->name('retur.index');
    Route::get('/retur/{id}', [\App\Http\Controllers\Admin\ReturController::class, 'show'])->name('retur.show');
    Route::post('/retur/{id}/terima', [\App\Http\Controllers\Admin\ReturController::class, 'terima'])->name('retur.terima');
    Route::post('/retur/{id}/tolak', [\App\Http\Controllers\Admin\ReturController::class, 'tolak'])->name('retur.tolak');
    Route::post('/retur/{id}/konfirmasi-barang', [\App\Http\Controllers\Admin\ReturController::class, 'konfirmasiBarang'])->name('retur.konfirmasi-barang');
    Route::post('/retur/{id}/upload-bukti-transfer', [\App\Http\Controllers\Admin\ReturController::class, 'uploadBuktiTransfer'])->name('retur.upload-bukti-transfer');
});

// Google Socialite Routes
Route::get('/auth/google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

require __DIR__.'/auth.php';

// Secure Deploy Helper Routes for Shared Hosting / VPS without SSH
Route::get('/deploy/{action}/{token}', function ($action, $token) {
    $expectedToken = env('DEPLOY_TOKEN');
    
    if (empty($expectedToken) || $token !== $expectedToken) {
        abort(403, 'Unauthorized access. Please set a secure DEPLOY_TOKEN in your .env file.');
    }

    try {
        switch ($action) {
            case 'migrate':
                Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                return 'Migration completed:<br><pre>' . Illuminate\Support\Facades\Artisan::output() . '</pre>';
            case 'optimize':
                Illuminate\Support\Facades\Artisan::call('optimize');
                return 'Optimization completed:<br><pre>' . Illuminate\Support\Facades\Artisan::output() . '</pre>';
            case 'clear':
                Illuminate\Support\Facades\Artisan::call('optimize:clear');
                return 'Cache cleared:<br><pre>' . Illuminate\Support\Facades\Artisan::output() . '</pre>';
            case 'storage-link':
                Illuminate\Support\Facades\Artisan::call('storage:link');
                return 'Storage link completed:<br><pre>' . Illuminate\Support\Facades\Artisan::output() . '</pre>';
            default:
                return 'Action not found. Available actions: migrate, optimize, clear, storage-link';
        }
    } catch (\Exception $e) {
        return 'Error executing action "' . e($action) . '": ' . e($e->getMessage());
    }
});