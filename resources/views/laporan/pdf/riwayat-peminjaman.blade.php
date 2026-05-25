<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Peminjaman</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header .lab-name { font-size: 14px; color: #0d6efd; margin-top: 5px; }
        .filter-info { margin-bottom: 15px; font-size: 11px; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 8px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    @php
        $labName = \App\Models\Setting::get('lab_name', 'Laboratorium UNY', auth()->user()->id_lab ?? null);
    @endphp
    <div class="header">
        <h1>LAPORAN RIWAYAT PEMINJAMAN</h1>
        <div class="lab-name">{{ $labName }}</div>
        <p>Periode pengembalian: {{ \Carbon\Carbon::parse($request->tanggal_awal)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($request->tanggal_akhir)->format('d/m/Y') }}</p>
    </div>
    <div class="filter-info">
        <strong>Filter yang diterapkan:</strong><br>
        Semester: {{ $request->get('id_semester') ? (App\Models\Semester::find($request->id_semester)->nama_semester ?? '-') : 'Semua' }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Kode Transaksi</th><th>Peminjam</th><th>NIM</th><th>Tgl Pinjam</th><th>Tgl Kembali</th>
                <th>Total Unit</th><th>Rusak</th><th>Hilang</th><th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $p)
            @php
                $tglKembali = $p->details->where('status_item','kembali')->max('tanggal_kembali_aktual');
                $rusak = $p->details->where('kondisi_setelah','rusak')->sum('jumlah');
                $hilang = $p->details->where('kondisi_setelah','hilang')->sum('jumlah');
            @endphp
            <tr>
                <td>{{ $p->kode_transaksi }}</td>
                <td>{{ $p->nama_peminjam }}</td>
                <td>{{ $p->nim ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($p->tanggal_penggunaan)->format('d/m/Y') }}</td>
                <td>{{ $tglKembali ? \Carbon\Carbon::parse($tglKembali)->format('d/m/Y') : '-' }}</td>
                <td class="text-right">{{ $p->details->sum('jumlah') }}</td>
                <td class="text-right">{{ $rusak }}</td>
                <td class="text-right">{{ $hilang }}</td>
                <td>{{ $p->catatan_awal ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">Dicetak pada {{ now()->translatedFormat('d F Y H:i:s') }}</div>
</body>
</html>