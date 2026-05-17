<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Barang</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; color: #555; }
        .filter-info { margin-bottom: 15px; font-size: 11px; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 8px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #777; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA BARANG</h1>
        <p>Sistem Informasi Laboratorium AV & TV</p>
    </div>
    <div class="filter-info">
        <strong>Filter yang diterapkan:</strong><br>
        Semester: {{ $request->get('id_semester') ? (App\Models\Semester::find($request->id_semester)->nama_semester ?? '-') : 'Semua' }} |
        Tanggal cetak: {{ now()->format('d/m/Y H:i') }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Merk Alat</th>
                <th>Nama Alat</th>
                <th>Deskripsi</th>
                <th class="text-right">Baik</th>
                <th class="text-right">Rusak</th>
                <th class="text-right">Hilang</th>
                <th class="text-right">Total</th>
                <th>Kapasitas Alat</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item->merk ?? '-' }}</td>
                <td>{{ $item->nama_barang }}</td>
                <td>{{ Str::limit($item->deskripsi, 80) ?? '-' }}</td>
                <td class="text-right">{{ number_format($item->jumlah_baik) }}</td>
                <td class="text-right">{{ number_format($item->jumlah_rusak) }}</td>
                <td class="text-right">{{ number_format($item->jumlah_hilang) }}</td>
                <td class="text-right">{{ number_format($item->stok) }}</td>
                <td>{{ $item->kapasitas ?: '-' }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#f9f9f9; font-weight:bold">
                <td colspan="3" class="text-right">TOTAL</td>
                <td class="text-right">{{ number_format($data->sum('jumlah_baik')) }}</td>
                <td class="text-right">{{ number_format($data->sum('jumlah_rusak')) }}</td>
                <td class="text-right">{{ number_format($data->sum('jumlah_hilang')) }}</td>
                <td class="text-right">{{ number_format($data->sum('stok')) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    <div class="footer">
        Dicetak pada {{ now()->format('d/m/Y H:i:s') }} | © Sistem Lab AV & TV
    </div>
</body>
</html>