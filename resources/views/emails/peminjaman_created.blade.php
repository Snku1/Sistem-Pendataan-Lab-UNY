<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Peminjaman Barang</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #28a745, #218838);
            padding: 25px 20px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .lab-details {
            font-size: 11px;
            opacity: 0.85;
            margin-top: 8px;
            line-height: 1.4;
        }
        .content {
            padding: 30px 25px;
        }
        .content h3 {
            color: #28a745;
            margin-top: 0;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
        }
        .detail-table th, .detail-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-table th {
            background: #e9ecef;
            width: 40%;
            font-weight: 600;
        }
        .detail-table tr:last-child td, .detail-table tr:last-child th {
            border-bottom: none;
        }
        .info-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .footer {
            background: #f8f9fa;
            padding: 15px 25px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Konfirmasi Peminjaman</h1>
            <p>{{ App\Models\Setting::get('lab_name', 'Laboratorium UNY') }}</p>
            <div class="lab-details">
                {{ App\Models\Setting::get('lab_address', '') }}<br>
                Telp. {{ App\Models\Setting::get('lab_phone', '') }}
            </div>
        </div>
        <div class="content">
            <h3>Kepada Yth. {{ $peminjaman->nama_peminjam }}</h3>
            <p>Peminjaman barang Anda telah berhasil dicatat dengan kode transaksi <strong>{{ $peminjaman->kode_transaksi }}</strong>.</p>
            <p>Berikut detail peminjaman Anda:</p>

            <table class="detail-table">
                <tr>
                    <th>Kode Transaksi</th>
                    <td>{{ $peminjaman->kode_transaksi }}</td>
                </tr>
                <tr>
                    <th>Nama Peminjam</th>
                    <td>{{ $peminjaman->nama_peminjam }} @if($peminjaman->nim) ({{ $peminjaman->nim }}) @endif</td>
                </tr>
                <tr>
                    <th>Tanggal Peminjaman</th>
                    <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_penggunaan)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <th>Jatuh Tempo</th>
                    <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <th>Barang yang Dipinjam</th>
                    <td>
                        @foreach($peminjaman->details as $detail)
                            - {{ $detail->barang->nama_barang }} ({{ $detail->jumlah }} unit)<br>
                        @endforeach
                    </td>
                </tr>
                @if($peminjaman->catatan_awal)
                <tr>
                    <th>Catatan</th>
                    <td>{{ $peminjaman->catatan_awal }}</td>
                </tr>
                @endif
            </table>

            <div class="info-box">
                <strong>📌 Informasi Penting:</strong> Harap perhatikan tanggal jatuh tempo. Kembalikan barang tepat waktu untuk kelancaran layanan laboratorium.
            </div>

            <p>Terima kasih telah menggunakan layanan peminjaman laboratorium kami.</p>
        </div>
        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} {{ App\Models\Setting::get('lab_name', 'Laboratorium UNY') }} - Universitas Negeri Yogyakarta</p>
        </div>
    </div>
</body>
</html>