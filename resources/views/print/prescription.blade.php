<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resep_{{ $prescription->id }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            padding: 30px;
        }

        /* Header Style */
        .header {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            text-transform: uppercase;
            font-size: 24px;
        }

        /* Information Section */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item p {
            margin: 5px 0;
            font-size: 14px;
        }

        /* Table Style */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }

        td {
            border-bottom: 1px solid #dee2e6;
            padding: 12px;
        }

        .text-right {
            text-align: right;
        }

        /* Footer & Signature */
        .footer-wrapper {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .sig-box {
            text-align: center;
            width: 200px;
        }

        .sig-space {
            height: 80px;
            margin: 10px 0;
        }

        .doctor-name {
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
        }

        /* Print Button Area */
        .action-bar {
            background: #f4f4f4;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .btn {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }

        .btn-print {
            background: #2d3436;
            color: white;
        }

        .btn-back {
            background: #636e72;
            color: white;
            margin-right: 10px;
        }

        /* MEDIA PRINT CONFIGURATION */
        @media print {
            .action-bar {
                display: none;
            }

            /* Tombol tidak ikut dicetak */
            .container {
                border: none;
                padding: 0;
            }

            body {
                padding: 0;
            }

            @page {
                margin: 1.5cm;
            }
        }
    </style>
</head>

<body>

    <div class="action-bar">
        <button onclick="window.history.back()" class="btn btn-back">Kembali</button>
        <button onclick="window.print()" class="btn btn-print">
            Cetak Sekarang
            <i class="bi bi-printer ms-2"></i>
        </button>
    </div>

    <div class="container">
        <div class="header">
            <h1>RS Delta Surya</h1>
            <p>Jl. Pahlawan No. 9, Sidoarjo | Telp: (031) 8962531</p>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <p><strong>Pasien:</strong> {{ $prescription->examination->patient->name }}</p>
                <p><strong>Usia/JK:</strong>
                    {{ \Carbon\Carbon::parse($prescription->examination->patient->born_date)->age }} th /
                    {{ strtoupper($prescription->examination->patient->sex) }}</p>
            </div>
            <div class="info-item text-right">
                <p><strong>Tanggal:</strong> {{ $prescription->created_at->format('d M Y') }}</p>
                <p><strong>Waktu:</strong> {{ $prescription->created_at->format('H:i') }} WIB</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nama Obat</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-right">Jumlah</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prescription->prescriptionDetails as $detail)
                    <tr>
                        <td>{{ $detail->medicine_name }}</td>
                        <td class="text-right">Rp{{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                        <td class="text-right">{{ $detail->qty }}</td>
                        <td class="text-right">Rp{{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total Pembayaran</strong></td>
                    <td class="text-right"><strong>Rp{{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>

        <div class="footer-wrapper">
            <div class="sig-box">
                <p>Penerima,</p>
                <div class="sig-space"></div>
                <p>( ............................... )</p>
            </div>

            <div class="sig-box">
                <p>Sidoarjo, {{ date('d M Y') }}</p>
                <p>Dokter Pemeriksa,</p>
                <div class="sig-space">
                    <p class="signature-img">
                        <img src="{{ asset('storage/doctor-signatures/' . $prescription->examination->user->doctorProfile->signature) }}"
                            style="width: 100px; margin: 0px" alt="">
                    </p>
                </div>
                <p class="doctor-name">{{ $prescription->examination->user->name }}</p>
                <p>STR. {{ $prescription->examination->user->doctorProfile->str_number ?? '------------------' }}</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.onafterprint = function() {
            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                text: "Apakah proses cetak berhasil dan pembayaran sudah diterima?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Sudah Bayar',
                cancelButtonText: 'Batal/Belum'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Hanya jika klik "Ya", maka kirim request ke server
                    updateStatusToPaid();
                }
            });
        };

        function updateStatusToPaid() {
            fetch("{{ route('prescription.update', $prescription->id) }}", {
                    method: "PUT",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Berhasil!', data.msg, 'success')
                            .then(() => window.location.href = "{{ route('pharmacist.home') }}");
                    } else {
                        Swal.fire('Gagal', data.msg, 'error');
                    }
                });
        }
    </script>
</body>

</html>
