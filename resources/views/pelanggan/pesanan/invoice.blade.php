<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #INV-{{ str_pad($pesanan->id_pesanan, 5, '0', STR_PAD_LEFT) }} - Mini Workshop</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f5f5f5; color: #111; font-size: 13px; }

        .screen-bar {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .screen-bar a { color: #6b7280; font-size: 11px; font-weight: 700; text-decoration: none; text-transform: uppercase; letter-spacing: .08em; }
        .screen-bar a:hover { color: #111; }
        .btn-download {
            background: #111;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .1em;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-download:hover { background: #374151; }

        .page-wrap { max-width: 680px; margin: 24px auto; padding: 0 16px 48px; }

        /* Invoice card */
        .invoice {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 16px rgba(0,0,0,.08);
        }

        /* Header */
        .inv-header {
            background: #fff;
            color: #000;
            border-bottom: 1.5px solid #000;
            padding: 24px 32px 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .inv-header .brand { display: flex; align-items: center; gap: 10px; }
        .inv-header .brand img { height: 32px; width: auto; object-fit: contain; }
        .inv-header .brand-name { font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: -.02em; color: #000; }
        .inv-header .brand-sub { font-size: 9px; color: #555; font-weight: 600; text-transform: uppercase; letter-spacing: .1em; margin-top: 2px; }
        .inv-header .inv-meta { text-align: right; }
        .inv-header .inv-title { font-size: 22px; font-weight: 800; letter-spacing: -.03em; color: #000; }
        .inv-header .inv-no { font-size: 11px; font-weight: 600; color: #555; margin-top: 2px; }

        /* Status bar */
        .inv-status {
            background: #fff;
            border-bottom: 1.5px dashed #bbb;
            padding: 7px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .inv-status .status-label { font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: .1em; color: #555; }
        .inv-status .status-val { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; color: #000; }

        /* Body */
        .inv-body { padding: 24px 32px; }

        /* Info grid */
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .info-block .info-title { font-size: 8px; font-weight: 800; text-transform: uppercase; letter-spacing: .12em; color: #000; margin-bottom: 6px; }

        /* Divider */
        .divider { border: none; border-top: 1.5px solid #000; margin: 0 0 20px; }

        /* Table */
        .inv-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .inv-table thead tr { border-bottom: 1.5px solid #000; }
        .inv-table thead th { font-size: 8px; font-weight: 800; text-transform: uppercase; letter-spacing: .12em; color: #000; padding: 0 0 8px; text-align: left; }
        .inv-table thead th:last-child { text-align: right; }
        .inv-table thead th.center { text-align: center; }
        .inv-table tbody tr { border-bottom: 1px solid #ddd; }
        .inv-table tbody td { padding: 10px 0; font-size: 11px; vertical-align: middle; }
        .inv-table tbody td:last-child { text-align: right; font-weight: 700; }
        .inv-table tbody td.center { text-align: center; }
        .prod-name { font-weight: 700; color: #000; font-size: 11px; }
        .prod-size { font-size: 9px; color: #555; margin-top: 1px; }

        /* Totals */
        .totals { display: flex; justify-content: flex-end; }
        .totals-box { width: 240px; }
        .totals-row { display: flex; justify-content: space-between; align-items: center; padding: 4px 0; font-size: 11px; }
        .totals-row .label { color: #000; font-weight: 500; }
        .totals-row .val { font-weight: 600; color: #000; }
        .totals-row.grand { border-top: 1.5px solid #000; margin-top: 6px; padding-top: 8px; }
        .totals-row.grand .label { font-size: 12px; font-weight: 800; color: #000; text-transform: uppercase; letter-spacing: .05em; }
        .totals-row.grand .val { font-size: 14px; font-weight: 800; color: #000; }

        /* Payment info */
        .pay-info { margin-top: 20px; padding: 14px 18px; background: #fff; border-radius: 0; border: 1.5px solid #000; }
        .pay-info .pay-title { font-size: 8px; font-weight: 800; text-transform: uppercase; letter-spacing: .12em; color: #000; margin-bottom: 8px; }
        .pay-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .pay-item .pay-label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #000; }
        .pay-item .pay-val { font-size: 11px; font-weight: 700; color: #000; margin-top: 2px; text-transform: uppercase; }

        /* Footer */
        .inv-footer {
            border-top: 1.5px solid #000;
            padding: 14px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .inv-footer .footer-note { font-size: 9px; color: #555; line-height: 1.5; }
        .inv-footer .footer-brand { font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: .1em; color: #555; }

        /* Stamp */
        .stamp-wrap { display: flex; justify-content: flex-end; margin-top: 20px; }
        .stamp {
            border: 2.5px solid #000;
            border-radius: 8px;
            padding: 6px 14px;
            text-align: center;
            transform: rotate(-8deg);
            display: inline-block;
        }
        .stamp .stamp-text { font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .1em; color: #000; }
        .stamp .stamp-sub { font-size: 8px; font-weight: 700; color: #000; letter-spacing: .08em; }

        /* Print styles */
        @media print {
            body { background: white; }
            .screen-bar { display: none !important; }
            .page-wrap { margin: 0; padding: 0; max-width: 100%; }
            .invoice { border-radius: 0; box-shadow: none; }
            @page { size: A4; margin: 12mm; }
        }
    </style>
</head>
<body>

    {{-- Toolbar (hanya tampil di layar, tidak di print) --}}
    <div class="screen-bar">
        <a href="{{ url()->previous() }}">
            ← Kembali
        </a>
        <button class="btn-download" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download / Print Invoice
        </button>
    </div>

    <div class="page-wrap">
        <div class="invoice">

            {{-- Header --}}
            <div class="inv-header">
                <div class="brand">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo">
                    <div>
                        <div class="brand-name">Mini Workshop</div>
                        <div class="brand-sub">Pekanbaru, Sumatra</div>
                    </div>
                </div>
                <div class="inv-meta">
                    <div class="inv-title">INVOICE</div>
                    <div class="inv-no">#INV-{{ str_pad($pesanan->id_pesanan, 5, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>

            {{-- Status bar --}}
            <div class="inv-status">
                <span class="status-label">Status Pembayaran</span>
                <span class="status-val">✓ Lunas</span>
            </div>

            {{-- Body --}}
            <div class="inv-body">

                {{-- Info grid --}}
                <div class="info-grid">
                    <div class="info-block">
                        <div class="info-title">Tagihan Kepada</div>
                        <div style="display:flex;flex-direction:column;gap:4px;margin-top:4px;">
                            <div style="display:flex;gap:6px;font-size:11px;">
                                <span style="color:#000;font-weight:600;min-width:60px;">Nama</span>
                                <span style="color:#000;font-weight:700;">{{ $pesanan->user->nama ?? 'Pelanggan' }}</span>
                            </div>
                            <div style="display:flex;gap:6px;font-size:11px;">
                                <span style="color:#000;font-weight:600;min-width:60px;">Email</span>
                                <span style="color:#000;">{{ $pesanan->user->email ?? '-' }}</span>
                            </div>
                            <div style="display:flex;gap:6px;font-size:11px;">
                                <span style="color:#000;font-weight:600;min-width:60px;">No. HP</span>
                                <span style="color:#000;">{{ $pesanan->no_hp }}</span>
                            </div>
                            @if($pesanan->alamat_pengiriman && $pesanan->alamat_pengiriman !== 'Transaksi Toko Offline')
                            <div style="display:flex;gap:6px;font-size:11px;margin-top:2px;">
                                <span style="color:#000;font-weight:600;min-width:60px;">Alamat</span>
                                <span style="color:#000;line-height:1.5;">{{ $pesanan->alamat_pengiriman }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="info-block" style="text-align:right">
                        <div class="info-title">Detail Invoice</div>
                        <div style="display:flex;flex-direction:column;gap:4px;margin-top:4px;">
                            <div style="display:flex;justify-content:flex-end;gap:6px;font-size:11px;">
                                <span style="color:#000;font-weight:600;">Tanggal</span>
                                <span style="color:#000;font-weight:700;">{{ \Carbon\Carbon::parse($pesanan->tanggal_pesanan)->format('d F Y') }}</span>
                            </div>
                            @if($pesanan->pembayaran?->tanggal_pembayaran)
                            <div style="display:flex;justify-content:flex-end;gap:6px;font-size:11px;">
                                <span style="color:#000;font-weight:600;">Dibayar pada</span>
                                <span style="color:#000;">{{ \Carbon\Carbon::parse($pesanan->pembayaran->tanggal_pembayaran)->format('d F Y, H:i') }}</span>
                            </div>
                            @endif
                            <div style="display:flex;justify-content:flex-end;gap:6px;font-size:11px;">
                                <span style="color:#000;font-weight:600;">Tipe Pesanan</span>
                                <span style="color:#000;">{{ ucfirst($pesanan->tipe_pesanan ?? 'Online') }}</span>
                            </div>
                            <div style="display:flex;justify-content:flex-end;gap:6px;font-size:11px;">
                                <span style="color:#000;font-weight:600;">No. Invoice</span>
                                <span style="color:#000;font-weight:700;">#INV-{{ str_pad($pesanan->id_pesanan, 5, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="divider">

                {{-- Tabel produk --}}
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th style="width:40%">Produk</th>
                            <th class="center">Qty</th>
                            <th class="center">Harga Satuan</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pesanan->detail as $item)
                        <tr>
                            <td>
                                <div class="prod-name">{{ $item->produk->nama_produk }}</div>
                                @if($item->produk->size)
                                <div class="prod-size">Ukuran: {{ $item->produk->size }}</div>
                                @endif
                            </td>
                            <td class="center">{{ $item->qty }}</td>
                            <td class="center">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->harga * $item->qty, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Totals --}}
                <div class="totals">
                    <div class="totals-box">
                        @php
                            $subtotal = $pesanan->detail->sum(fn($i) => $i->harga * $i->qty);
                            $ongkir   = $pesanan->total_harga - $subtotal;
                        @endphp
                        <div class="totals-row">
                            <span class="label">Subtotal Produk</span>
                            <span class="val">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($ongkir > 0)
                        <div class="totals-row">
                            <span class="label">Ongkos Kirim</span>
                            <span class="val">Rp {{ number_format($ongkir, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="totals-row grand">
                            <span class="label">Total</span>
                            <span class="val">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Info pembayaran --}}
                @if($pesanan->pembayaran)
                <div class="pay-info">
                    <div class="pay-title">Informasi Pembayaran</div>
                    <div class="pay-grid">
                        <div class="pay-item">
                            <div class="pay-label">Metode</div>
                            <div class="pay-val">{{ str_replace('_', ' ', $pesanan->pembayaran->metode_pembayaran) }}</div>
                        </div>
                        @if($pesanan->pembayaran->bank_tujuan)
                        <div class="pay-item">
                            <div class="pay-label">Bank</div>
                            <div class="pay-val">{{ $pesanan->pembayaran->bank_tujuan }}</div>
                        </div>
                        @endif
                        <div class="pay-item">
                            <div class="pay-label">Status</div>
                            <div class="pay-val" style="color:#000">✓ Berhasil</div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Stamp lunas --}}
                <div class="stamp-wrap">
                    <div class="stamp">
                        <div class="stamp-text">LUNAS</div>
                        <div class="stamp-sub">{{ \Carbon\Carbon::parse($pesanan->pembayaran?->tanggal_pembayaran ?? $pesanan->tanggal_pesanan)->format('d/m/Y') }}</div>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="inv-footer">
                <div class="footer-note">Terima kasih telah berbelanja di Mini Workshop.<br>Dokumen ini adalah bukti pembayaran yang sah.</div>
                <div class="footer-brand">Mini Workshop &copy; {{ date('Y') }}</div>
            </div>

        </div>
    </div>

</body>
</html>
