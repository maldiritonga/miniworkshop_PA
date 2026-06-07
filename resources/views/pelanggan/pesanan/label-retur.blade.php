<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Retur {{ $retur->produk->nama_produk }} — RTR-{{ str_pad($retur->id_retur, 5, '0', STR_PAD_LEFT) }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f0f0f0; color: #000; }

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

        .page-wrap { max-width: 400px; margin: 24px auto; padding: 0 12px 60px; }

        .label { background: #fff; border: 1.5px solid #000; font-size: 11px; }

        .top-strip {
            background: #111;
            padding: 6px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .top-strip .brand { display: flex; align-items: center; gap: 8px; }
        .top-strip img { height: 22px; width: auto; object-fit: contain; }
        .top-strip .brand-name { font-size: 13px; font-weight: 900; color: #fff; text-transform: uppercase; }
        .retur-tag { background: #fff; color: #111; font-size: 9px; font-weight: 900; padding: 2px 8px; border-radius: 2px; text-transform: uppercase; letter-spacing: .06em; }

        .barcode-strip {
            border-bottom: 1.5px solid #000;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f9f9f9;
        }
        .order-block .order-no { font-size: 18px; font-weight: 900; letter-spacing: .08em; font-family: 'Courier New', monospace; color: #000; }
        .order-block .order-label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #888; margin-bottom: 2px; }
        .order-block .sub-no { font-size: 10px; font-weight: 700; color: #555; margin-top: 2px; }
        .fake-barcode { display: flex; align-items: flex-end; gap: 1.5px; height: 36px; }
        .fake-barcode span { display: inline-block; background: #000; width: 2px; }

        .dash { border: none; border-top: 1.5px dashed #bbb; }

        .addr-wrap { display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1.5px solid #000; }
        .addr-col { padding: 10px 12px; }
        .addr-col:first-child { border-right: 1.5px dashed #bbb; }
        .addr-tag { display: inline-block; font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: .1em; padding: 2px 6px; border-radius: 2px; margin-bottom: 6px; }
        .tag-from { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }
        .tag-to { background: #111; color: #fff; }
        .addr-name { font-size: 13px; font-weight: 800; color: #000; line-height: 1.3; margin-bottom: 3px; }
        .addr-phone { font-size: 11px; font-weight: 700; color: #111; margin-bottom: 4px; }
        .addr-detail { font-size: 10px; color: #444; line-height: 1.5; }

        .items-wrap { padding: 8px 12px; border-bottom: 1.5px dashed #bbb; }
        .section-title { font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: .1em; color: #888; margin-bottom: 6px; }
        .item-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 4px 0; }
        .item-name { font-size: 11px; font-weight: 700; color: #000; }
        .item-meta { font-size: 9px; color: #888; margin-top: 1px; }

        .alasan-wrap { padding: 8px 12px; border-bottom: 1.5px dashed #bbb; background: #f9f9f9; }
        .alasan-text { font-size: 10px; color: #374151; line-height: 1.5; }

        .warn-strip { padding: 6px 12px; background: #f9f9f9; border-bottom: 1.5px dashed #bbb; display: flex; align-items: flex-start; gap: 6px; }
        .warn-text { font-size: 9px; font-weight: 700; color: #374151; line-height: 1.5; }

        .label-footer { padding: 6px 12px; display: flex; justify-content: space-between; align-items: center; }
        .footer-left { font-size: 8px; color: #aaa; }
        .footer-right { font-size: 8px; font-weight: 800; color: #ccc; text-transform: uppercase; letter-spacing: .06em; }

        @media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .page-wrap { margin: 0; padding: 0; max-width: 100%; }
            @page { size: 100mm 150mm; margin: 3mm; }
        }
    </style>
</head>
<body>

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

            {{-- ① Header --}}
            <div class="top-strip">
                <div class="brand">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo">
                    <span class="brand-name">Mini Workshop</span>
                </div>
                <span class="retur-tag">↩ Retur Barang</span>
            </div>

            {{-- ② No. Retur + barcode --}}
            <div class="barcode-strip">
                <div class="order-block">
                    <div class="order-label">No. Retur</div>
                    <div class="order-no">RTR-{{ str_pad($retur->id_retur, 5, '0', STR_PAD_LEFT) }}</div>
                    <div class="sub-no">Pesanan #{{ str_pad($pesanan->id_pesanan, 6, '0', STR_PAD_LEFT) }}</div>
                </div>
                <div class="fake-barcode">
                    @php
                        $heights = [28,20,32,16,36,24,30,18,34,22,28,14,36,26,20,32,18,30,24,36,16,28,22,34,20];
                    @endphp
                    @foreach($heights as $h)
                        <span style="height:{{ $h }}px"></span>
                    @endforeach
                </div>
            </div>

            {{-- ③ Alamat --}}
            <div class="addr-wrap">
                {{-- Pengirim: pelanggan --}}
                <div class="addr-col">
                    <div><span class="addr-tag tag-from">Dari (Anda)</span></div>
                    <div class="addr-name">{{ $pesanan->user->nama ?? 'Pelanggan' }}</div>
                    <div class="addr-phone">{{ $pesanan->no_hp }}</div>
                    <div class="addr-detail">{{ $pesanan->alamat_pengiriman }}</div>
                </div>
                {{-- Tujuan: toko --}}
                <div class="addr-col">
                    <div><span class="addr-tag tag-to">Kepada (Toko)</span></div>
                    <div class="addr-name">Mini Workshop</div>
                    <div class="addr-phone">081234567890</div>
                    <div class="addr-detail">Jl. DI Panjaitan Jl. Yos Sudarso No.6, Kp. Bandar, Kec. Senapelan, Kota Pekanbaru, Riau 28155</div>
                </div>
            </div>

            {{-- ④ Produk yang diretur (hanya produk ini) --}}
            <div class="items-wrap">
                <div class="section-title">Produk yang Diretur (1 item)</div>
                <div class="item-row">
                    <div style="flex:1">
                        <div class="item-name">{{ $retur->produk->nama_produk }}</div>
                        @if($retur->produk->size)
                        <div class="item-meta">Ukuran: {{ $retur->produk->size }}</div>
                        @endif
                        @if(isset($detailProduk))
                        <div class="item-meta">Qty retur: {{ $detailProduk->qty }} pcs · Rp {{ number_format($detailProduk->harga, 0, ',', '.') }}/pcs</div>
                        @endif
                        <div class="item-meta">Pesanan #{{ str_pad($pesanan->id_pesanan, 6, '0', STR_PAD_LEFT) }} · {{ \Carbon\Carbon::parse($pesanan->tanggal_pesanan)->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>

            {{-- ⑤ Alasan retur --}}
            <div class="alasan-wrap">
                <div class="section-title">Alasan Retur</div>
                <div class="alasan-text">{{ $retur->alasan_retur }}</div>
            </div>

            {{-- ⑥ Petunjuk --}}
            <div class="warn-strip">
                <span style="font-size:13px;">⚠️</span>
                <span class="warn-text">
                    Kemas produk dengan aman sebelum dikirim.<br>
                    Tempelkan label ini di bagian luar paket yang terlihat jelas.<br>
                    Simpan bukti pengiriman hingga retur selesai diproses.
                </span>
            </div>

            {{-- ⑦ Footer --}}
            <div class="label-footer">
                <span class="footer-left">Dicetak: {{ now()->format('d/m/Y H:i') }}</span>
                <span class="footer-right">Mini Workshop © {{ date('Y') }}</span>
            </div>

        </div>
    </div>

</body>
</html>
