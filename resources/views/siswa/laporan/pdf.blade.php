<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Detail Logistik - {{ $summary['name'] }}</title>
    <style>
        /* --- RESET & DASAR --- */
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 0; line-height: 1.4; }
        
        /* --- KOP SURAT --- */
        .header { text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { font-size: 16px; font-weight: 800; margin: 0; text-transform: uppercase; color: #0f172a; letter-spacing: 1px; }
        .header h2 { font-size: 12px; font-weight: bold; margin: 4px 0; text-transform: uppercase; color: #334155; }
        .header p { margin: 2px 0; font-size: 9px; color: #64748b; }

        /* --- INFORMASI SISWA --- */
        .info-box { width: 100%; margin-bottom: 20px; border: 1px solid #e2e8f0; padding: 10px; background-color: #f8fafc; }
        .info-table { width: 100%; border: none; }
        .info-table td { padding: 2px; border: none; vertical-align: top; }
        .label { font-weight: bold; width: 100px; color: #475569; text-transform: uppercase; font-size: 9px; }
        .value { color: #0f172a; font-weight: bold; }

        /* --- TABEL UTAMA --- */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th { background-color: #0f172a; color: #ffffff; padding: 8px 6px; text-transform: uppercase; font-size: 8px; border: 1px solid #0f172a; text-align: left; }
        .data-table td { border: 1px solid #cbd5e1; padding: 8px 6px; vertical-align: top; }
        .data-table tr:nth-child(even) { background-color: #f1f5f9; }

        /* --- STYLING KHUSUS DALAM TABEL --- */
        .item-name { font-weight: bold; display: block; font-size: 10px; margin-bottom: 2px; }
        .item-code { font-size: 8px; color: #64748b; font-family: 'Courier New', monospace; letter-spacing: -0.5px; }
        
        .date-row { display: block; white-space: nowrap; font-size: 9px; }
        .date-label { color: #64748b; font-size: 7px; text-transform: uppercase; margin-right: 3px; }

        /* --- BADGES STATUS --- */
        .badge { padding: 3px 6px; border-radius: 3px; font-size: 7px; font-weight: bold; text-transform: uppercase; display: inline-block; margin-top: 2px; }
        .bg-aman { background-color: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .bg-rusak { background-color: #ffedd5; color: #9a3412; border: 1px solid #fdba74; }
        .bg-hilang { background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .bg-lunas { background-color: #0f172a; color: #fff; }
        .bg-hutang { background-color: #ef4444; color: #fff; }

        /* --- KOLOM DENDA --- */
        .fine-box { margin-top: 4px; padding: 4px; background-color: #fff; border: 1px dashed #cbd5e1; border-radius: 3px; }
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
        <h1>Laporan Rekapitulasi Peminjaman Aset</h1>
        <h2>SMK NEGERI 1 CIOMAS - UNIT TEKNOLOGI & LOGISTIK</h2>
        <p>Dokumen ini dicetak otomatis oleh sistem TekniLog pada tanggal {{ now()->format('d/m/Y H:i') }} WIB</p>
    </div>

    {{-- INFORMASI SISWA --}}
    <div class="info-box">
        <table class="info-table">
            <tr>
                <td class="label">Nama Siswa</td>
                <td class="value">: {{ $summary['name'] }}</td>
                <td class="label">Total Transaksi</td>
                <td class="value">: {{ $data->count() }} Item</td>
            </tr>
            <tr>
                <td class="label">Kelas / Jurusan</td>
                <td class="value">: {{ $summary['class'] }}</td>
                <td class="label">Status Akun</td>
                <td class="value">: <span style="color: #166534;">AKTIF / VERIFIED</span></td>
            </tr>
        </table>
    </div>

    {{-- TABEL DATA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th style="width: 140px;">Detail Barang</th>
                <th style="width: 90px;">Waktu Peminjaman</th>
                <th style="width: 30px; text-align: center;">Qty</th>
                <th style="width: 80px; text-align: center;">Kondisi Akhir</th>
                <th>Catatan Insiden & Administrasi Denda</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $loan)
            <tr>
                <td style="text-align: center;">{{ $key + 1 }}</td>
                
                {{-- DETAIL BARANG --}}
                <td>
                    <span class="item-name">{{ $loan->item->name }}</span>
                    <span class="item-code">KODE: {{ $loan->item->asset_code }}</span>
                </td>

                {{-- WAKTU --}}
                <td>
                    <span class="date-row"><span class="date-label">PINJAM:</span> {{ $loan->created_at->format('d/m/Y') }}</span>
                    <span class="date-row"><span class="date-label">BALIK:</span> {{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->format('d/m/Y') : '-' }}</span>
                </td>

                {{-- QUANTITY --}}
                <td style="text-align: center; font-weight: bold;">
                    {{ $loan->quantity }}
                </td>

                {{-- KONDISI & STATUS --}}
                <td style="text-align: center;">
                    @php
                        $condClass = 'bg-aman';
                        $condText = 'AMAN';
                        
                        if ($loan->return_condition === 'rusak') {
                            $condClass = 'bg-rusak';
                            $condText = 'RUSAK';
                        } elseif ($loan->return_condition === 'hilang') {
                            $condClass = 'bg-hilang';
                            $condText = 'HILANG';
                        } elseif ($loan->status !== 'returned') {
                            $condClass = ''; 
                            $condText = 'DIPINJAM';
                        }
                    @endphp
                    
                    @if($loan->status === 'returned')
                        <span class="badge {{ $condClass }}">{{ $condText }}</span>
                    @else
                        <span class="badge" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">AKTIF</span>
                    @endif
                </td>

                {{-- DETAIL INSIDEN & DENDA --}}
                <td>
                    @if($loan->return_condition !== 'aman' && $loan->status === 'returned')
                        <div style="margin-bottom: 4px;">
                            <span style="font-weight: bold; font-size: 9px; color: #991b1b;">INSIDEN:</span>
                            <span style="font-style: italic; color: #333;">"{{ $loan->return_note ?? '-' }}"</span>
                        </div>
                        
                        @if($loan->lost_quantity > 0)
                            <div style="font-size: 8px; color: #64748b;">
                                Unit Terdampak: <b>{{ $loan->lost_quantity }} Unit</b>
                            </div>
                        @endif

                        @if($loan->fine_amount > 0)
                            <div class="fine-box">
                                <span class="fine-row">Nominal Denda: <span class="fine-money">Rp {{ number_format($loan->fine_amount, 0, ',', '.') }}</span></span>
                                <span class="fine-row" style="margin-top: 2px;">
                                    Status: 
                                    @if($loan->fine_status === 'paid')
                                        <span class="badge bg-lunas" style="font-size: 6px; padding: 1px 4px;">LUNAS</span>
                                    @else
                                        <span class="badge bg-hutang" style="font-size: 6px; padding: 1px 4px;">BELUM LUNAS</span>
                                    @endif
                                </span>
                            </div>
                        @endif
                    @elseif($loan->status === 'returned')
                        <span style="color: #166534; font-size: 9px;">- Pengembalian sukses & tepat waktu.</span>
                    @else
                        <span style="color: #64748b; font-size: 9px; font-style: italic;">Sedang dalam peminjaman.</span>
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
                <p>Mengetahui,<br>Kepala Program Keahlian</p>
                <div class="sign-space"></div>
                <div class="sign-name">( ........................... )</div>
                <div class="sign-role">NIP. ...........................</div>
            </td>
            <td class="sign-col">
                <p>Diperiksa Oleh,<br>Toolman Lab. RPL/TKJ</p>
                <div class="sign-space"></div>
                <div class="sign-name">( ........................... )</div>
                <div class="sign-role">Petugas Logistik</div>
            </td>
            <td class="sign-col">
                <p>Bogor, {{ now()->translatedFormat('d F Y') }}<br>Peminjam Aset</p>
                <div class="sign-space"></div>
                <div class="sign-name">{{ $summary['name'] }}</div>
                <div class="sign-role">Siswa / Perwakilan Kelas</div>
            </td>
        </tr>
    </table>

</body>
</html>