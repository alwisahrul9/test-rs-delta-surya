@extends('layouts.app')

@section('content')
    <div class="container-fluid">
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
                                <th>Penanganan Oleh</th>
                                <th>Status Bayar</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patient->examinations as $exam)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($exam->examination_time)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $exam->user->name }}</td>
                                    <td>
                                        @php $status = $exam->prescription->status ?? 'pending'; @endphp
                                        <span class="badge {{ $status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                                            {{ strtoupper($status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if ($status == 'pending')
                                            @if (auth()->user()->hasRole('pharmacist'))
                                                <a href="{{ route('print.prescription', $exam->prescription->id) }}" class="btn btn-success me-2">
                                                    <i class="bi bi-printer"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('patients.edit', $patient->id) }}?exam_id={{ $exam->id }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>
                                            @endif
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
