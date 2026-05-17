<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Lokasi;
use App\Models\PenanggungJawab;
use App\Models\Semester;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    public function run()
    {
        // Nonaktifkan sementara foreign key checks agar truncate bisa berjalan
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Hapus data lama
        DB::table('barang_penanggungjawab')->truncate();
        DB::table('barang')->truncate();

        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Ambil lokasi (buat jika belum ada)
        $lokasi = Lokasi::first();
        if (!$lokasi) {
            $lokasi = Lokasi::create([
                'nama_lokasi' => 'Laboratorium AV & TV, Departemen Pendidikan Teknik Elektronika dan Informatika FT UNY - Gedung IDB Lantai 2'
            ]);
            $this->command->info('Lokasi baru telah dibuat.');
        }

        // Ambil semester aktif (bisa null jika belum ada)
        $semester = Semester::where('is_active', true)->first();
        if (!$semester) {
            $this->command->warn('Tidak ada semester aktif, id_semester akan diisi NULL.');
        }

        // Ambil atau buat penanggung jawab
        $pj1 = PenanggungJawab::firstOrCreate(
            ['nama_pj' => 'Dr. Ponco Walipranoto, S.Pd.T., M.Pd.'],
            [
                'no_kontak' => '081227210230',
                'email' => 'poncowali@uny.ac.id'
            ]
        );
        $pj2 = PenanggungJawab::firstOrCreate(
            ['nama_pj' => 'Siswi Dwi Ayuriyanti'],
            [
                'no_kontak' => '085743040345',
                'email' => 'siswidwiayuriyanti@uny.ac.id'
            ]
        );

            $barangData = [
                // Data barang (16 item) - tetap sama seperti sebelumnya
                [
                    'nama_barang' => 'Kamera Video',
                    'merk' => 'Sony',
                    'deskripsi' => 'Sony HXR-NX100 Full HD NXCAM Single 1" Exmor R CMOS Sensor, Dual SD Memory Card Slots, Create & Share Picture Profiles, 24x Clear Image Zoom, 48x Digital Zoom, 1920x1080 up to 60p, Slow and Quick Motion Function, Sony G Lens with 12x Optical Zoom, Discrete Manual Focus, Zoom, Iris Rings, XAVC S, AVCHD 2.0, DV Recording Codecs, 2 x 3-Pin XLR Audio Inputs',
                    'kapasitas' => null,
                    'stok' => 3,
                    'jumlah_baik' => 3,
                    'jumlah_rusak' => 0,
                    'jumlah_hilang' => 0,
                    'keterangan' => 'Lab AV & TV, Gedung IDB Lantai 2, FT UNY'
                ],
                [
                    'nama_barang' => 'Kamera Foto',
                    'merk' => 'Canon',
                    'deskripsi' => 'Kamera DSLR Canon 80D 24MP APS-C CMOS sensor with Dual Pixel AF, 45-point AF system with all cross-type points, 3" 1.04M-dot articulating touchscreen, 1080/60p video capture, 7 fps continuous shooting with AF, Weather-resistant body, 7560-pixel RGB+IR Metering Sensor, Wi-Fi + NFC',
                    'kapasitas' => null,
                    'stok' => 1,
                    'jumlah_baik' => 1,
                    'jumlah_rusak' => 0,
                    'jumlah_hilang' => 0,
                    'keterangan' => null
                ],
                [
                    'nama_barang' => 'Lighting',
                    'merk' => 'Yongnuo',
                    'deskripsi' => 'Light Source: 300 LED beads, Output Power: 18W, Lumen: 2280LM, Color Temperature: 5500K, Color Rendering Index: ≥90%',
                    'kapasitas' => null,
                    'stok' => 2,
                    'jumlah_baik' => 2,
                    'jumlah_rusak' => 0,
                    'jumlah_hilang' => 0,
                    'keterangan' => null
                ],
                [
                    'nama_barang' => 'Wireless Video Transmission',
                    'merk' => 'Hollyland',
                    'deskripsi' => 'Wireless Video Transmitter/Receiver Set, Transmitter HDMI/SDI Input, SDI Loop Out, Receiver HDMI & 2 x SDI Outputs, OLED Screen, USB Type-C Power Input, 1000′ Line-of-Sight 1080p60 Transmission, 5.1 to 5.9 GHz Frequency Range, AES-128 Encryption, 40 ms Latency, L-Series Battery Plates on TX/RX, DC Adapter Power or Optional Batter',
                    'kapasitas' => null,
                    'stok' => 3,
                    'jumlah_baik' => 3,
                    'jumlah_rusak' => 0,
                    'jumlah_hilang' => 0,
                    'keterangan' => null
                ],
                [
                    'nama_barang' => 'Microphone Wireless',
                    'merk' => 'Saramonic',
                    'deskripsi' => '2 x Omni Lav Mic & Omni Mic Built-In, 2 Ultracompact Clip-On Pro Transmitter, Baterai Internal 8 Jam, Charging Case, 3.5mm Cables for Camera & Mobile Device, 18 System, up to 328′ (100 meter) Range',
                    'kapasitas' => null,
                    'stok' => 3,
                    'jumlah_baik' => 3,
                    'jumlah_rusak' => 0,
                    'jumlah_hilang' => 0,
                    'keterangan' => null
                ],
                [
                    'nama_barang' => 'USB Audio Interface',
                    'merk' => 'Behringer U-Phoria UMC22',
                    'deskripsi' => '2 Inputs / 2 Outputs, Max Sample Rate/Resolution 48 kHz / 24-Bit, Display and Indicators 2 x LED (Signal) 2 x LED (Clip) 1 x LED (+48V) 1 x LED (Power), Host Connection 1 x USB Type-B (USB 2.0), Analog I/O 1 x Combo XLR-1/4" TRS Mic/Line Input 1 x 1/4" TRS Hi-Z Input 2 x 1/4" TRS Monitor Output 1 x 1/4" TRS Headphone Output, Phantom Power +48 V Selectable On/Off',
                    'kapasitas' => null,
                    'stok' => 1,
                    'jumlah_baik' => 1,
                    'jumlah_rusak' => 0,
                    'jumlah_hilang' => 0,
                    'keterangan' => null
                ],
                [
                    'nama_barang' => 'Audio Mixing Console',
                    'merk' => 'Yamaha MG16XU',
                    'deskripsi' => '16-Channel Mixing Console: Max. 10 Mic / 16 Line Inputs (8 mono + 4 stereo) / 4 GROUP Buses + 1 Stereo Bus / 4 AUX (incl. FX). "D-PRE" mic preamps, 1-Knob compressors, high-grade effects SPX with 24 programs, 24-bit/192kHz 2in/2out USB Audio functions, works with iPad, includes Cubase AI, PAD switch, +48V phantom power, XLR balanced outputs, internal universal power supply, rack mount kit, metal chassis, 444x130x500 mm, 6.8 kg',
                    'kapasitas' => null,
                    'stok' => 1,
                    'jumlah_baik' => 1,
                    'jumlah_rusak' => 0,
                    'jumlah_hilang' => 0,
                    'keterangan' => null
                ],
                [
                    'nama_barang' => 'Tripod',
                    'merk' => 'Libec',
                    'deskripsi' => 'Tripod Libec',
                    'kapasitas' => null,
                    'stok' => 5,
                    'jumlah_baik' => 5,
                    'jumlah_rusak' => 0,
                    'jumlah_hilang' => 0,
                    'keterangan' => null
                ],
                [
                    'nama_barang' => 'TV',
                    'merk' => 'Samsung',
                    'deskripsi' => 'SAMSUNG UA43K5002AK',
                    'kapasitas' => null,
                    'stok' => 1,
                    'jumlah_baik' => 1,
                    'jumlah_rusak' => 0,
                    'jumlah_hilang' => 0,
                    'keterangan' => null
                ],
                [
                    'nama_barang' => 'Perangkat Streaming',
                    'merk' => 'VMOX',
                    'deskripsi' => 'VMOX RIGEL II FOR VMIX VIDEO SWITCHER & STREAMING',
                    'kapasitas' => null,
                    'stok' => 1,
                    'jumlah_baik' => 1,
                    'jumlah_rusak' => 0,
                    'jumlah_hilang' => 0,
                    'keterangan' => null
                ],
                [
                    'nama_barang' => 'Intercom',
                    'merk' => 'Hollyland',
                    'deskripsi' => 'FF Hollyland Solidcom C1-6S Full-Duplex Wireless DECT Intercom System - C1-6S',
                    'kapasitas' => '6 orang',
                    'stok' => 1,
                    'jumlah_baik' => 1,
                    'jumlah_rusak' => 0,
                    'jumlah_hilang' => 0,
                    'keterangan' => null
                ],
                [
                    'nama_barang' => 'Intercom',
                    'merk' => 'Eartec',
                    'deskripsi' => 'Eartec HUB7S Mini Duplex Base 7-Person Wireless Intercom System',
                    'kapasitas' => '7 orang',
                    'stok' => 1,
                    'jumlah_baik' => 1,
                    'jumlah_rusak' => 0,
                    'jumlah_hilang' => 0,
                    'keterangan' => null
                ],
                [
                    'nama_barang' => 'Speaker',
                    'merk' => 'Behringer Eurolive B208D',
                    'deskripsi' => 'Active 200 Watt 2-Way PA Speaker System with 8" Woofer and 1.35" Compression Driver',
                    'kapasitas' => null,
                    'stok' => 2,
                    'jumlah_baik' => 2,
                    'jumlah_rusak' => 0,
                    'jumlah_hilang' => 0,
                    'keterangan' => null
                ],
                [
                    'nama_barang' => 'Audio Mixing Console',
                    'merk' => 'Behringer X32 Compact',
                    'deskripsi' => 'Compact 40-input channel, 25-bus digital mixing console for Studio and Live application. 16 Midas-designed fully programmable mic preamps, 17 fully automated motorized 100 mm faders, 8 XLR outputs plus 6 additional line in/outputs, 2 phones connectors, talkback, LCD Scribble Strips, 32x32 USB 2.0 audio interface, iPad/iPhone apps, high-resolution 7" color TFT, Main LCR, 6 matrix buses, 16 mix buses, 8 DCA and 6 mute groups, virtual FX rack, 40-bit floating-point DSP, dual AES50 ports, USB type-A, ULTRANET, AES/EBU, MIDI, expansion port',
                    'kapasitas' => null,
                    'stok' => 1,
                    'jumlah_baik' => 1,
                    'jumlah_rusak' => 0,
                    'jumlah_hilang' => 0,
                    'keterangan' => null
                ],
                [
                    'nama_barang' => 'Virtual Reality Gear',
                    'merk' => 'Oculus Quest 2',
                    'deskripsi' => 'Oculus Quest 3',
                    'kapasitas' => null,
                    'stok' => 3,
                    'jumlah_baik' => 3,
                    'jumlah_rusak' => 0,
                    'jumlah_hilang' => 0,
                    'keterangan' => null
                ],
                [
                    'nama_barang' => 'Camera Tracking',
                    'merk' => 'Intel RealSense T265',
                    'deskripsi' => 'Camera Tracking Intel RealSense T266',
                    'kapasitas' => null,
                    'stok' => 3,
                    'jumlah_baik' => 3,
                    'jumlah_rusak' => 0,
                    'jumlah_hilang' => 0,
                    'keterangan' => null
                ],
            ];

           $kodeCounter = 1;
        foreach ($barangData as $data) {
            $kode = 'AV' . str_pad($kodeCounter, 3, '0', STR_PAD_LEFT);
            $barang = Barang::create([
                'kode_barang' => $kode,
                'nama_barang' => $data['nama_barang'],
                'merk' => $data['merk'],
                'deskripsi' => $data['deskripsi'],
                'kapasitas' => $data['kapasitas'],
                'id_lokasi' => $lokasi->id_lokasi,
                'stok' => $data['stok'],
                'jumlah_baik' => $data['jumlah_baik'],
                'jumlah_rusak' => $data['jumlah_rusak'],
                'jumlah_hilang' => $data['jumlah_hilang'],
                'keterangan' => $data['keterangan'],
                'id_semester' => $semester ? $semester->id_semester : null,
            ]);

            // Attach kedua penanggung jawab
            $barang->penanggungJawab()->attach([$pj1->id_pj, $pj2->id_pj]);

            $kodeCounter++;
        }

        $this->command->info('Seeder Barang berhasil dijalankan. ' . ($kodeCounter - 1) . ' barang telah ditambahkan.');
    }
}