<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Pengiriman #{{ str_pad($pesanan->id_pesanan, 6, '0', STR_PAD_LEFT) }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f0f0f0; color: #000; }

        /* ── Toolbar (screen only) ── */
        .toolbar {
            background: #fff;
            border-bottom: 1px solid #ddd;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 99;
        }
        .toolbar a { font-size: 11px; font-weight: 700; color: #555; text-decoration: none; text-transform: uppercase; letter-spacing: .06em; }
        .btn-print {
            background: #111;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .08em;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover { background: #374151; }

        /* ── Page wrap ── */
        .page-wrap { max-width: 400px; margin: 24px auto; padding: 0 12px 60px; }

        /* ── Label card ── */
        .label {
            background: #fff;
            border: 1.5px solid #000;
            font-size: 11px;
        }

        /* ── Top strip: platform branding ── */
        .top-strip {
            background: #fff;
            border-bottom: 1.5px solid #000;
            padding: 6px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .top-strip .brand { display: flex; align-items: center; gap: 8px; }
        .top-strip img { height: 22px; width: auto; object-fit: contain; }
        .top-strip .brand-name { font-size: 13px; font-weight: 900; color: #000; text-transform: uppercase; letter-spacing: -.01em; }
        .top-strip .service-tag { background: #fff; color: #000; border: 1.5px solid #000; font-size: 9px; font-weight: 900; padding: 2px 8px; border-radius: 2px; text-transform: uppercase; letter-spacing: .06em; }

        /* ── Barcode / order number strip ── */
        .barcode-strip {
            border-bottom: 1.5px solid #000;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
        }
        .order-block .order-no {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: .08em;
            font-family: 'Courier New', monospace;
            color: #000;
        }        .order-block .order-label {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #000;
            margin-bottom: 2px;
        }
        .fake-barcode { display: flex; align-items: flex-end; gap: 1.5px; height: 36px; }
        .fake-barcode span { display: inline-block; background: #000; width: 2px; }

        /* ── Divider dashed ── */
        .dash { border: none; border-top: 1.5px dashed #bbb; }

        /* ── Address section ── */
        .addr-wrap { display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1.5px solid #000; }
        .addr-col { padding: 10px 12px; }
        .addr-col:first-child { border-right: 1.5px dashed #bbb; }
        .addr-tag {
            display: inline-block;
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .1em;
            padding: 2px 6px;
            border-radius: 2px;
            margin-bottom: 6px;
        }
        .tag-to { background: #fff; color: #000; border: 1px solid #000; }
        .tag-from { background: #fff; color: #000; border: 1px solid #000; }
        .addr-name { font-size: 13px; font-weight: 800; color: #000; line-height: 1.3; margin-bottom: 3px; }
        .addr-phone { font-size: 11px; font-weight: 700; color: #000; margin-bottom: 4px; }
        .addr-detail { font-size: 10px; color: #000; line-height: 1.5; }

        /* ── Items section ── */
        .items-wrap { padding: 8px 12px; border-bottom: 1.5px dashed #bbb; }
        .section-title { font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: .1em; color: #000; margin-bottom: 6px; }
        .item-row { display: flex; justify-content: space-between; align-items: center; padding: 3px 0; }
        .item-name { font-size: 11px; font-weight: 700; color: #000; }
        .item-meta { font-size: 9px; color: #555; }
        .item-qty { font-size: 11px; font-weight: 900; color: #000; background: #fff; border: 1px solid #000; padding: 1px 7px; border-radius: 3px; }

        /* ── Payment info strip ── */
        .pay-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 7px 12px;
            border-bottom: 1.5px dashed #bbb;
            background: #fff;
        }
        .pay-item .pay-label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #000; }
        .pay-item .pay-val { font-size: 11px; font-weight: 800; color: #000; margin-top: 1px; }
        .pay-item .pay-val.paid { color: #000; font-weight: 900; }

        /* ── Warning strip ── */
        .warn-strip {
            padding: 6px 12px;
            background: #fff;
            border-bottom: 1.5px dashed #bbb;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .warn-strip .warn-icon { font-size: 14px; }
        .warn-strip .warn-text { font-size: 9px; font-weight: 700; color: #000; line-height: 1.4; }

        /* ── Footer ── */
        .label-footer {
            padding: 6px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footer-left { font-size: 8px; color: #555; }
        .footer-right { font-size: 8px; font-weight: 800; color: #555; text-transform: uppercase; letter-spacing: .06em; }

        /* ── Print ── */
        @media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .page-wrap { margin: 0; padding: 0; max-width: 100%; }
            @page { size: 100mm 150mm; margin: 3mm; }
        }
    </style>
</head>
<body>

    <!-- Toolbar -->
    <div class="toolbar">
        <a href="{{ url()->previous() }}">← Kembali</a>
        <button class="btn-print" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print / Download
        </button>
    </div>

    <div class="page-wrap">
        <div class="label">

            {{-- ① Branding strip --}}
            <div class="top-strip">
                <div class="brand">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo">
                    <span class="brand-name">Mini Workshop</span>
                </div>
                <span class="service-tag">{{ strtoupper($pesanan->kurir ?? 'REGULER') }}</span>
            </div>

            {{-- ② Order number + fake barcode --}}
            <div class="barcode-strip">
                <div class="order-block">
                    @if($pesanan->resi)
                        <div class="order-label" style="font-weight: 900; color: #111;">NO. RESI ({{ strtoupper($pesanan->kurir) }})</div>
                        <div class="order-no" style="font-size: 22px; letter-spacing: 0.1em;">{{ $pesanan->resi }}</div>
                        <div style="font-size: 9px; color: #666; margin-top: 2px;">No. Pesanan: #{{ str_pad($pesanan->id_pesanan, 6, '0', STR_PAD_LEFT) }}</div>
                    @else
                        <div class="order-label">No. Pesanan</div>
                        <div class="order-no">{{ str_pad($pesanan->id_pesanan, 6, '0', STR_PAD_LEFT) }}</div>
                        <div style="font-size: 9px; color: #d97706; margin-top: 2px;">*Resi kurir belum terbit</div>
                    @endif
                </div>
                {{-- Fake barcode visual --}}
                <div class="fake-barcode">
                    @php
                        $heights = [28,20,32,16,36,24,30,18,34,22,28,14,36,26,20,32,18,30,24,36,16,28,22,34,20];
                    @endphp
                    @foreach($heights as $h)
                        <span style="height:{{ $h }}px"></span>
                    @endforeach
                </div>
            </div>

            {{-- ③ Addresses --}}
            <div class="addr-wrap">
                {{-- Penerima --}}
                <div class="addr-col">
                    <div><span class="addr-tag tag-to">Kepada</span></div>
                    <div class="addr-name">{{ $pesanan->user->nama ?? 'Pelanggan' }}</div>
                    <div class="addr-phone">{{ $pesanan->no_hp }}</div>
                    <div class="addr-detail">{{ $pesanan->alamat_pengiriman }}</div>
                </div>
                {{-- Pengirim --}}
                <div class="addr-col">
                    <div><span class="addr-tag tag-from">Dari</span></div>
                    <div class="addr-name">Mini Workshop</div>
                    <div class="addr-phone">081234567890</div>
                    <div class="addr-detail">Jl. DI Panjaitan Jl. Yos Sudarso No.6, Kp. Bandar, Kec. Senapelan, Kota Pekanbaru, Riau 28155</div>
                </div>
            </div>

            {{-- ④ Isi paket --}}
            <div class="items-wrap">
                <div class="section-title">Isi Paket</div>
                @foreach($pesanan->detail as $item)
                <div class="item-row">
                    <div>
                        <div class="item-name">{{ $item->produk->nama_produk }}</div>
                        @if($item->produk->size)
                        <div class="item-meta">Ukuran: {{ $item->produk->size }}</div>
                        @endif
                    </div>
                    <div class="item-qty">x{{ $item->qty }}</div>
                </div>
                @endforeach
            </div>

            {{-- ⑤ Info pembayaran --}}
            <div class="pay-strip">
                <div class="pay-item">
                    <div class="pay-label">Metode Bayar</div>
                    <div class="pay-val">{{ strtoupper(str_replace('_', ' ', $pesanan->pembayaran->metode_pembayaran ?? '-')) }}</div>
                </div>
                @if($pesanan->pembayaran?->bank_tujuan)
                <div class="pay-item">
                    <div class="pay-label">Bank</div>
                    <div class="pay-val">{{ $pesanan->pembayaran->bank_tujuan }}</div>
                </div>
                @endif
                <div class="pay-item">
                    <div class="pay-label">Status</div>
                    <div class="pay-val paid">✓ Lunas</div>
                </div>
                <div class="pay-item">
                    <div class="pay-label">Total</div>
                    <div class="pay-val">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- ⑥ Warning --}}
            <div class="warn-strip">
                <span class="warn-icon">⚠️</span>
                <span class="warn-text">Jangan terima paket jika kondisi rusak atau tidak sesuai.<br>Simpan label ini hingga barang diterima dengan baik.</span>
            </div>

            {{-- ⑦ Footer --}}
            <div class="label-footer">
                <span class="footer-left">Dicetak: {{ now()->format('d/m/Y H:i') }} · Tgl Pesan: {{ \Carbon\Carbon::parse($pesanan->tanggal_pesanan)->format('d/m/Y') }}</span>
                <span class="footer-right">Mini Workshop</span>
            </div>

        </div>
    </div>

</body>
</html>
