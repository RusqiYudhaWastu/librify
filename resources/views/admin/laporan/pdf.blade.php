<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Admin - {{ $summary['admin'] }}</title>
    <style>
        /* --- RESET & DASAR --- */
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 0; line-height: 1.4; }
        
        /* --- KOP SURAT --- */
        .header { text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 16px; font-weight: 800; margin: 0; text-transform: uppercase; color: #0f172a; letter-spacing: 1px; }
        .header h2 { font-size: 12px; font-weight: bold; margin: 4px 0; text-transform: uppercase; color: #334155; }
        .header p { margin: 2px 0; font-size: 9px; color: #64748b; }

        /* --- RINGKASAN DATA --- */
        .info-box { width: 100%; margin-bottom: 20px; border: 1px solid #e2e8f0; padding: 10px; background-color: #f1f5f9; }
        .info-table { width: 100%; border: none; }
        .info-table td { padding: 2px; border: none; vertical-align: top; }
        .label { font-weight: bold; width: 120px; color: #0f172a; text-transform: uppercase; font-size: 9px; }
        .value { color: #334155; font-weight: bold; }

        /* --- TABEL UTAMA --- */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        .data-table th { background-color: #0f172a; color: #ffffff; padding: 8px 6px; text-transform: uppercase; font-size: 8px; border: 1px solid #0f172a; text-align: left; }
        .data-table td { border: 1px solid #cbd5e1; padding: 8px 6px; vertical-align: top; word-wrap: break-word; }
        .data-table tr:nth-child(even) { background-color: #f8fafc; }

        /* --- STYLING DATA DALAM TABEL --- */
        .user-name { font-weight: bold; display: block; font-size: 10px; color: #0f172a; }
        .user-dept { font-size: 8px; color: #64748b; text-transform: uppercase; }
        
        .item-name { font-weight: bold; display: block; font-size: 9px; margin-bottom: 1px; }
        .item-code { font-size: 7px; color: #64748b; font-family: 'Courier New', monospace; }

        .date-row { display: block; white-space: nowrap; font-size: 8px; }
        .date-label { color: #64748b; font-size: 7px; text-transform: uppercase; margin-right: 3px; }

        /* --- BADGES STATUS --- */
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 7px; font-weight: bold; text-transform: uppercase; display: inline-block; margin-top: 2px; }
        .bg-aman { background-color: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .bg-rusak { background-color: #ffedd5; color: #9a3412; border: 1px solid #fdba74; }
        .bg-hilang { background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .bg-aktif { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        
        .bg-lunas { background-color: #064e3b; color: #fff; }
        .bg-hutang { background-color: #dc2626; color: #fff; }

        /* --- KOLOM DENDA --- */
        .fine-box { margin-top: 4px; padding: 3px; background-color: #fff; border: 1px dashed #cbd5e1; border-radius: 3px; }
        .fine-row { display: block; font-size: 8px; margin-bottom: 1px; }
        .fine-money { color: #dc2626; font-weight: bold; }

        /* --- FOOTER --- */
        .footer { width: 100%; margin-top: 40px; }
        .sign-col { width: 33%; text-align: center; vertical-align: top; }
        .sign-space { height: 50px; margin-top: 10px; }
        .sign-name { font-weight: bold; text-decoration: underline; text-transform: uppercase; }
        .sign-role { font-size: 9px; color: #64748b; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <h1>Laporan Monitoring Logistik (Administrator)</h1>
        <h2>SMK NEGERI 1 CIOMAS - UNIT TEKNOLOGI & LOGISTIK</h2>
        <p>Rekapitulasi aktivitas peminjaman dan insiden aset sekolah</p>
    </div>

    {{-- RINGKASAN LAPORAN --}}
    <div class="info-box">
        <table class="info-table">
            <tr>
                <td class="label">Dicetak Oleh</td>
                <td class="value">: {{ $summary['admin'] }} (Administrator System)</td>
                <td class="label">Periode Data</td>
                <td class="value">: {{ $summary['period'] }}</td>
            </tr>
            <tr>
                <td class="label">Total Transaksi</td>
                <td class="value">: {{ $data->count() }} Log Aktivitas</td>
                <td class="label">Tanggal Cetak</td>
                <td class="value">: {{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
            </tr>
        </table>
    </div>

    {{-- TABEL DATA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20px; text-align: center;">No</th>
                <th style="width: 100px;">Peminjam (Siswa/Kelas)</th>
                <th style="width: 110px;">Detail Barang</th>
                <th style="width: 85px;">Waktu Sirkulasi</th>
                <th style="width: 25px; text-align: center;">Qty</th>
                <th style="width: 60px; text-align: center;">Kondisi</th>
                <th>Catatan & Status Denda</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $log)
            <tr>
                <td style="text-align: center;">{{ $key + 1 }}</td>
                
                {{-- PEMINJAM --}}
                <td>
                    <span class="user-name">{{ $log->user->name }}</span>
                    <span class="user-dept">{{ $log->user->department->name ?? 'UMUM' }}</span>
                </td>

                {{-- BARANG --}}
                <td>
                    <span class="item-name">{{ $log->item->name }}</span>
                    <span class="item-code">ASSET: {{ $log->item->asset_code }}</span>
                </td>

                {{-- WAKTU --}}
                <td>
                    <span class="date-row"><span class="date-label">PINJAM:</span> {{ $log->created_at->format('d/m/y H:i') }}</span>
                    <span class="date-row"><span class="date-label">BALIK:</span> {{ $log->return_date ? \Carbon\Carbon::parse($log->return_date)->format('d/m/y H:i') : '-' }}</span>
                </td>

                {{-- QTY --}}
                <td style="text-align: center; font-weight: bold;">
                    {{ $log->quantity }}
                </td>

                {{-- KONDISI --}}
                <td style="text-align: center;">
                    @php
                        $condClass = 'bg-aktif';
                        $condText = 'DIPINJAM';
                        
                        if ($log->status === 'returned') {
                            if ($log->return_condition === 'aman') {
                                $condClass = 'bg-aman';
                                $condText = 'AMAN';
                            } elseif ($log->return_condition === 'rusak') {
                                $condClass = 'bg-rusak';
                                $condText = 'RUSAK';
                            } elseif ($log->return_condition === 'hilang') {
                                $condClass = 'bg-hilang';
                                $condText = 'HILANG';
                            }
                        }
                    @endphp
                    <span class="badge {{ $condClass }}">{{ $condText }}</span>
                </td>

                {{-- INSIDEN & DENDA --}}
                <td>
                    @if(in_array($log->return_condition, ['rusak', 'hilang']))
                        <div style="margin-bottom: 3px;">
                            <span style="font-weight: bold; font-size: 8px; color: #991b1b;">INSIDEN:</span>
                            <span style="font-style: italic; font-size: 9px;">"{{ $log->return_note ?? '-' }}"</span>
                        </div>

                        @if($log->lost_quantity > 0)
                            <div style="font-size: 8px; color: #64748b; margin-bottom: 2px;">
                                Unit Terdampak: <b>{{ $log->lost_quantity }} Unit</b>
                            </div>
                        @endif

                        @if($log->fine_amount > 0)
                            <div class="fine-box">
                                <span class="fine-row">Denda: <span class="fine-money">Rp {{ number_format($log->fine_amount, 0, ',', '.') }}</span></span>
                                <span class="fine-row" style="margin-top: 2px;">
                                    Status: 
                                    @if($log->fine_status === 'paid')
                                        <span class="badge bg-lunas" style="font-size: 6px; padding: 1px 4px;">LUNAS</span>
                                    @else
                                        <span class="badge bg-hutang" style="font-size: 6px; padding: 1px 4px;">BELUM BAYAR</span>
                                    @endif
                                </span>
                            </div>
                        @endif
                    @elseif($log->status === 'returned')
                        <span style="color: #166534; font-size: 8px;">Pengembalian Tuntas.</span>
                    @else
                        <span style="color: #64748b; font-size: 8px; font-style: italic;">Sedang berjalan...</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- FOOTER TANDA TANGAN --}}
    <table class="footer">
        <tr>
            <td class="sign-col">
                <p>Mengetahui,<br>Kepala Sekolah / Waka Sarpras</p>
                <div class="sign-space"></div>
                <div class="sign-name">( ........................... )</div>
                <div class="sign-role">NIP. ...........................</div>
            </td>
            <td class="sign-col">
                {{-- Spasi Kosong --}}
            </td>
            <td class="sign-col">
                <p>Bogor, {{ now()->translatedFormat('d F Y') }}<br>Diverifikasi Oleh,</p>
                <div class="sign-space"></div>
                <div class="sign-name">{{ $summary['admin'] }}</div>
                <div class="sign-role">Administrator Sistem</div>
            </td>
        </tr>
    </table>

</body>
</html>