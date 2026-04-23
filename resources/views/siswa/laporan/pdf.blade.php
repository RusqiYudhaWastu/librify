<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Detail Sirkulasi - {{ $summary['name'] }}</title>
    <style>
        /* --- RESET & DASAR --- */
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 0; line-height: 1.4; }
        
        /* --- KOP SURAT --- */
        .header { text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 16px; font-weight: 800; margin: 0; text-transform: uppercase; color: #0f172a; letter-spacing: 1px; }
        .header h2 { font-size: 12px; font-weight: bold; margin: 4px 0; text-transform: uppercase; color: #334155; }
        .header p { margin: 2px 0; font-size: 9px; color: #64748b; }

        /* --- INFORMASI SISWA --- */
        .info-box { width: 100%; margin-bottom: 20px; border: 1px solid #e2e8f0; padding: 10px; background-color: #f8fafc; border-radius: 4px; }
        .info-table { width: 100%; border: none; }
        .info-table td { padding: 4px 2px; border: none; vertical-align: top; }
        .label { font-weight: bold; width: 100px; color: #475569; text-transform: uppercase; font-size: 9px; }
        .value { color: #0f172a; font-weight: bold; font-size: 10px; }

        /* --- TABEL UTAMA --- */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 5px; table-layout: fixed; }
        .data-table th { background-color: #0f172a; color: #ffffff; padding: 8px 6px; text-transform: uppercase; font-size: 8px; border: 1px solid #0f172a; text-align: left; }
        .data-table td { border: 1px solid #cbd5e1; padding: 8px 6px; vertical-align: top; word-wrap: break-word; }
        .data-table tr:nth-child(even) { background-color: #f1f5f9; }

        /* --- STYLING KHUSUS DALAM TABEL --- */
        .user-name { font-weight: bold; display: block; font-size: 10px; color: #0f172a; text-transform: uppercase; }
        
        .date-row { display: block; white-space: nowrap; font-size: 8px; margin-bottom: 3px; }
        .date-label { color: #64748b; font-size: 7px; text-transform: uppercase; margin-right: 4px; display: inline-block; width: 35px; }

        /* --- BADGES STATUS --- */
        .badge { padding: 4px 6px; border-radius: 3px; font-size: 7px; font-weight: bold; text-transform: uppercase; display: inline-block; margin-bottom: 2px; text-align: center; }
        .bg-aman { background-color: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .bg-rusak { background-color: #ffedd5; color: #9a3412; border: 1px solid #fdba74; }
        .bg-hilang { background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        
        .bg-lunas { background-color: #2563eb; color: #fff; } 
        .bg-hutang { background-color: #ef4444; color: #fff; }

        /* --- RATING / TRUST SCORE BADGE --- */
        .trust-score { font-size: 8px; background-color: #fef08a; color: #854d0e; border: 1px solid #fde047; padding: 3px 6px; border-radius: 3px; display: inline-block; font-weight: bold; }

        /* --- KOLOM DENDA --- */
        .fine-box { margin-top: 6px; padding: 6px; background-color: #fff; border: 1px dashed #ef4444; border-radius: 4px; }
        .fine-row { display: block; font-size: 8px; margin-bottom: 3px; }
        .fine-money { color: #dc2626; font-weight: bold; font-size: 10px; }

        /* --- FOOTER --- */
        .footer { width: 100%; margin-top: 40px; }
        .sign-col { width: 33%; text-align: center; vertical-align: top; }
        .sign-space { height: 60px; margin-top: 10px; }
        .sign-name { font-weight: bold; text-decoration: underline; text-transform: uppercase; font-size: 10px; }
        .sign-role { font-size: 9px; color: #64748b; margin-top: 2px; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <h1>Laporan Rekapitulasi Peminjaman Buku</h1>
        <h2>PERPUSTAKAAN LIBRIFY - SYSTEM REPORT</h2>
        <p>Dokumen ini dicetak otomatis oleh sistem pada tanggal {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    {{-- INFORMASI SISWA & RUMUS TRUST SCORE --}}
    <div class="info-box">
        @php
            // ✅ LOGIC TRUST SCORE PINTAR
            $userId = collect($data)->first()->user_id ?? null;
            $starScore = '5.0'; 

            if($userId) {
                $avgRating = \App\Models\Loan::where('user_id', $userId)->where('status', 'returned')->avg('rating');
                if ($avgRating > 0) {
                    $starScore = number_format($avgRating, 1);
                } else {
                    $issuesCount = \App\Models\Loan::where('user_id', $userId)->whereIn('return_condition', ['rusak', 'hilang'])->count();
                    $calculatedTrust = 100 - ($issuesCount * 20);
                    $calculatedTrust = max(0, min(100, $calculatedTrust));
                    $starScore = number_format(($calculatedTrust / 100) * 5, 1);
                }
            }
        @endphp
        <table class="info-table">
            <tr>
                <td class="label">Nama Peminjam</td>
                <td class="value">: {{ $summary['name'] }}</td>
                <td class="label">Total Transaksi</td>
                <td class="value">: {{ $summary['total_loans'] }} Peminjaman</td>
            </tr>
            <tr>
                <td class="label">Periode Data</td>
                <td class="value">: {{ $summary['filter_start'] }} - {{ $summary['filter_end'] }}</td>
                <td class="label">Total Denda</td>
                <td class="value">: Rp {{ number_format($summary['total_fines'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Dicetak Oleh</td>
                <td class="value">: {{ $summary['admin'] ?? $summary['toolman'] ?? 'Sistem Librify' }}</td>
                <td class="label">Trust Score</td>
                <td class="value">: <span class="trust-score">★ {{ $starScore }} / 5.0</span></td>
            </tr>
        </table>
    </div>

    {{-- TABEL DATA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20px; text-align: center;">No</th>
                <th style="width: 100px;">Data Peminjam</th>
                <th style="width: 180px;">Rincian Buku & Kuantitas</th>
                <th style="width: 90px;">Jadwal & Durasi</th>
                <th style="width: 60px; text-align: center;">Status</th>
                <th>Insiden & Denda</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Menggabungkan transaksi berdasarkan waktu dan status
                $groupedData = collect($data)->groupBy(function($item) {
                    return $item->created_at->format('Y-m-d H:i') . '|' . $item->status;
                });
                $no = 1;
            @endphp

            @forelse($groupedData as $groupKey => $group)
            @php
                $first = $group->first();
                $itemCount = $group->count();
                $totalFine = $group->sum('fine_amount');
                $totalLost = $group->sum('lost_quantity');
                
                $isUnpaid = $group->where('fine_status', 'unpaid')->count() > 0;
                $hasIssue = $group->whereIn('return_condition', ['rusak', 'hilang'])->count() > 0;

                $unitLabel = $first->duration_unit == 'hours' ? 'Jam' : 'Hari';
                $deadline = $first->created_at->copy();
                
                if($first->duration_unit == 'hours') {
                    $deadline->addHours($first->duration_amount);
                } else {
                    $deadline->addDays($first->duration_amount);
                }

                $rating = $first->user_rating ?? 0;
            @endphp
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                
                {{-- PEMINJAM & TRUST SCORE --}}
                <td>
                    <span class="user-name">{{ $first->user->name }}</span>
                    
                    <div style="margin-top: 8px;">
                        <span style="font-size: 6px; font-weight: bold; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 2px;">Trust Score Peminjam:</span>
                        @if($rating > 0)
                            <span class="trust-score">★ {{ number_format($rating, 1) }} / 5.0</span>
                        @else
                            <span style="font-size: 7px; color: #94a3b8; font-style: italic;">(Belum ada rating)</span>
                        @endif
                    </div>
                </td>

                {{-- ✅ RINCIAN BUKU & KUANTITAS (TATA LETAK DIRAPIHKAN) --}}
                <td style="padding-right: 10px;">
                    @if($itemCount > 1)
                        <div style="margin-bottom: 6px; font-weight: bold; font-size: 8px; color: #ffffff; background: #4f46e5; padding: 4px 6px; border-radius: 3px; display: inline-block;">
                            PAKET PEMINJAMAN ({{ $itemCount }} BUKU)
                        </div>
                    @endif
                    
                    <table style="width: 100%; border-collapse: collapse; margin-top: 2px;">
                        @foreach($group as $log)
                        <tr>
                            <td style="border: none; padding: 4px 0; font-size: 9px; border-bottom: 1px solid #e2e8f0;">
                                <span style="font-weight: bold; color: #0f172a; display: block; margin-bottom: 2px;">{{ $log->item->name }}</span>
                                <span style="font-size: 7px; color: #64748b; font-family: monospace;">ISBN: {{ $log->item->asset_code }}</span>
                                
                                @if($log->return_condition && $log->return_condition !== 'aman')
                                    <div style="margin-top: 4px;">
                                        <span style="color: #dc2626; font-size: 7px; font-weight: bold; text-transform: uppercase; background: #fee2e2; padding: 2px 4px; border-radius: 2px;">[{{ $log->return_condition }}]</span> 
                                        <span style="font-style: italic; font-size: 7px; color: #b91c1c;">"{{ $log->return_note }}"</span>
                                    </div>
                                @endif
                            </td>
                            <td style="border: none; padding: 4px 0; font-size: 9px; font-weight: bold; color: #4f46e5; text-align: right; vertical-align: top; border-bottom: 1px solid #e2e8f0; width: 45px;">
                                {{ $log->quantity }} Buku
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </td>

                {{-- JADWAL & DURASI --}}
                <td>
                    <span class="date-row">
                        <span class="date-label">PINJAM</span>: {{ $first->created_at->format('d/m/Y H:i') }}
                    </span>
                    <span class="date-row" style="color: #2563eb; font-weight: bold;">
                        <span class="date-label">DURASI</span>: {{ $first->duration_amount }} {{ $unitLabel }}
                    </span>
                    <span class="date-row" style="color: #dc2626;">
                        <span class="date-label">BATAS</span>: {{ $deadline->format('d/m/Y H:i') }}
                    </span>

                    @if($first->return_date)
                        <div style="margin-top: 6px; border-top: 1px dashed #cbd5e1; padding-top: 5px;">
                            <span class="date-row" style="color: #166534; font-weight: bold;">
                                <span class="date-label">KEMBALI</span>: {{ \Carbon\Carbon::parse($first->return_date)->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    @endif
                </td>

                {{-- STATUS PAKET --}}
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
                        @php
                            $statusLabel = $first->status === 'pending' ? 'MENUNGGU' : 'DIPINJAM';
                        @endphp
                        <span class="badge" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; display: block;">{{ $statusLabel }}</span>
                    @endif
                </td>

                {{-- INSIDEN & DENDA PAKET --}}
                <td>
                    @if($totalFine > 0 || $totalLost > 0)
                        
                        @if($totalLost > 0)
                            <div style="font-size: 8px; color: #dc2626; margin-bottom: 4px;">
                                Total Hilang: <b>{{ $totalLost }} Buku</b>
                            </div>
                        @endif

                        @if($totalFine > 0)
                            <div class="fine-box">
                                <span class="fine-row">Total Denda Transaksi:<br> <span class="fine-money">Rp {{ number_format($totalFine, 0, ',', '.') }}</span></span>
                                <span class="fine-row" style="margin-top: 4px; padding-top: 4px; border-top: 1px solid #fecaca;">
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
                        <span style="color: #166534; font-size: 8px; font-weight: bold;">
                            @if($first->status === 'returned') Aman, tidak ada insiden. @else - @endif
                        </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px; font-style: italic; color: #94a3b8;">Tidak ada data riwayat peminjaman pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER TANDA TANGAN --}}
    <table class="footer">
        <tr>
            <td class="sign-col">
                <p>Mengetahui,<br>Kepala Perpustakaan</p>
                <div class="sign-space"></div>
                <div class="sign-name">( ........................... )</div>
                <div class="sign-role">NIP. ...........................</div>
            </td>
            <td class="sign-col">
                <p>Diperiksa Oleh,<br>Petugas Perpustakaan</p>
                <div class="sign-space"></div>
                <div class="sign-name">( ........................... )</div>
                <div class="sign-role">Petugas Sistem Librify</div>
            </td>
            <td class="sign-col">
                <p>Bogor, {{ now()->translatedFormat('d F Y') }}<br>Peminjam Buku,</p>
                <div class="sign-space"></div>
                <div class="sign-name">{{ $summary['name'] }}</div>
                <div class="sign-role">Siswa / Member Perpustakaan</div>
            </td>
        </tr>
    </table>

</body>
</html>