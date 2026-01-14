@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Tambah Pemeriksaan & Pasien</h4>
            <a href="{{ route('doctor.home') }}" class="btn btn-primary rounded-pill">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('patients.store') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <h5 class="mb-3 text-primary">Identitas Pasien</h5>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror" name="name" id="name"
                            placeholder="Nama Pasien">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-4">
                        <label for="born_date" class="form-label">Tanggal Lahir</label>
                        <input type="date" value="{{ old('born_date') }}"
                            class="form-control @error('born_date') is-invalid @enderror" name="born_date" id="born_date">
                        @error('born_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-4">
                        <label for="sex" class="form-label">Jenis Kelamin</label>
                        <select class="form-select @error('sex') is-invalid @enderror" name="sex">
                            <option value="" disabled {{ old('sex') ? '' : 'selected' }}>Pilih...</option>
                            <option value="m" {{ old('sex') == 'm' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="f" {{ old('sex') == 'f' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('sex')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3 text-primary">Tanda-Tanda Vital</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tinggi (cm)</label>
                        <input type="number" name="height" value="{{ old('height') }}"
                            class="form-control @error('height') is-invalid @enderror">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Berat (kg)</label>
                        <input type="number" name="weight" value="{{ old('weight') }}"
                            class="form-control @error('weight') is-invalid @enderror">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">Respiration Rate</label>
                        <input type="number" name="respiration_rate" value="{{ old('respiration_rate') }}"
                            class="form-control @error('respiration_rate') is-invalid @enderror">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">Systole</label>
                        <input type="number" name="systole" value="{{ old('systole') }}"
                            class="form-control @error('systole') is-invalid @enderror">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">Diastole</label>
                        <input type="number" name="diastole" value="{{ old('diastole') }}"
                            class="form-control @error('diastole') is-invalid @enderror">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">Heart Rate</label>
                        <input type="number" name="heart_rate" value="{{ old('heart_rate') }}"
                            class="form-control @error('heart_rate') is-invalid @enderror">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">Suhu (°C)</label>
                        <input type="number" step="0.1" name="temperature" value="{{ old('temperature') }}"
                            class="form-control @error('temperature') is-invalid @enderror">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="clinical_notes" class="form-label">Catatan Klinis / Keluhan</label>
                    <textarea class="form-control @error('clinical_notes') is-invalid @enderror" name="clinical_notes" rows="3">{{ old('clinical_notes') }}</textarea>
                    @error('clinical_notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label d-flex justify-content-between align-items-center">
                        Berkas Luar (Opsional)
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="add-file">
                            <i class="bi bi-plus"></i> Tambah File
                        </button>
                    </label>
                    <div id="file-container">
                        <div class="input-group mb-2 file-row">
                            <input type="file" name="patient_files[]" accept="image/*"
                                class="form-control @error('patient_files.*') is-invalid @enderror">
                            <button class="btn btn-outline-danger remove-file" type="button" style="display:none;"><i
                                    class="bi bi-trash"></i></button>
                        </div>
                    </div>
                    @error('patient_files.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="card border border-primary mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Resep Obat</span>
                        <button type="button" class="btn btn-sm btn-light" id="add-medicine">
                            <i class="bi bi-plus-lg"></i> Tambah Item Obat
                        </button>
                    </div>
                    <div class="card-body py-3" id="medicine-container">
                    </div>
                    @error('medicines.*')
                        <div class="px-3 pb-3 text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button class="btn btn-success btn-md px-5" type="submit">
                        <i class="bi bi-check2-circle me-2"></i> Simpan Pemeriksaan
                    </button>
                </div>
            </form>
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

            $(document).on('click', '.remove-file', function() {
                $(this).closest('.file-row').remove();
                toggleFileButtons();
            });
        });
    </script>
@endpush
