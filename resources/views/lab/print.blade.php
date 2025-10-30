<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pemeriksaan Laboratorium - {{ $labOrder->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            padding: 20mm;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 10pt;
            color: #333;
        }
        
        .document-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        
        .info-table td:first-child {
            width: 150px;
            font-weight: bold;
        }
        
        .info-table td:nth-child(2) {
            width: 10px;
        }
        
        .results-section {
            margin-top: 25px;
        }
        
        .results-title {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 15px;
            padding: 8px;
            background-color: #f0f0f0;
            border-left: 4px solid #2563EB;
        }
        
        .result-item {
            page-break-inside: avoid;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #fafafa;
        }
        
        .result-item h3 {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2563EB;
        }
        
        .result-details {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .result-row {
            display: table-row;
        }
        
        .result-label {
            display: table-cell;
            padding: 8px;
            font-weight: bold;
            width: 150px;
            background-color: #e8e8e8;
            border: 1px solid #ccc;
        }
        
        .result-value {
            display: table-cell;
            padding: 8px;
            border: 1px solid #ccc;
            background-color: #fff;
        }
        
        .result-value.large {
            font-size: 16pt;
            font-weight: bold;
        }
        
        .abnormal-high {
            color: #DC2626;
            font-weight: bold;
        }
        
        .abnormal-low {
            color: #F59E0B;
            font-weight: bold;
        }
        
        .abnormal-critical {
            color: #7F1D1D;
            background-color: #FEE2E2;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
        
        .normal {
            color: #16A34A;
            font-weight: bold;
        }
        
        .footer-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        
        .signature-area {
            margin-top: 30px;
            text-align: right;
        }
        
        .signature-box {
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }
        
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        
        .notes-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #FEF3C7;
            border-left: 4px solid #F59E0B;
            page-break-inside: avoid;
        }
        
        .notes-section h4 {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .notes-section p {
            font-size: 10pt;
            line-height: 1.5;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-after: always;
            }
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #2563EB;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 11pt;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        
        .print-button:hover {
            background-color: #1D4ED8;
        }
        
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #6B7280;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 11pt;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            display: inline-block;
            z-index: 1000;
        }
        
        .back-button:hover {
            background-color: #4B5563;
        }
    </style>
</head>
<body>
    {{-- Print Button (hidden saat print) --}}
    <button onclick="window.print()" class="print-button no-print">
        🖨️ Cetak Hasil
    </button>
    
    <a href="{{ route('lab.show', $labOrder) }}" class="back-button no-print">
        ← Kembali
    </a>

    {{-- Header Klinik --}}
    <div class="header">
        <h1>KLINIK PRATAMA SIMPUS</h1>
        <p>Jl. Contoh No. 123, Kota, Provinsi 12345</p>
        <p>Telp: (0123) 456-7890 | Email: info@klinik-simpus.id</p>
    </div>

    {{-- Document Title --}}
    <div class="document-title">
        Hasil Pemeriksaan Laboratorium
    </div>

    {{-- Patient Information --}}
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td>No. Order</td>
                <td>:</td>
                <td><strong>{{ $labOrder->order_number }}</strong></td>
            </tr>
            <tr>
                <td>Nama Pasien</td>
                <td>:</td>
                <td><strong>{{ $labOrder->visit->patient->name }}</strong></td>
            </tr>
            <tr>
                <td>No. Rekam Medis</td>
                <td>:</td>
                <td><strong>{{ $labOrder->visit->patient->medical_record_number }}</strong></td>
            </tr>
            <tr>
                <td>Jenis Kelamin / Usia</td>
                <td>:</td>
                <td>
                    @if($labOrder->visit->patient->gender === 'MALE') Laki-laki
                    @elseif($labOrder->visit->patient->gender === 'FEMALE') Perempuan
                    @else -
                    @endif
                    • 
                    @if($labOrder->visit->patient->date_of_birth)
                        {{ \Carbon\Carbon::parse($labOrder->visit->patient->date_of_birth)->age }} tahun
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <td>Tanggal Lahir</td>
                <td>:</td>
                <td>
                    @if($labOrder->visit->patient->date_of_birth)
                        {{ \Carbon\Carbon::parse($labOrder->visit->patient->date_of_birth)->format('d F Y') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <td>Dokter Peminta</td>
                <td>:</td>
                <td>{{ $labOrder->orderedByUser->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Poli Asal</td>
                <td>:</td>
                <td>{{ $labOrder->visit->clinic_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Waktu Permintaan</td>
                <td>:</td>
                <td>{{ $labOrder->requested_at->format('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
                <td>Waktu Selesai</td>
                <td>:</td>
                <td><strong>{{ $labOrder->completed_at->format('d F Y, H:i') }} WIB</strong></td>
            </tr>
            @if($labOrder->clinical_notes)
            <tr>
                <td>Catatan Klinis</td>
                <td>:</td>
                <td>{{ $labOrder->clinical_notes }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Lab Results --}}
    <div class="results-section">
        <div class="results-title">
            HASIL PEMERIKSAAN
        </div>

        @if($labOrder->items->count() === 0)
        <div class="notes-section">
            <h4>⚠️ Perhatian</h4>
            <p>Tidak ada item pemeriksaan yang tercatat untuk order ini.</p>
        </div>
        @else
            @foreach($labOrder->items as $index => $item)
            <div class="result-item">
                <h3>{{ $index + 1 }}. {{ $item->test_name }}</h3>
                
                @if($item->loinc_code)
                <p style="color: #666; font-size: 9pt; margin-bottom: 10px;">LOINC Code: {{ $item->loinc_code }}</p>
                @endif
                
                @if($item->result)
                <div class="result-details">
                    <div class="result-row">
                        <div class="result-label">Hasil</div>
                        <div class="result-value large">
                            <span class="
                                @if($item->abnormal_flag === 'NORMAL') normal
                                @elseif($item->abnormal_flag === 'HIGH') abnormal-high
                                @elseif($item->abnormal_flag === 'LOW') abnormal-low
                                @elseif($item->abnormal_flag === 'CRITICAL') abnormal-critical
                                @endif
                            ">
                                {{ $item->result }}
                                @if($item->unit)
                                    <span style="font-size: 11pt;">{{ $item->unit }}</span>
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    @if($item->reference_range)
                    <div class="result-row">
                        <div class="result-label">Nilai Rujukan</div>
                        <div class="result-value">{{ $item->reference_range }}</div>
                    </div>
                    @endif
                    
                    @if($item->abnormal_flag)
                    <div class="result-row">
                        <div class="result-label">Interpretasi</div>
                        <div class="result-value">
                            @if($item->abnormal_flag === 'NORMAL')
                                <span class="normal">✓ NORMAL</span>
                            @elseif($item->abnormal_flag === 'HIGH')
                                <span class="abnormal-high">↑ TINGGI (di atas nilai normal)</span>
                            @elseif($item->abnormal_flag === 'LOW')
                                <span class="abnormal-low">↓ RENDAH (di bawah nilai normal)</span>
                            @elseif($item->abnormal_flag === 'CRITICAL')
                                <span class="abnormal-critical">⚠ KRITIS - Memerlukan Perhatian Segera!</span>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    @if($item->result_status)
                    <div class="result-row">
                        <div class="result-label">Status Hasil</div>
                        <div class="result-value">
                            @if($item->result_status === 'FINAL')
                                <strong>FINAL</strong> (Hasil telah diverifikasi)
                            @elseif($item->result_status === 'PRELIMINARY')
                                <strong>PRELIMINARY</strong> (Hasil sementara)
                            @else
                                {{ $item->result_status }}
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    @if($item->resulted_at)
                    <div class="result-row">
                        <div class="result-label">Waktu Input Hasil</div>
                        <div class="result-value">{{ \Carbon\Carbon::parse($item->resulted_at)->format('d F Y, H:i') }} WIB</div>
                    </div>
                    @endif
                </div>
                @else
                <div class="notes-section" style="margin-top: 10px;">
                    <p>⏳ Hasil belum tersedia untuk tes ini.</p>
                </div>
                @endif
            </div>
            @endforeach
        @endif
    </div>

    {{-- Important Notes --}}
    @if($labOrder->items->where('abnormal_flag', 'CRITICAL')->count() > 0)
    <div class="notes-section">
        <h4>⚠️ PERHATIAN PENTING</h4>
        <p>
            Terdapat <strong>{{ $labOrder->items->where('abnormal_flag', 'CRITICAL')->count() }}</strong> 
            hasil pemeriksaan dengan status <strong>KRITIS</strong>. 
            Disarankan untuk segera berkonsultasi dengan dokter yang merawat untuk tindakan lebih lanjut.
        </p>
    </div>
    @endif

    {{-- Footer / Signature --}}
    <div class="footer-section">
        <div class="signature-area">
            <div class="signature-box">
                <p>Petugas Laboratorium</p>
                <div class="signature-line">
                    <strong>{{ Auth::user()->name ?? 'Petugas Lab' }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Print Note --}}
    <div class="notes-section no-print" style="margin-top: 40px;">
        <h4>📄 Informasi Cetak</h4>
        <p>
            Dokumen ini dicetak pada: <strong>{{ now()->format('d F Y, H:i:s') }} WIB</strong><br>
            Pastikan hasil cetak sudah sesuai sebelum mencetak ke printer. 
            Gunakan tombol "Cetak Hasil" di atas atau tekan <strong>Ctrl+P</strong> untuk mencetak.
        </p>
    </div>
</body>
</html>
