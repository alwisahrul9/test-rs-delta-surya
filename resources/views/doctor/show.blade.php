@extends('layouts.app')

@section('content')
    <style>
        /* Memastikan modal body bisa scroll jika konten lebih panjang dari layar */
        #modalExamination .modal-body {
            max-height: calc(100vh - 200px);
            /* Menghitung sisa tinggi layar */
            overflow-y: auto;
        }
    </style>
    <div class="container-fluid">
        {{-- Tambahkan Tombol di Card Header --}}
        <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalExamination">
            <i class="bi bi-plus-lg"></i> Tambah Pemeriksaan
        </button>

        {{-- MODAL EXAMINATION --}}
        <div class="modal fade" id="modalExamination" tabindex="-1" aria-labelledby="modalExaminationLabel" aria-hidden="true">
            <div class="modal-dialog modal-full modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white" id="modalExaminationLabel">Tambah Pemeriksaan Baru -
                            {{ $patient->name }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('patients.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                        <input type="hidden" name="name" value="{{ $patient->name }}">
                        <input type="hidden" name="born_date" value="{{ $patient->born_date }}">
                        <input type="hidden" name="sex" value="{{ $patient->sex }}">

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    {{-- Tanda Vital --}}
                                    @php
                                        $vitals = [
                                            'height' => 'Tinggi Badan (cm)',
                                            'weight' => 'Berat Badan (kg)',
                                            'systole' => 'Systole',
                                            'diastole' => 'Diastole',
                                            'heart_rate' => 'Heart Rate',
                                            'temperature' => 'Suhu (°C)',
                                            'respiration_rate' => 'Respiration Rate',
                                        ];
                                    @endphp
                                    @foreach ($vitals as $key => $label)
                                        <div class="col-12 mb-3">
                                            <label class="form-label">{{ $label }}</label>
                                            <input type="number" step="0.1" name="{{ $key }}"
                                                class="form-control @error($key) is-invalid @enderror"
                                                value="{{ old($key) }}">
                                            @error($key)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-md-6">
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Catatan Klinis</label>
                                        <textarea name="clinical_notes" class="form-control @error('clinical_notes') is-invalid @enderror" rows="3">{{ old('clinical_notes') }}</textarea>
                                        @error('clinical_notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    {{-- File Upload --}}
                                    <div class="col-12 mb-3">
                                        <label class="form-label d-flex justify-content-between">
                                            Berkas Luar (Gambar)
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                id="add-file"><i class="bi bi-plus"></i></button>
                                        </label>
                                        <div id="file-container">
                                            <div class="input-group file-row mb-2">
                                                <input type="file" name="patient_files[]" class="form-control"
                                                    accept="image/*">
                                                <button class="btn btn-outline-danger remove-file" type="button"
                                                    style="display:none;"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Resep Obat --}}
                                    <div class="col-12">
                                        <div class="card border border-primary">
                                            <div
                                                class="card-header bg-light d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">Resep Obat</span>
                                                <button type="button" class="btn btn-sm btn-primary"
                                                    id="add-medicine">Tambah Obat</button>
                                            </div>
                                            <div class="card-body" id="medicine-container">
                                                {{-- Baris obat akan muncul di sini --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Simpan Pemeriksaan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0 text-white">Detail Profil Pasien</h4>
            </div>
            <div class="card-body mt-3">
                <div class="row">
                    <div class="col-md-3 border-end">
                        <label class="text-muted small">Nama Lengkap</label>
                        <p class="fw-bold">{{ $patient->name }}</p>
                    </div>
                    <div class="col-md-3 border-end">
                        <label class="text-muted small">Tanggal Lahir</label>
                        <p class="fw-bold">{{ \Carbon\Carbon::parse($patient->born_date)->translatedFormat('d F Y') }}</p>
                    </div>
                    <div class="col-md-3 border-end">
                        <label class="text-muted small">Jenis Kelamin</label>
                        <p class="fw-bold">{{ $patient->sex == 'm' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="text-muted small">Umur</label>
                        <p class="fw-bold">{{ \Carbon\Carbon::parse($patient->born_date)->age }} Tahun</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Riwayat Pemeriksaan</h4>
                <a href="{{ route('doctor.home') }}" class="btn btn-secondary btn-sm">Kembali</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mt-2">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal Periksa</th>
                                <th>Status Bayar</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patient->examinations as $exam)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($exam->examination_time)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @php $status = $exam->prescription->status ?? 'pending'; @endphp
                                        <span class="badge {{ $status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                                            {{ strtoupper($status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if ($status == 'pending')
                                            <a href="{{ route('patients.edit', $patient->id) }}?exam_id={{ $exam->id }}"
                                                class="btn btn-sm btn-info">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-secondary" disabled
                                                title="Data yang sudah dibayar tidak dapat diubah">
                                                <i class="bi bi-lock-fill"></i> Locked
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada riwayat pemeriksaan oleh
                                        Anda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            const medicineApiUrl = "{{ route('proxy.medicines') }}";

            // FUNGSI UTAMA RESEP
            function initChoices(element, defaultValue = null) {
                const choices = new Choices(element, {
                    searchEnabled: true,
                    shouldSort: false,
                    placeholder: true,
                    placeholderValue: 'Cari obat...',
                    itemSelectText: '',
                    allowHTML: true
                });

                choices.setChoices(async () => {
                    try {
                        const res = await fetch(medicineApiUrl); // Memanggil route internal Laravel
                        const data = await res.json();
                        return data.map(item => ({
                            value: `${item.id}_${item.name}`,
                            label: item.name,
                            selected: `${item.id}_${item.name}` == defaultValue
                        }));
                    } catch (err) {
                        return [];
                    }
                });
            }

            function addMedicineRow(valId = '', valQty = '') {
                let uniqueId = 'select-' + Date.now() + Math.floor(Math.random() * 100);
                let rowHtml = `
                <div class="row mb-3 medicine-row align-items-end border-bottom pb-3">
                    <div class="col-md-7">
                        <label class="small text-muted">Nama Obat</label>
                        <select name="medicines[]" class="form-select medicine-select" id="${uniqueId}" required></select>
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted">Kuantitas</label>
                        <input type="number" name="quantities[]" class="form-control" min="1" value="${valQty}" required>
                    </div>
                    <div class="col-md-2 text-end">
                        <button type="button" class="btn btn-outline-danger remove-medicine btn-sm"><i class="bi bi-trash"></i></button>
                    </div>
                </div>`;

                $("#medicine-container").append(rowHtml);
                initChoices(document.getElementById(uniqueId), valId);
                toggleMedicineButtons();
            }

            // LOGIKA OLD DATA (Agar tidak hilang saat error validasi)
            @if (old('medicines'))
                @foreach (old('medicines') as $index => $mId)
                    addMedicineRow('{{ $mId }}', '{{ old('quantities')[$index] }}');
                @endforeach
            @else
                addMedicineRow(); // Baris default jika tidak ada old data
            @endif

            // EVENT HANDLERS
            $("#add-medicine").click(() => addMedicineRow());
            $(document).on('click', '.remove-medicine', function() {
                $(this).closest('.medicine-row').remove();
                toggleMedicineButtons();
            });

            function toggleMedicineButtons() {
                $('.remove-medicine').toggle($('.medicine-row').length > 1);
            }

            // FILE HANDLERS
            function addNewFileRow() {
                $("#file-container").append(`
                    <div class="input-group mb-2 file-row">
                        <input type="file" name="patient_files[]" accept="image/*" class="form-control @error('patient_files.*') is-invalid @enderror">
                        <button class="btn btn-outline-danger remove-file" type="button"><i class="bi bi-trash"></i></button>
                    </div>
                `);
                toggleFileButtons();
            }

            // Logika untuk menangani tampilan saat balik dari error validasi
            @if (old('patient_files') || $errors->has('patient_files.*'))
                // Jika ada error pada file, kita beri peringatan ke user
                // Browser tidak bisa mengembalikan file yang sudah dipilih
                $('#file-container').before(`
                    <div class="alert alert-warning py-2 mb-2">
                        <small><i class="bi bi-exclamatation-triangle me-1"></i> 
                        Karena alasan keamanan, silakan pilih ulang berkas pemeriksaan luar jika sebelumnya sudah memilih.</small>
                    </div>
                `);
            @endif

            $("#add-file").click(function() {
                addNewFileRow();
            });

            function toggleFileButtons() {
                if ($('.file-row').length > 1) {
                    $('.remove-file').show();
                } else {
                    $('.remove-file').hide();
                }
            }

            @if ($errors->any())
                var myModal = new bootstrap.Modal(document.getElementById('modalExamination'));
                myModal.show();

                // Paksa body agar tidak terkunci
                document.body.classList.add('modal-open');
                document.body.style.overflow = 'hidden';
            @endif

            $(document).on('click', '.remove-file', function() {
                $(this).closest('.file-row').remove();
                toggleFileButtons();
            });
        });
    </script>
@endpush
