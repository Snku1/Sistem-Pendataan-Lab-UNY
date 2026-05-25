<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Barang Masuk</title>
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
        <h1>LAPORAN BARANG MASUK</h1>
        <div class="lab-name">{{ $labName }}</div>
        <p>Periode: {{ \Carbon\Carbon::parse($request->get('tanggal_awal'))->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($request->get('tanggal_akhir'))->format('d/m/Y') }}</p>
    </div>
    <div class="filter-info">
        <strong>Filter yang diterapkan:</strong><br>
        Semester: {{ $request->get('id_semester') ? (App\Models\Semester::find($request->get('id_semester'))->nama_semester ?? '-') : 'Semua' }} |
        Status: {{ $request->get('status') ?: 'Semua' }}
    </div>
    <table>
        <thead>
            <tr><th>Tanggal</th><th>Nama Barang</th><th>Jumlah</th><th>Sumber</th><th>Pemeriksa</th><th>Status</th><th>Catatan</th></tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/Y') }}</td>
                <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                <td class="text-right">{{ number_format($item->jumlah_masuk) }}</td>
                <td>{{ $item->sumber ?? '-' }}</td>
                <td>{{ $item->penanggungJawab->nama_pj ?? '-' }}</td>
                <td>{{ $item->status == 'menunggu' ? 'Menunggu' : 'Diterima' }}</td>
                <td>{{ $item->catatan_pemeriksaan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#f9f9f9; font-weight:bold">
                <td colspan="2" class="text-right">TOTAL</td>
                <td class="text-right">{{ number_format($data->sum('jumlah_masuk')) }}</td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
    </table>
    <div class="footer">Dicetak pada {{ now()->translatedFormat('d F Y H:i:s') }}</div>
</body>
</html>