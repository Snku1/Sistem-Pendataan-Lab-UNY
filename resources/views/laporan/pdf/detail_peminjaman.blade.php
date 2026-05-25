<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Detail Peminjaman - {{ $peminjaman->kode_transaksi }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #0d6efd;
        }
        .header .lab-name {
            font-size: 14px;
            color: #555;
            margin-top: 5px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table th, .info-table td {
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }
        .info-table th {
            background-color: #f2f2f2;
            width: 30%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        .items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    @php
        $labName = \App\Models\Setting::get('lab_name', 'Laboratorium UNY', auth()->user()->id_lab ?? null);
    @endphp
    <div class="header">
        <h1>DETAIL PEMINJAMAN</h1>
        <div class="lab-name">{{ $labName }}</div>
        <p>Kode Transaksi: {{ $peminjaman->kode_transaksi }}</p>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr><th>Nama Peminjam</th><td>{{ $peminjaman->nama_peminjam }} @if($peminjaman->nim) (NIM: {{ $peminjaman->nim }}) @endif</td></tr>
            <tr><th>Email</th><td>{{ $peminjaman->email }}</td></tr>
            <tr><th>Tanggal Penggunaan</th><td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_penggunaan)->translatedFormat('d F Y') }}</td></tr>
            <tr><th>Jatuh Tempo</th><td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}</td></tr>
            <tr><th>Status</th><td><strong>{{ ucfirst($peminjaman->status_transaksi) }}</strong></td></tr>
            @if($peminjaman->catatan_awal)
            <tr><th>Catatan Awal</th><td>{{ $peminjaman->catatan_awal }}</td></tr>
            @endif
        </table>
    </div>

    <h3>Daftar Barang yang Dipinjam</h3>
    <table class="items-table">
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Merk</th>
                <th class="text-right">Jumlah</th>
                <th>Kondisi Setelah</th>
                <th>Status Item</th>
                <th>Tanggal Kembali</th>
                <th>Catatan Kembali</th>
            </tr>
        </thead>
        <tbody>
            @foreach($peminjaman->details as $detail)
            <tr>
                <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                <td>{{ $detail->barang->merk ?? '-' }}</td>
                <td class="text-right">{{ $detail->jumlah }}</td>
                <td>{{ ucfirst($detail->kondisi_setelah ?? '-') }}</td>
                <td>
                    @if($detail->status_item == 'dipinjam')
                        <span style="color: #856404;">Dipinjam</span>
                    @else
                        <span style="color: #28a745;">Kembali</span>
                    @endif
                 </td>
                <td>{{ $detail->tanggal_kembali_aktual ? \Carbon\Carbon::parse($detail->tanggal_kembali_aktual)->translatedFormat('d M Y') : '-' }}</td>
                <td>{{ $detail->catatan_kembali ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->translatedFormat('d F Y H:i:s') }}
    </div>
</body>
</html>