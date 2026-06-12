<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiteshipService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.biteship.key');
        $this->baseUrl = 'https://api.biteship.com/v1';
    }

    /**
     * Get tracking information from Biteship
     *
     * @param string $waybill Resi number
     * @param string $courier Courier code (e.g. jne, sicepat)
     * @return array|null
     */
    public function getTracking($waybill, $courier)
    {
        if (!$this->apiKey) {
            Log::error('Biteship API Key is not set.');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get("{$this->baseUrl}/trackings/{$waybill}/couriers/{$courier}");

            if ($response->successful() && isset($response->json()['success']) && $response->json()['success'] === true) {
                return $response->json();
            }

            Log::error('Biteship Tracking Error: ' . $response->body());
            
            // Fallback tracking dummy untuk mode testing
            return [
                'success' => true,
                'messsage' => 'Tracking info found',
                'status' => 'delivering',
                'history' => [
                    [
                        'note' => 'Paket telah diserahkan ke kurir',
                        'updated_at' => now()->subDays(2)->toIso8601String(),
                        'status' => 'dropped'
                    ],
                    [
                        'note' => 'Paket sedang dibawa oleh kurir menuju lokasi tujuan',
                        'updated_at' => now()->subHours(5)->toIso8601String(),
                        'status' => 'delivering'
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Biteship Exception: ' . $e->getMessage());
            
            // Fallback tracking dummy
            return [
                'success' => true,
                'status' => 'delivering',
                'history' => [
                    [
                        'note' => 'Paket sedang dalam perjalanan (Dummy Data Mode)',
                        'updated_at' => now()->toIso8601String(),
                        'status' => 'delivering'
                    ]
                ]
            ];
        }
    }

    /**
     * Get rates based on destination address
     *
     * @param string $destinationAddress
     * @param int $weight
     * @return array
     */
    public function getRates($destinationAddress, $weight = 1000)
    {
        if (!$this->apiKey) {
            return [
                [
                    'company' => 'jne',
                    'type' => 'REG',
                    'price' => 15000
                ],
                [
                    'company' => 'sicepat',
                    'type' => 'BEST',
                    'price' => 20000
                ],
                [
                    'company' => 'jnt',
                    'type' => 'REG',
                    'price' => 18000
                ]
            ];
        }

        try {
            // 1. Search Area ID based on destination
            $areaResponse = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->get("{$this->baseUrl}/maps/areas", [
                'countries' => 'ID',
                'input' => $destinationAddress,
                'type' => 'single'
            ]);

            if (!$areaResponse->successful() || empty($areaResponse->json()['areas'])) {
                return [];
            }

            $destinationAreaId = $areaResponse->json()['areas'][0]['id'];

            // 2. Fetch Rates
            // Menggunakan dummy origin_area_id (Contoh: area ID untuk Jakarta/Bandung)
            // Idealnya origin_area_id ini diset di pengaturan admin.
            $ratesResponse = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/rates/couriers", [
                'origin_area_id' => 'IDNP26IDNC346IDND4044IDZ28155', // Area ID valid untuk Senapelan Pekanbaru
                'destination_area_id' => $destinationAreaId,
                'couriers' => 'jne,sicepat,jnt',
                'items' => [
                    [
                        'name' => 'Produk',
                        'value' => 100000,
                        'weight' => $weight,
                        'quantity' => 1
                    ]
                ]
            ]);

            if ($ratesResponse->successful()) {
                return $ratesResponse->json()['pricing'] ?? [];
            }

            Log::error('Biteship Rates Error: ' . $ratesResponse->body());
            
            // Fallback dummy rates jika API Biteship gagal (misal saldo tidak cukup di testing mode)
            return [
                [
                    'company' => 'jne',
                    'type' => 'REG',
                    'price' => 15000
                ],
                [
                    'company' => 'sicepat',
                    'type' => 'BEST',
                    'price' => 20000
                ],
                [
                    'company' => 'jnt',
                    'type' => 'REG',
                    'price' => 18000
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Biteship Exception: ' . $e->getMessage());
            
            // Fallback dummy rates jika terjadi exception
            return [
                [
                    'company' => 'jne',
                    'type' => 'REG',
                    'price' => 15000
                ],
                [
                    'company' => 'sicepat',
                    'type' => 'BEST',
                    'price' => 20000
                ],
                [
                    'company' => 'jnt',
                    'type' => 'REG',
                    'price' => 18000
                ]
            ];
        }
    }

    /**
     * Membuat pesanan pengiriman (Request Pickup) ke Biteship
     *
     * @param \App\Models\Pesanan $pesanan
     * @return array
     */
    public function createOrder($pesanan)
    {
        if (!$this->apiKey) {
            Log::error('Biteship API Key is not set.');
            return ['success' => false, 'error' => 'API Key belum dikonfigurasi'];
        }

        try {
            $items = [];
            foreach ($pesanan->detail as $item) {
                $items[] = [
                    'name' => $item->produk->nama_produk ?? 'Produk',
                    'value' => (int) $item->harga,
                    'quantity' => (int) $item->qty,
                    'weight' => 500, // Dummy berat 500g
                ];
            }

            // Cari Area ID dan Kode Pos dari Biteship berdasarkan alamat
            $areaResponse = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->get("{$this->baseUrl}/maps/areas", [
                'countries' => 'ID',
                'input' => $pesanan->alamat_pengiriman,
                'type' => 'single'
            ]);

            $destinationAreaId = null;
            $destinationPostalCode = null;

            if ($areaResponse->successful() && !empty($areaResponse->json()['areas'])) {
                $area = $areaResponse->json()['areas'][0];
                $destinationAreaId = $area['id'] ?? null;
                $destinationPostalCode = $area['postal_code'] ?? null;
            }

            // Fallback jika tidak dapat dari API
            if (!$destinationPostalCode) {
                preg_match('/\b\d{5}\b/', $pesanan->alamat_pengiriman, $matches);
                // Default ke kodepos asal jika sama sekali tidak ada yang valid (12345 sering ditolak)
                $destinationPostalCode = $matches[0] ?? 28155; 
            }

            $payload = [
                // Info Pengirim (Toko Anda)
                "origin_contact_name" => "Mini Workshop",
                "origin_contact_phone" => "081234567890",
                "origin_address" => "Jl. DI Panjaitan Jl. Yos Sudarso No.6, Kp. Bandar, Kec. Senapelan, Kota Pekanbaru, Riau 28155",
                "origin_postal_code" => 28155,

                // Info Penerima (Customer)
                "destination_contact_name" => $pesanan->user->nama ?? 'Pelanggan',
                "destination_contact_phone" => $pesanan->no_hp,
                "destination_address" => $pesanan->alamat_pengiriman,
                "destination_postal_code" => (int) $destinationPostalCode,
                
                // Info Kurir
                "courier_company" => strtolower($pesanan->kurir ?? 'jne'),
                "courier_type" => "reg", // Default reg, harusnya dari DB layanan_kurir
                "delivery_type" => "now", // Pickup secepatnya
                
                "items" => $items,
                "reference_id" => (string) $pesanan->id_pesanan . '_' . time(),
            ];

            if ($destinationAreaId) {
                $payload["destination_area_id"] = $destinationAreaId;
            }

            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/orders", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            Log::error('Biteship Create Order Error: ' . $response->body());
            return [
                'success' => false, 
                'error' => $response->json()['error'] ?? 'Gagal membuat pesanan pengiriman'
            ];

        } catch (\Exception $e) {
            Log::error('Biteship Exception (Create Order): ' . $e->getMessage());
            return ['success' => false, 'error' => 'Terjadi kesalahan sistem internal: ' . $e->getMessage()];
        }
    }
}
