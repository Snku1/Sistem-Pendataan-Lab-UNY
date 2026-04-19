<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;
use App\Models\Lokasi;

class BarangSeeder extends Seeder
{
    public function run()
    {
        $lokasi = Lokasi::first();

        $barangList = [
            [
                'kode_barang' => 'AV001',
                'nama_barang' => 'Kamera Video',
                'merk' => 'Sony',
                'deskripsi' => 'Sony HXR-NX100 Full HD NXCAM Single 1" Exmor R CMOS Sensor, Dual SD Memory Card Slots, 24x Clear Image Zoom, 48x Digital Zoom, 1920x1080 up to 60p, Slow and Quick Motion Function, Sony G Lens with 12x Optical Zoom, Discrete Manual Focus, Zoom, Iris Rings, XAVC S, AVCHD 2.0, DV Recording Codecs, 2 x 3-Pin XLR Audio Inputs',
                'kapasitas' => null,
                'stok' => 3,
                'kondisi' => 'baik',
                'keterangan' => 'Lab AV & TV, Gedung IDB Lantai 2, FT UNY'
            ],
            [
                'kode_barang' => 'AV002',
                'nama_barang' => 'Kamera Foto',
                'merk' => 'Canon',
                'deskripsi' => 'Kamera DSLR Canon 80D 24MP APS-C CMOS sensor with Dual Pixel AF, 45-point AF system with all cross-type points, 3" 1.04M-dot articulating touchscreen, 1080/60p video capture, 7 fps continuous shooting with AF, Weather-resistant body, 7560-pixel RGB+IR Metering Sensor, Wi-Fi + NFC',
                'kapasitas' => null,
                'stok' => 1,
                'kondisi' => 'baik',
                'keterangan' => null
            ],
            [
                'kode_barang' => 'AV003',
                'nama_barang' => 'Lighting',
                'merk' => 'Yongnuo',
                'deskripsi' => 'Light Source: 300 LED beads, Output Power: 18W, Lumen: 2280LM, Color Temperature: 5500K, Color Rendering Index: ≥90%',
                'kapasitas' => null,
                'stok' => 2,
                'kondisi' => 'baik',
                'keterangan' => null
            ],
            [
                'kode_barang' => 'AV004',
                'nama_barang' => 'Wireless Video Transmission',
                'merk' => 'Hollyland',
                'deskripsi' => 'Wireless Video Transmitter/Receiver Set, Transmitter HDMI/SDI Input, SDI Loop Out, Receiver HDMI & 2 x SDI Outputs, OLED Screen, USB Type-C Power Input, 1000′ Line-of-Sight 1080p60 Transmission, 5.1 to 5.9 GHz Frequency Range, AES-128 Encryption, 40 ms Latency, L-Series Battery Plates on TX/RX, DC Adapter Power or Optional Batter',
                'kapasitas' => null,
                'stok' => 3,
                'kondisi' => 'baik',
                'keterangan' => null
            ],
            [
                'kode_barang' => 'AV005',
                'nama_barang' => 'Microphone Wireless',
                'merk' => 'Saramonic',
                'deskripsi' => '2 x Omni Lav Mic & Omni Mic Built-In, 2 Ultracompact Clip-On Pro Transmitter, Baterai Internal 8 Jam, Charging Case, 3.5mm Cables for Camera & Mobile Device, 18 System, up to 328′ (100 meter) Range',
                'kapasitas' => null,
                'stok' => 3,
                'kondisi' => 'baik',
                'keterangan' => null
            ],
            [
                'kode_barang' => 'AV006',
                'nama_barang' => 'USB Audio Interface',
                'merk' => 'Behringer U-Phoria UMC22',
                'deskripsi' => '2 Inputs / 2 Outputs, Max Sample Rate/Resolution 48 kHz / 24-Bit, Display and Indicators 2 x LED (Signal) 2 x LED (Clip) 1 x LED (+48V) 1 x LED (Power), Host Connection 1 x USB Type-B (USB 2.0), Analog I/O 1 x Combo XLR-1/4" TRS Mic/Line Input 1 x 1/4" TRS Hi-Z Input 2 x 1/4" TRS Monitor Output 1 x 1/4" TRS Headphone Output, Phantom Power +48 V Selectable On/Off',
                'kapasitas' => null,
                'stok' => 1,
                'kondisi' => 'baik',
                'keterangan' => null
            ],
            [
                'kode_barang' => 'AV007',
                'nama_barang' => 'Audio Mixing Console',
                'merk' => 'Yamaha MG16XU',
                'deskripsi' => '16-Channel Mixing Console: Max. 10 Mic / 16 Line Inputs (8 mono + 4 stereo) / 4 GROUP Buses + 1 Stereo Bus / 4 AUX (incl. FX). "D-PRE" mic preamps, 1-Knob compressors, high-grade effects SPX with 24 programs, 24-bit/192kHz 2in/2out USB Audio functions, works with iPad, includes Cubase AI, PAD switch, +48V phantom power, XLR balanced outputs, internal universal power supply, rack mount kit, metal chassis, 444x130x500 mm, 6.8 kg',
                'kapasitas' => null,
                'stok' => 1,
                'kondisi' => 'baik',
                'keterangan' => null
            ],
            [
                'kode_barang' => 'AV008',
                'nama_barang' => 'Tripod',
                'merk' => 'Libec',
                'deskripsi' => 'Tripod Libec',
                'kapasitas' => null,
                'stok' => 5,
                'kondisi' => 'baik',
                'keterangan' => null
            ],
            [
                'kode_barang' => 'AV009',
                'nama_barang' => 'TV',
                'merk' => 'Samsung',
                'deskripsi' => 'SAMSUNG UA43K5002AK',
                'kapasitas' => null,
                'stok' => 1,
                'kondisi' => 'baik',
                'keterangan' => null
            ],
            [
                'kode_barang' => 'AV010',
                'nama_barang' => 'Perangkat Streaming',
                'merk' => 'VMOX',
                'deskripsi' => 'VMOX RIGEL II FOR VMIX VIDEO SWITCHER & STREAMING',
                'kapasitas' => null,
                'stok' => 1,
                'kondisi' => 'baik',
                'keterangan' => null
            ],
            [
                'kode_barang' => 'AV011',
                'nama_barang' => 'Intercom',
                'merk' => 'Hollyland',
                'deskripsi' => 'FF Hollyland Solidcom C1-6S Full-Duplex Wireless DECT Intercom System - C1-6S',
                'kapasitas' => '6 orang',
                'stok' => 1,
                'kondisi' => 'baik',
                'keterangan' => null
            ],
            [
                'kode_barang' => 'AV012',
                'nama_barang' => 'Intercom',
                'merk' => 'Eartec',
                'deskripsi' => 'Eartec HUB7S Mini Duplex Base 7-Person Wireless Intercom System',
                'kapasitas' => '7 orang',
                'stok' => 1,
                'kondisi' => 'baik',
                'keterangan' => null
            ],
            [
                'kode_barang' => 'AV013',
                'nama_barang' => 'Speaker',
                'merk' => 'Behringer Eurolive B208D',
                'deskripsi' => 'Active 200 Watt 2-Way PA Speaker System with 8" Woofer and 1.35" Compression Driver',
                'kapasitas' => null,
                'stok' => 2,
                'kondisi' => 'baik',
                'keterangan' => null
            ],
            [
                'kode_barang' => 'AV014',
                'nama_barang' => 'Audio Mixing Console',
                'merk' => 'Behringer X32 Compact',
                'deskripsi' => 'Compact 40-input channel, 25-bus digital mixing console for Studio and Live application. 16 Midas-designed fully programmable mic preamps, 17 fully automated motorized 100 mm faders, 8 XLR outputs plus 6 additional line in/outputs, 2 phones connectors, talkback, LCD Scribble Strips, 32x32 USB 2.0 audio interface, iPad/iPhone apps, high-resolution 7" color TFT, Main LCR, 6 matrix buses, 16 mix buses, 8 DCA and 6 mute groups, virtual FX rack, 40-bit floating-point DSP, dual AES50 ports, USB type-A, ULTRANET, AES/EBU, MIDI, expansion port',
                'kapasitas' => null,
                'stok' => 1,
                'kondisi' => 'baik',
                'keterangan' => null
            ],
        ];

        foreach ($barangList as $barang) {
            $barang['id_lokasi'] = $lokasi->id_lokasi;
            Barang::create($barang);
        }
    }
}