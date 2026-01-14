@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <form action="{{ route('patients.update', $patient->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="exam_id" value="{{ $latestExam->id }}">

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 text-white">Pemeriksaan Hari Ini</h5>
                        </div>
                        <div class="card-body mt-3">
                            {{-- Identitas Pasien --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Pasien</label>
                                    <input type="text" class="form-control" name="name" value="{{ $patient->name }}"
                                        readonly>
                                </div>
                                <div class="col">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="text" class="form-control"
                                        value="{{ \Carbon\Carbon::parse($patient->born_date)->translatedFormat('d F Y') }}"
                                        name="born_date" readonly>
                                </div>
                                <div class="col">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <input type="text" class="form-control"
                                        value="{{ $patient->sex === 'm' ? 'Laki-Laki' : 'Perempuan' }}" readonly>
                                    <input type="hidden" class="form-control" value="{{ $patient->sex }}" name="sex"
                                        readonly>
                                </div>
                            </div>

                            {{-- Tanda Vital --}}
                            <div class="row">
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
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">{{ $label }}</label>
                                        <input type="number" step="0.1" name="{{ $key }}"
                                            class="form-control @error($key) is-invalid @enderror"
                                            value="{{ old($key, $latestExam->$key ?? '') }}">
                                        @error($key)
                                            <div class="px-3 pb-3 text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catatan Klinis Terbaru</label>
                                <textarea name="clinical_notes" class="form-control" rows="4">{{ old('clinical_notes', $latestExam->clinical_notes ?? '') }}</textarea>
                            </div>

                            <hr>

                            {{-- Fitur Upload & Hapus Gambar (Berkas Luar) --}}
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center">
                                    Berkas Pemeriksaan Luar
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="add-file">
                                        <i class="bi bi-plus"></i> Tambah File Baru
                                    </button>
                                </label>

                                @if ($latestExam && $latestExam->patientFiles->count() > 0)
                                    <div class="row mb-3 bg-light p-3 rounded border">
                                        <div class="col-12"><small class="text-muted d-block mb-2">Berkas tersimpan (Centang
                                                untuk hapus):</small></div>
                                        @foreach ($latestExam->patientFiles as $file)
                                            <div class="col-md-3 col-sm-6 mb-2 text-center">
                                                <div class="position-relative border rounded p-2 bg-white">
                                                    <img src="{{ asset('storage/patient-files/' . $file->file) }}"
                                                        class="img-fluid rounded mb-2"
                                                        style="height: 100px; object-fit: cover; cursor: pointer;"
                                                        onclick="opentab('{{ asset('storage/patient-files/' . $file->file) }}')">
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input me-2" type="checkbox"
                                                            name="delete_files[]" value="{{ $file->id }}"
                                                            id="file-{{ $file->id }}">
                                                        <label class="form-check-label text-danger small"
                                                            for="file-{{ $file->id }}">Hapus</label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div id="file-container">
                                    <div class="input-group mb-2 file-row">
                                        <input type="file" name="patient_files[]" accept="image/*"
                                            class="form-control @error('patient_files.*') is-invalid @enderror">
                                        <button class="btn btn-outline-danger remove-file" type="button"
                                            style="display:none;"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                                @error('patient_files.*')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="card border border-primary mb-4">
                                <div
                                    class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
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
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0 text-white">Riwayat Medis</h5>
                        </div>
                        <div class="card-body overflow-auto pt-3" style="max-height: 80vh;">
                            <div class="accordion" id="historyAccordion">
                                @foreach ($patient->examinations as $exam)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed py-2" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#exam{{ $exam->id }}">
                                                <small>{{ \Carbon\Carbon::parse($exam->latestExam_time)->format('d/m/Y') }}</small>
                                                <span
                                                    class="ms-auto badge {{ $exam->prescription->status == 'paid' ? 'bg-success' : 'bg-warning' }} btn-sm">
                                                    {{ Str::title($exam->prescription->status) ?? 'Pending' }}
                                                </span>
                                            </button>
                                        </h2>
                                        <div id="exam{{ $exam->id }}" class="accordion-collapse collapse"
                                            data-bs-parent="#historyAccordion">
                                            <div class="accordion-body small">
                                                <strong>Catatan:</strong> {{ $exam->clinical_notes }}<br>
                                                <strong>Diperiksa Oleh:</strong> {{ $exam->user->name }}<br>
                                                <strong>Obat:</strong>
                                                <ul class="ps-3 mb-0">
                                                    @foreach ($exam->prescription->prescriptionDetails as $detail)
                                                        <li class="border-bottom py-2">{{ $detail->medicine_name }}
                                                            x{{ $detail->qty }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 text-end">
                <button type="submit" class="btn btn-success px-5">Simpan Perubahan</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const medicineApiUrl = "{{ route('proxy.medicines') }}";

            function initChoices(element, defaultValue = null) {
                const choices = new Choices(element, {
                    searchEnabled: true,
                    shouldSort: false,
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

            // --- Logika Pengisian Data (Data Terintegrasi) ---
            @if (old('medicines'))
                @foreach (old('medicines') as $index => $mId)
                    addMedicineRow('{{ $mId }}', '{{ old('quantities')[$index] }}');
                @endforeach
            @elseif (isset($latestExam->prescription->prescriptionDetails) &&
                    $latestExam->prescription->prescriptionDetails->count() > 0)
                @foreach ($latestExam->prescription->prescriptionDetails as $detail)
                    addMedicineRow('{{ $detail->medicine_id }}_{{ $detail->medicine_name }}',
                        '{{ $detail->qty }}');
                @endforeach
            @else
                addMedicineRow();
            @endif

            // Handlers
            $("#add-medicine").click(() => addMedicineRow());
            $(document).on('click', '.remove-medicine', function() {
                $(this).closest('.medicine-row').remove();
                toggleMedicineButtons();
            });

            function toggleMedicineButtons() {
                $('.remove-medicine').toggle($('.medicine-row').length > 1);
            }

            // --- File Handlers ---
            $("#add-file").click(function() {
                $("#file-container").append(`
                <div class="input-group mb-2 file-row">
                    <input type="file" name="patient_files[]" accept="image/*" class="form-control">
                    <button class="btn btn-outline-danger remove-file" type="button"><i class="bi bi-trash"></i></button>
                </div>
            `);
                $('.remove-file').show();
            });

            $(document).on('click', '.remove-file', function() {
                $(this).closest('.file-row').remove();
                if ($('.file-row').length === 1) $('.remove-file').hide();
            });
        });
    </script>
    <script>
        function callApi() {
            return new Promise(function(resolve) {
                setTimeout(function() {
                    resolve();
                }, 1000);
            });
        }

        function opentab(url) {
            var page = window.open('url', "_blank", "width=1000,height=1000");
            page.document.write("<p>Loading....</p>");
            callApi()
                .then(function() {
                    page.location.href = `${url}`;
                })
                .catch(function() {
                    page.close();
                });
        }
    </script>
@endpush
