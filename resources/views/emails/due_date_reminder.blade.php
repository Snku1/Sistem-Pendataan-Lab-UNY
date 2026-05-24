<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Jatuh Tempo Peminjaman</title>
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
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
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
            font-size: 15px;
        }
        .lab-details {
            font-size: 12px;
            opacity: 0.85;
            margin-top: 8px;
            line-height: 1.4;
        }
        .content {
            padding: 30px 25px;
        }
        .content h3 {
            color: #0d6efd;
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
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
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
            <h1>⏰ Pengingat Jatuh Tempo</h1>
            <p>{{ App\Models\Setting::get('lab_name', 'Laboratorium UNY') }}</p>
            <div class="lab-details">
                {{ App\Models\Setting::get('lab_address', '') }}<br>
                Telp. {{ App\Models\Setting::get('lab_phone', '') }}
            </div>
        </div>
        <div class="content">
            <h3>Kepada Yth. {{ $peminjaman->nama_peminjam }}</h3>
            <p>Peminjaman barang dengan kode transaksi <strong>{{ $peminjaman->kode_transaksi }}</strong> akan jatuh tempo dalam <strong>{{ $daysBefore }} hari</strong>.</p>
            <p>Harap segera mengembalikan barang ke laboratorium sebelum batas waktu yang ditentukan.</p>

            <table class="detail-table">
                <tr>
                    <th>Kode Transaksi</th>
                    <td>{{ $peminjaman->kode_transaksi }}</td>
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
            </table>

            <div class="warning-box">
                <strong>⚠️ Perhatian:</strong> Segera kembalikan barang tepat waktu untuk kelancaran administrasi laboratorium.
            </div>

            <p>Jika Anda sudah mengembalikan barang, abaikan email ini.</p>
        </div>
        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} {{ App\Models\Setting::get('lab_name', 'Laboratorium UNY') }} - Universitas Negeri Yogyakarta</p>
        </div>
    </div>
</body>
</html>