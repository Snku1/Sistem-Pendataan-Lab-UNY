<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Manajemen Stok</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .filter-info { margin-bottom: 15px; font-size: 11px; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 8px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #777; }
        .text-right { text-align: right; }
        .warning { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN MANAJEMEN STOK</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($request->get('tanggal_awal', date('Y-m-01')))->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($request->get('tanggal_akhir', date('Y-m-t')))->format('d/m/Y') }}</p>
    </div>
    <div class="filter-info">
        Filter: Semester: {{ $request->get('id_semester') ? (App\Models\Semester::find($request->id_semester)->nama_semester ?? '-') : 'Semua' }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Merk</th>
                <th class="text-right">Stok Awal</th>
                <th class="text-right">Stok Masuk</th>
                <th class="text-right">Stok Keluar</th>
                <th class="text-right">Stok Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item->nama_barang }}</td>
                <td>{{ $item->merk }}</td>
                <td class="text-right">{{ number_format($item->stok_awal) }}</td>
                <td class="text-right">{{ number_format($item->stok_masuk) }}</td>
                <td class="text-right">{{ number_format($item->stok_keluar) }}</td>
                <td class="text-right {{ $item->stok_akhir <= 2 ? 'warning' : '' }}">{{ number_format($item->stok_akhir) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#f9f9f9; font-weight:bold">
                <td colspan="2" class="text-right">TOTAL</td>
                <td class="text-right">{{ number_format($data->sum('stok_awal')) }}</td>
                <td class="text-right">{{ number_format($data->sum('stok_masuk')) }}</td>
                <td class="text-right">{{ number_format($data->sum('stok_keluar')) }}</td>
                <td class="text-right">{{ number_format($data->sum('stok_akhir')) }}</td>
            </tr>
        </tfoot>
    </table>
    <div class="footer">* Stok ≤ 2 ditandai merah sebagai peringatan menipis.<br>Dicetak: {{ now()->format('d/m/Y H:i:s') }}</div>
</body>
</html>