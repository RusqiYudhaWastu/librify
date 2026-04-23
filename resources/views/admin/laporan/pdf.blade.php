<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi - {{ $summary['admin'] ?? $summary['toolman'] ?? 'Petugas' }}</title>
    <style>
        /* --- RESET & DASAR --- */
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 0; line-height: 1.4; }
        
        /* --- KOP SURAT --- */
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 16px; font-weight: 800; margin: 0; text-transform: uppercase; color: #4f46e5; letter-spacing: 1px; }
        .header h2 { font-size: 12px; font-weight: bold; margin: 4px 0; text-transform: uppercase; color: #334155; }
        .header p { margin: 2px 0; font-size: 9px; color: #64748b; }

        /* --- RINGKASAN DATA --- */
        .info-box { width: 100%; margin-bottom: 20px; border: 1px solid #e0e7ff; padding: 10px; background-color: #eef2ff; }
        .info-table { width: 100%; border: none; }
        .info-table td { padding: 2px; border: none; vertical-align: top; }
        .label { font-weight: bold; width: 120px; color: #4f46e5; text-transform: uppercase; font-size: 9px; }
        .value { color: #334155; font-weight: bold; }

        /* --- TABEL UTAMA --- */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        .data-table th { background-color: #4f46e5; color: #ffffff; padding: 8px 6px; text-transform: uppercase; font-size: 8px; border: 1px solid #4f46e5; text-align: left; }
        .data-table td { border: 1px solid #cbd5e1; padding: 8px 6px; vertical-align: top; word-wrap: break-word; }
        .data-table tr:nth-child(even) { background-color: #f8fafc; }

        /* --- STYLING DATA DALAM TABEL --- */
        .user-name { font-weight: bold; display: block; font-size: 10px; color: #0f172a; }
        .user-dept { font-size: 8px; color: #64748b; text-transform: uppercase; margin-top: 2px; display: block; }
        
        .item-list { margin: 2px 0 0 0; padding-left: 14px; font-size: 8px; color: #334155; }
        .item-list li { margin-bottom: 5px; }
        .item-name { font-weight: bold; color: #0f172a; }
        .item-code { font-size: 7px; color: #64748b; font-family: 'Courier New', monospace; }

        .date-row { display: block; white-space: nowrap; font-size: 8px; margin-bottom: 2px; }
        .date-label { color: #64748b; font-size: 7px; text-transform: uppercase; margin-right: 4px; display: inline-block; width: 35px; }

        /* --- BADGES STATUS --- */
        .badge { padding: 3px 5px; border-radius: 3px; font-size: 7px; font-weight: bold; text-transform: uppercase; display: inline-block; margin-bottom: 2px; }
        .bg-aman { background-color: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .bg-rusak { background-color: #ffedd5; color: #9a3412; border: 1px solid #fdba74; }
        .bg-hilang { background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        
        .bg-lunas { background-color: #064e3b; color: #fff; }
        .bg-hutang { background-color: #dc2626; color: #fff; }

        /* --- RATING / TRUST SCORE BADGE --- */
        .trust-score { font-size: 7px; background-color: #fef08a; color: #854d0e; border: 1px solid #fde047; padding: 2px 4px; border-radius: 3px; display: inline-block; font-weight: bold; }

        /* --- KOLOM DENDA --- */
        .fine-box { margin-top: 4px; padding: 4px; background-color: #fff; border: 1px dashed #ef4444; border-radius: 3px; }
        .fine-row { display: block; font-size: 8px; margin-bottom: 2px; }
        .fine-money { color: #dc2626; font-weight: bold; font-size: 9px; }

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
        <h1>Laporan Monitoring Sirkulasi Perpustakaan</h1>
        <h2>SMK NEGERI 1 CIOMAS - LIBRIFY PERPUSTAKAAN</h2>
        <p>Rekapitulasi aktivitas peminjaman, skor kepercayaan member, dan insiden buku</p>
    </div>

    {{-- RINGKASAN LAPORAN --}}
    <div class="info-box">
        <table class="info-table">
            <tr>
                <td class="label">Dicetak Oleh</td>
                <td class="value">: {{ $summary['admin'] ?? $summary['toolman'] ?? 'Petugas Perpustakaan' }}</td>
                <td class="label">Periode Data</td>
                <td class="value">: {{ $summary['period'] }}</td>
            </tr>
            <tr>
                <td class="label">Total Transaksi</td>
                <td class="value">: {{ $summary['total'] ?? $summary['total_logs'] ?? 0 }} Log (Paket)</td>
                <td class="label">Total Denda</td>
                <td class="value">: Rp {{ number_format($summary['total_fines'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Cetak</td>
                <td class="value">: {{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
                <td class="label">Status Dokumen</td>
                <td class="value">: <span style="color: #4f46e5;">OFFICIAL REPORT</span></td>
            </tr>
        </table>
    </div>

    {{-- TABEL DATA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20px; text-align: center;">No</th>
                <th style="width: 100px;">Identitas Peminjam</th>
                <th style="width: 150px;">Rincian Buku & Kuantitas</th>
                <th style="width: 90px;">Jadwal & Durasi</th>
                <th style="width: 60px; text-align: center;">Status</th>
                <th>Insiden & Denda Paket</th>
            </tr>
        </thead>
        <tbody>
            @php
                // ✅ LOGIC GROUPING BATCHING
                $groupedData = collect($data)->groupBy(function($item) {
                    return $item->user_id . '|' . $item->created_at->format('Y-m-d H:i') . '|' . $item->status;
                });
                $no = 1;
            @endphp

            @foreach($groupedData as $groupKey => $group)
            @php
                $first = $group->first(); // Data perwakilan
                $itemCount = $group->count();
                $totalFine = $group->sum('fine_amount');
                $totalLost = $group->sum('lost_quantity');
                
                // Status Lunas
                $isUnpaid = $group->where('fine_status', 'unpaid')->count() > 0;

                // Cek kondisi keseluruhan paket
                $hasIssue = $group->whereIn('return_condition', ['rusak', 'hilang'])->count() > 0;

                // Logic Hitung Deadline
                $unitLabel = $first->duration_unit == 'hours' ? 'Jam' : 'Hari';
                $deadline = $first->created_at->copy();
                
                if($first->duration_unit == 'hours') {
                    $deadline->addHours($first->duration_amount);
                } else {
                    $deadline->addDays($first->duration_amount);
                }

                // ✅ LOGIC TRUST SCORE PINTAR (Sesuai Dashboard)
                $avgRating = \App\Models\Loan::where('user_id', $first->user_id)->where('status', 'returned')->avg('rating');
                
                if ($avgRating > 0) {
                    $starScore = number_format($avgRating, 1);
                } else {
                    // Kalkulasi otomatis jika belum di-rate (Base 100, -20 per insiden = Bintang 5.0 jadi turun)
                    $issuesCount = \App\Models\Loan::where('user_id', $first->user_id)->whereIn('return_condition', ['rusak', 'hilang'])->count();
                    $calculatedTrust = 100 - ($issuesCount * 20);
                    $calculatedTrust = max(0, min(100, $calculatedTrust));
                    $starScore = number_format(($calculatedTrust / 100) * 5, 1);
                }
            @endphp
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                
                {{-- PEMINJAM & TRUST SCORE --}}
                <td>
                    <span class="user-name">{{ $first->user->name }}</span>
                    <span class="user-dept">{{ $first->user->classRoom->name ?? 'MEMBER UMUM' }}</span>
                    
                    {{-- ✅ TRUST SCORE BERHASIL DIMUNCULKAN --}}
                    <div style="margin-top: 6px;">
                        <span style="font-size: 6px; font-weight: bold; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 2px;">Trust Score:</span>
                        <span class="trust-score">SCORE {{ $starScore }} / 5.0</span>
                    </div>
                </td>

                {{-- RINCIAN BUKU (MULTIPLE ITEMS) --}}
                <td>
                    @if($itemCount > 1)
                        <div style="margin-bottom: 4px; font-weight: bold; font-size: 8px; color: #4f46e5; background: #e0e7ff; padding: 2px 4px; border-radius: 2px; border: 1px solid #bfdbfe; display: inline-block;">
                            PAKET ({{ $itemCount }} BUKU)
                        </div>
                    @endif
                    <ul class="item-list">
                        @foreach($group as $log)
                            <li>
                                <span class="item-name">{{ $log->item->name }}</span> ({{ $log->quantity }} Buku)<br>
                                <span class="item-code">ISBN: {{ $log->item->asset_code }}</span>
                                
                                {{-- Jika buku spesifik rusak/hilang --}}
                                @if($log->return_condition && $log->return_condition !== 'aman')
                                    <div style="margin-top: 2px;">
                                        <span style="color: #dc2626; font-size: 7px; font-weight: bold; text-transform: uppercase;">[{{ $log->return_condition }}]</span> 
                                        <span style="font-style: italic; font-size: 7px; color: #b91c1c;">"{{ $log->return_note }}"</span>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </td>

                {{-- JADWAL & DURASI --}}
                <td>
                    <span class="date-row">
                        <span class="date-label">PINJAM</span>: {{ $first->created_at->format('d/m/y H:i') }}
                    </span>
                    <span class="date-row" style="color: #1d4ed8; font-weight: bold;">
                        <span class="date-label">DURASI</span>: {{ $first->duration_amount }} {{ $unitLabel }}
                    </span>
                    <span class="date-row" style="color: #be123c;">
                        <span class="date-label">BATAS</span>: {{ $deadline->format('d/m/y H:i') }}
                    </span>
                    
                    @if($first->return_date)
                        <div style="margin-top: 4px; border-top: 1px dashed #cbd5e1; padding-top: 3px;">
                            <span class="date-row" style="color: #064e3b; font-weight: bold;">
                                <span class="date-label">BALIK</span>: {{ \Carbon\Carbon::parse($first->return_date)->format('d/m/y H:i') }}
                            </span>
                        </div>
                    @endif
                </td>

                {{-- STATUS & KONDISI (KESELURUHAN PAKET) --}}
                <td style="text-align: center;">
                    @if($first->status === 'returned')
                        <span class="badge bg-aman" style="display: block; margin-bottom: 4px;">SELESAI</span>
                        @if($hasIssue)
                            <span class="badge bg-rusak" style="display: block;">BERMASALAH</span>
                        @else
                            <span class="badge bg-aman" style="display: block;">KONDISI AMAN</span>
                        @endif
                    @elseif($first->status === 'rejected')
                        <span class="badge bg-hilang" style="display: block;">DITOLAK</span>
                    @else
                        <span class="badge" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; display: block;">
                            @if($first->status === 'pending') MENUNGGU
                            @elseif($first->status === 'approved' || $first->status === 'borrowed') DIPINJAM
                            @else {{ strtoupper($first->status) }}
                            @endif
                        </span>
                    @endif
                </td>

                {{-- INSIDEN & DENDA PAKET --}}
                <td>
                    @if($totalFine > 0 || $totalLost > 0 || $first->admin_note)
                        
                        @if($first->admin_note)
                            <div style="font-size: 8px; color: #475569; margin-bottom: 4px;">
                                <b>Feedback:</b> <i style="color: #334155;">"{{ $first->admin_note }}"</i>
                            </div>
                        @endif

                        @if($totalLost > 0)
                            <div style="font-size: 8px; color: #dc2626; margin-bottom: 2px;">
                                Total Hilang: <b>{{ $totalLost }} Buku</b>
                            </div>
                        @endif

                        @if($totalFine > 0)
                            <div class="fine-box">
                                <span class="fine-row">Total Denda Paket: <span class="fine-money">Rp {{ number_format($totalFine, 0, ',', '.') }}</span></span>
                                <span class="fine-row" style="margin-top: 3px;">
                                    Status: 
                                    @if(!$isUnpaid)
                                        <span class="badge bg-lunas" style="font-size: 6px; padding: 2px 4px;">LUNAS</span>
                                    @else
                                        <span class="badge bg-hutang" style="font-size: 6px; padding: 2px 4px;">BELUM BAYAR</span>
                                    @endif
                                </span>
                            </div>
                        @endif

                    @else
                        <span style="color: #10b981; font-size: 8px; font-weight: bold;">
                            @if($first->status === 'returned') Selesai tanpa kendala. @else - @endif
                        </span>
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
                <p>Mengetahui,<br>Kepala Sekolah / Kepala Perpustakaan</p>
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
                <div class="sign-name">{{ $summary['admin'] ?? $summary['toolman'] ?? 'Petugas Perpustakaan' }}</div>
                <div class="sign-role">Petugas Sistem</div>
            </td>
        </tr>
    </table>

</body>
</html>