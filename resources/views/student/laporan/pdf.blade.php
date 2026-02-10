<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat - {{ $summary['name'] }}</title>
    <style>
        /* --- RESET & DASAR --- */
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 0; line-height: 1.4; }
        
        /* --- KOP SURAT --- */
        .header { text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 16px; font-weight: 800; margin: 0; text-transform: uppercase; color: #0f172a; letter-spacing: 1px; }
        .header h2 { font-size: 12px; font-weight: bold; margin: 4px 0; text-transform: uppercase; color: #0891b2; } /* Cyan Dark */
        .header p { margin: 2px 0; font-size: 9px; color: #64748b; }

        /* --- RINGKASAN DATA --- */
        .info-box { width: 100%; margin-bottom: 20px; border: 1px solid #e2e8f0; padding: 10px; background-color: #f8fafc; }
        .info-table { width: 100%; border: none; }
        .info-table td { padding: 2px; border: none; vertical-align: top; }
        .label { font-weight: bold; width: 100px; color: #0f172a; text-transform: uppercase; font-size: 9px; }
        .value { color: #334155; font-weight: bold; text-transform: uppercase; }

        /* --- TABEL UTAMA --- */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        .data-table th { background-color: #0e7490; color: #ffffff; padding: 8px 6px; text-transform: uppercase; font-size: 8px; border: 1px solid #0e7490; text-align: left; }
        .data-table td { border: 1px solid #cbd5e1; padding: 8px 6px; vertical-align: top; word-wrap: break-word; }
        .data-table tr:nth-child(even) { background-color: #f0f9ff; } /* Light Cyan bg */

        /* --- BADGES STATUS --- */
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 7px; font-weight: bold; text-transform: uppercase; display: inline-block; margin-top: 2px; }
        .bg-aman { color: #166534; background-color: #dcfce7; }
        .bg-rusak { color: #9a3412; background-color: #ffedd5; }
        .bg-hilang { color: #991b1b; background-color: #fee2e2; }
        
        /* --- FOOTER --- */
        .footer { width: 100%; margin-top: 40px; }
        .sign-col { width: 33%; text-align: center; vertical-align: top; }
        .sign-space { height: 60px; margin-top: 10px; }
        .sign-name { font-weight: bold; text-decoration: underline; text-transform: uppercase; font-size: 10px; }
        .sign-role { font-size: 9px; color: #64748b; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <h1>Laporan Riwayat Peminjaman Siswa</h1>
        <h2>SMK NEGERI 1 CIOMAS - UNIT TEKNOLOGI & LOGISTIK</h2>
        <p>Dokumen ini adalah bukti sah riwayat penggunaan alat praktik siswa.</p>
    </div>

    {{-- RINGKASAN DATA SISWA --}}
    <div class="info-box">
        <table class="info-table">
            <tr>
                <td class="label">Nama Siswa</td>
                <td class="value">: {{ $summary['name'] }}</td>
                <td class="label">Periode Data</td>
                <td class="value">: {{ $summary['filter_start'] }} - {{ $summary['filter_end'] }}</td>
            </tr>
            <tr>
                <td class="label">Kelas / Jurusan</td>
                <td class="value">: {{ $summary['class'] }}</td>
                <td class="label">Tanggal Cetak</td>
                <td class="value">: {{ $summary['date'] }}</td>
            </tr>
        </table>
    </div>

    {{-- TABEL DATA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th style="width: 140px;">Nama Barang / Alat</th>
                <th style="width: 90px;">Tanggal Pinjam</th>
                <th style="width: 90px;">Tanggal Kembali</th>
                <th style="width: 80px;">Status</th>
                <th>Catatan & Kondisi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $key => $row)
            <tr>
                <td style="text-align: center;">{{ $key + 1 }}</td>
                
                {{-- BARANG --}}
                <td>
                    <span style="font-weight: bold; display: block;">{{ $row->item->name }}</span>
                    <span style="font-size: 8px; color: #64748b;">Kode: {{ $row->item->asset_code }}</span>
                </td>

                {{-- TANGGAL --}}
                <td>{{ $row->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $row->return_date ? \Carbon\Carbon::parse($row->return_date)->format('d/m/Y H:i') : '-' }}</td>

                {{-- STATUS --}}
                <td>
                    @php
                        $statusMap = [
                            'pending' => 'Menunggu',
                            'approved' => 'Dipinjam',
                            'returned' => 'Selesai',
                            'rejected' => 'Ditolak'
                        ];
                    @endphp
                    <span style="font-weight: bold; font-size: 8px; text-transform: uppercase;">
                        {{ $statusMap[$row->status] ?? $row->status }}
                    </span>
                </td>

                {{-- KONDISI & CATATAN --}}
                <td>
                    @if($row->return_condition)
                        @php
                            $badgeClass = match($row->return_condition) {
                                'aman' => 'bg-aman',
                                'rusak' => 'bg-rusak',
                                'hilang' => 'bg-hilang',
                                default => ''
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">Kondisi: {{ $row->return_condition }}</span>
                        @if($row->return_note)
                            <div style="font-size: 8px; margin-top: 2px; font-style: italic;">"{{ $row->return_note }}"</div>
                        @endif
                    @else
                        <span style="color: #cbd5e1;">-</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px; font-style: italic; color: #94a3b8;">Tidak ada data riwayat pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER TANDA TANGAN --}}
    <table class="footer">
        <tr>
            <td class="sign-col">
                <p>Mengetahui,<br>Waka Sarpras / Toolman</p>
                <div class="sign-space"></div>
                <div class="sign-name">( ........................... )</div>
            </td>
            <td class="sign-col">
                {{-- Kosong --}}
            </td>
            <td class="sign-col">
                <p>Bogor, {{ now()->translatedFormat('d F Y') }}<br>Siswa Peminjam,</p>
                <div class="sign-space"></div>
                <div class="sign-name">{{ $summary['name'] }}</div>
                <div class="sign-role">{{ $summary['class'] }}</div>
            </td>
        </tr>
    </table>

</body>
</html>