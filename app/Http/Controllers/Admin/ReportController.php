<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('filter_applied')) {
            return redirect()->route('admin.laporan.index', [
                'start_date' => $request->start_date,
                'end_date'   => $request->end_date,
                'tipe'       => $request->tipe,
            ])->with('success', 'Filter laporan berhasil diterapkan.');
        }

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->get('end_date', Carbon::now()->toDateString());
        $tipe      = $request->get('tipe', 'semua');

        $query = Pesanan::with(['user', 'pembayaran', 'detail.produk'])
            ->where('status_pesanan', 'selesai')
            ->whereDoesntHave('retur', fn($q) => $q->where('status_retur', 'selesai'))
            ->whereBetween('tanggal_pesanan', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($tipe !== 'semua') {
            $query->where('tipe_pesanan', $tipe);
        }

        $pesanan = $query->latest()->get();

        $totalPendapatan = $pesanan->sum('total_harga');
        $totalOnline     = $pesanan->where('tipe_pesanan', 'online')->sum('total_harga');
        $totalOffline    = $pesanan->where('tipe_pesanan', 'offline')->sum('total_harga');

        // Harian
        $hQuery = DB::table('pesanan')
            ->whereNotExists(fn($q) => $q->select(DB::raw(1))->from('retur')
                ->whereColumn('retur.id_pesanan', 'pesanan.id_pesanan')
                ->where('retur.status_retur', 'selesai'))
            ->where('pesanan.status_pesanan', 'selesai')
            ->whereBetween('pesanan.tanggal_pesanan', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        if ($tipe !== 'semua') $hQuery->where('tipe_pesanan', $tipe);

        $ringkasanHarian = $hQuery
            ->select(
                DB::raw('DATE(tanggal_pesanan) as date'),
                DB::raw('SUM(total_harga) as total'),
                DB::raw('COUNT(DISTINCT id_pesanan) as count')
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Mingguan
        $ringkasanMingguan = $pesanan->groupBy(function ($item) {
            $d = Carbon::parse($item->tanggal_pesanan);
            return 'Minggu ke-' . $d->weekOfMonth . ', ' . $d->translatedFormat('F Y');
        })->map(fn($g) => ['total' => $g->sum('total_harga'), 'count' => $g->count()]);

        // Bulanan
        $ringkasanBulanan = $pesanan->groupBy(function ($item) {
            return Carbon::parse($item->tanggal_pesanan)->translatedFormat('F Y');
        })->map(fn($g) => ['total' => $g->sum('total_harga'), 'count' => $g->count()]);

        return view('admin.laporan.index', compact(
            'pesanan', 'startDate', 'endDate', 'tipe',
            'totalPendapatan', 'totalOnline', 'totalOffline',
            'ringkasanHarian', 'ringkasanMingguan', 'ringkasanBulanan'
        ));
    }
}
