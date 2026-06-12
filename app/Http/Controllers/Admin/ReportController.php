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
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $pesanan = Pesanan::with(['user', 'pembayaran', 'detail'])
            ->where('status_pesanan', 'selesai')
            ->whereDoesntHave('retur', function ($q) {
                $q->where('status_retur', '!=', 'ditolak');
            })
            ->whereBetween('tanggal_pesanan', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->latest()
            ->get();

        $totalPendapatan = $pesanan->sum(function ($p) {
            return $p->detail->sum(function ($d) { return $d->harga * $d->qty; });
        });
        $totalOnline = $pesanan->where('tipe_pesanan', 'online')->sum(function ($p) {
            return $p->detail->sum(function ($d) { return $d->harga * $d->qty; });
        });
        $totalOffline = $pesanan->where('tipe_pesanan', 'offline')->sum(function ($p) {
            return $p->detail->sum(function ($d) { return $d->harga * $d->qty; });
        });

        $ringkasanHarian = DB::table('pesanan')
            ->join('detail_pesanan', 'pesanan.id_pesanan', '=', 'detail_pesanan.id_pesanan')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('retur')
                      ->whereColumn('retur.id_pesanan', 'pesanan.id_pesanan')
                      ->where('retur.status_retur', '!=', 'ditolak');
            })
            ->select(
                DB::raw('DATE(pesanan.tanggal_pesanan) as date'),
                DB::raw('SUM(detail_pesanan.harga * detail_pesanan.qty) as total'),
                DB::raw('COUNT(DISTINCT pesanan.id_pesanan) as count')
            )
            ->where('pesanan.status_pesanan', 'selesai')
            ->whereBetween('pesanan.tanggal_pesanan', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return view('admin.laporan.index', compact(
            'pesanan', 
            'startDate', 
            'endDate', 
            'totalPendapatan', 
            'totalOnline', 
            'totalOffline',
            'ringkasanHarian'
        ));
    }
}
