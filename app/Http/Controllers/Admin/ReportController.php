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

        $pesanan = Pesanan::with(['user', 'pembayaran'])
            ->where('status_pesanan', 'selesai')
            ->whereBetween('tanggal_pesanan', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->latest()
            ->get();

        $totalPendapatan = $pesanan->sum('total_harga');
        $totalOnline = $pesanan->where('tipe_pesanan', 'online')->sum('total_harga');
        $totalOffline = $pesanan->where('tipe_pesanan', 'offline')->sum('total_harga');

        $ringkasanHarian = Pesanan::select(
                DB::raw('DATE(tanggal_pesanan) as date'),
                DB::raw('SUM(total_harga) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->where('status_pesanan', 'selesai')
            ->whereBetween('tanggal_pesanan', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
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
