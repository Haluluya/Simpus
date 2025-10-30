<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak SEP - {{ $patient->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .header p {
            font-size: 11px;
        }
        
        .sep-info {
            background: #f5f5f5;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #000;
        }
        
        .sep-info h3 {
            font-size: 13px;
            margin-bottom: 5px;
        }
        
        .sep-number {
            font-size: 14px;
            font-weight: bold;
        }
        
        .section {
            margin-bottom: 15px;
        }
        
        .section-title {
            font-size: 13px;
            font-weight: bold;
            background: #e0e0e0;
            padding: 5px 8px;
            margin-bottom: 8px;
            border: 1px solid #000;
        }
        
        .data-row {
            display: flex;
            margin-bottom: 5px;
            padding: 3px 0;
        }
        
        .data-label {
            width: 200px;
            font-weight: bold;
        }
        
        .data-value {
            flex: 1;
        }
        
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
        }
        
        .signature-box p {
            margin-bottom: 60px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            padding-top: 5px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #000;
            font-size: 10px;
            text-align: center;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
            
            .container {
                border: 2px solid #000;
            }
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #2563EB;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .print-button:hover {
            background: #1D4ED8;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">🖨️ Cetak SEP</button>
    
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>SURAT ELEGIBILITAS PESERTA (SEP)</h1>
            <h2>{{ $facilityName }}</h2>
            <p>Jl. Contoh No. 123, Kota, Provinsi - Telp: (021) 12345678</p>
        </div>
        
        {{-- SEP Info --}}
        <div class="sep-info">
            <h3>Nomor SEP</h3>
            <div class="sep-number">{{ $sepNumber }}</div>
            <div style="margin-top: 5px;">
                <strong>Tanggal Cetak:</strong> {{ $printDate->format('d/m/Y H:i:s') }}
            </div>
        </div>
        
        {{-- Data Peserta --}}
        <div class="section">
            <div class="section-title">DATA PESERTA</div>
            
            <div class="data-row">
                <div class="data-label">No. Kartu BPJS</div>
                <div class="data-value">: {{ $patient->bpjs_card_no ?: '-' }}</div>
            </div>
            
            <div class="data-row">
                <div class="data-label">Nama Peserta</div>
                <div class="data-value">: {{ $patient->name }}</div>
            </div>
            
            <div class="data-row">
                <div class="data-label">NIK</div>
                <div class="data-value">: {{ $patient->nik }}</div>
            </div>
            
            <div class="data-row">
                <div class="data-label">No. Rekam Medis</div>
                <div class="data-value">: {{ $patient->medical_record_number }}</div>
            </div>
            
            <div class="data-row">
                <div class="data-label">Tanggal Lahir</div>
                <div class="data-value">: {{ $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('d/m/Y') : '-' }}</div>
            </div>
            
            <div class="data-row">
                <div class="data-label">Jenis Kelamin</div>
                <div class="data-value">: {{ $patient->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</div>
            </div>
            
            <div class="data-row">
                <div class="data-label">No. Telepon</div>
                <div class="data-value">: {{ $patient->phone ?: '-' }}</div>
            </div>
            
            <div class="data-row">
                <div class="data-label">Alamat</div>
                <div class="data-value">: {{ $patient->address ?: '-' }}</div>
            </div>
        </div>
        
        {{-- Data Pelayanan --}}
        <div class="section">
            <div class="section-title">DATA PELAYANAN</div>
            
            <div class="data-row">
                <div class="data-label">Tanggal SEP</div>
                <div class="data-value">: {{ $printDate->format('d/m/Y') }}</div>
            </div>
            
            <div class="data-row">
                <div class="data-label">Jenis Pelayanan</div>
                <div class="data-value">: Rawat Jalan</div>
            </div>
            
            <div class="data-row">
                <div class="data-label">Poli Tujuan</div>
                <div class="data-value">: _______________________</div>
            </div>
            
            <div class="data-row">
                <div class="data-label">Dokter</div>
                <div class="data-value">: _______________________</div>
            </div>
            
            <div class="data-row">
                <div class="data-label">Diagnosa Awal</div>
                <div class="data-value">: _______________________</div>
            </div>
            
            <div class="data-row">
                <div class="data-label">Catatan</div>
                <div class="data-value">: _______________________</div>
            </div>
        </div>
        
        {{-- Signature Section --}}
        <div class="signature-section">
            <div class="signature-box">
                <p>Peserta/Keluarga</p>
                <div class="signature-line">
                    (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
                </div>
            </div>
            
            <div class="signature-box">
                <p>Petugas Pendaftaran</p>
                <div class="signature-line">
                    (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
                </div>
            </div>
        </div>
        
        {{-- Footer --}}
        <div class="footer">
            <p><strong>PERHATIAN:</strong></p>
            <p>1. SEP ini berlaku untuk 1 (satu) kali kunjungan</p>
            <p>2. Harap membawa kartu BPJS asli saat berobat</p>
            <p>3. Datang sesuai jadwal yang telah ditentukan</p>
            <p style="margin-top: 10px;">Dokumen ini dicetak secara elektronik dan sah tanpa tanda tangan</p>
        </div>
    </div>
    
    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
