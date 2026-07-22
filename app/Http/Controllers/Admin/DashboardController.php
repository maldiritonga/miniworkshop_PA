<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Pesanan;
use App\Models\Retur;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'today');

        $startDate = match ($filter) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfDay(), // today
        };

        // 1. Total Produk (Katalog unique count & total stock)
        $produkKatalogCount = Produk::where('status_produk', 'aktif')->count();
        $produkTotalStok = Produk::where('status_produk', 'aktif')->sum('stok');

        // 2. Permintaan Retur (diajukan tapi belum diproses admin)
        $totalRetur = Retur::where('status_retur', 'diajukan')->count();

        // 3. Pendapatan (Filtered)
        $pendapatan = Pesanan::where('status_pesanan', 'selesai')
            ->whereDoesntHave('retur', function ($q) {
                $q->where('status_retur', 'selesai');
            })
            ->where('tanggal_pesanan', '>=', $startDate)
            ->sum('total_harga');

        // 4. Total Pesanan (Filtered)
        $pesananQuery = Pesanan::where('tanggal_pesanan', '>=', $startDate);
        $totalPesanan = (clone $pesananQuery)->count();

        // 5. Pesanan Sub-counts (Filtered)
        $pesananSelesai = (clone $pesananQuery)->where('status_pesanan', 'selesai')
            ->whereDoesntHave('retur', function ($q) {
                $q->where('status_retur', 'selesai');
            })
            ->count();
        $pesananDibatalkan = (clone $pesananQuery)->where('status_pesanan', 'dibatalkan')->count();
        $pesananDiproses = (clone $pesananQuery)->whereIn('status_pesanan', ['menunggu_pembayaran', 'dikemas', 'dikirim', 'diantar', 'diretur'])->count();

        $stats = [
            'filter' => $filter,
            'produk_katalog_count' => $produkKatalogCount,
            'produk_total_stok' => $produkTotalStok,
            'total_retur' => $totalRetur,
            'pendapatan' => $pendapatan,
            'total_pesanan' => $totalPesanan,
            'pesanan_selesai' => $pesananSelesai,
            'pesanan_dibatalkan' => $pesananDibatalkan,
            'pesanan_diproses' => $pesananDiproses,
        ];

        $pesananTerbaru = Pesanan::with('user')->latest()->take(5)->get();

        $totalStokGabungan = Produk::aktif()->sum('stok');
        $isTotalStokRendah = $totalStokGabungan < Produk::STOK_BATAS_RENDAH;

        // Chart Data (Dinamis berdasarkan filter, hitung produk terjual)
        if ($filter === 'today') {
            $chartDates = collect(range(23, 0))->map(fn($hours) => now()->subHours($hours)->startOfHour());
            $chartLabels = $chartDates->map(fn($date) => $date->format('H:i'));
            $chartTitle = 'Grafik Produk Terjual (Hari Ini)';
        } elseif ($filter === 'week') {
            $chartDates = collect(range(6, 0))->map(fn($days) => now()->subDays($days)->startOfDay());
            $chartLabels = $chartDates->map(fn($date) => $date->translatedFormat('d M'));
            $chartTitle = 'Grafik Produk Terjual (Minggu Ini)';
        } elseif ($filter === 'month') {
            $chartDates = collect(range(29, 0))->map(fn($days) => now()->subDays($days)->startOfDay());
            $chartLabels = $chartDates->map(fn($date) => $date->translatedFormat('d M'));
            $chartTitle = 'Grafik Produk Terjual (Bulan Ini)';
        } else {
            $chartDates = collect(range(11, 0))->map(fn($months) => now()->subMonths($months)->startOfMonth());
            $chartLabels = $chartDates->map(fn($date) => $date->translatedFormat('M Y'));
            $chartTitle = 'Grafik Produk Terjual (Tahun Ini)';
        }

        $chartData = $chartDates->map(function ($date) use ($filter) {
            $query = \App\Models\DetailPesanan::whereHas('pesanan', function ($q) {
                $q->where('status_pesanan', 'selesai')
                  ->whereDoesntHave('retur', function ($q2) {
                      $q2->where('status_retur', 'selesai');
                  });
            });

            if ($filter === 'today') {
                $end = $date->copy()->endOfHour();
                $query->whereHas('pesanan', fn($q) => $q->whereBetween('tanggal_pesanan', [$date, $end]));
            } elseif ($filter === 'year') {
                $end = $date->copy()->endOfMonth();
                $query->whereHas('pesanan', fn($q) => $q->whereBetween('tanggal_pesanan', [$date, $end]));
            } else {
                $query->whereHas('pesanan', fn($q) => $q->whereDate('tanggal_pesanan', $date));
            }

            return $query->sum('qty');
        });

        return view('admin.dashboard', compact(
            'stats',
            'pesananTerbaru',
            'totalStokGabungan',
            'isTotalStokRendah',
            'chartLabels',
            'chartData',
            'chartTitle'
        ));
    }
}
