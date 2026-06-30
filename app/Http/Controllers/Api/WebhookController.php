<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle incoming webhook from Biteship
     */
    public function biteship(Request $request)
    {
        Log::info('Biteship Webhook Received:', $request->all());

        // Biteship sends event payload. Usually we check the status.
        $status = $request->input('status');
        $orderId = $request->input('order_id');

        if (!$orderId) {
            return response()->json(['message' => 'Missing order_id'], 400);
        }

        // If the status is 'delivered', we mark the order as 'diantar'
        if ($status === 'delivered') {
            $pesanan = Pesanan::where('biteship_order_id', $orderId)->first();

            if ($pesanan) {
                // Only update if it's currently 'dikirim' or something prior to 'diantar'/'selesai'
                if (!in_array($pesanan->status_pesanan, ['selesai', 'dibatalkan', 'diretur'])) {
                    $pesanan->update([
                        'status_pesanan' => 'diantar',
                        'pesanan_diantar_at' => now(),
                    ]);
                    
                    Log::info("Pesanan {$pesanan->id_pesanan} diupdate menjadi diantar via webhook.");
                }
            } else {
                Log::warning("Pesanan dengan biteship_order_id {$orderId} tidak ditemukan.");
            }
        }

        return response()->json(['message' => 'Webhook processed successfully'], 200);
    }
}
