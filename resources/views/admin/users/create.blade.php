@extends('layouts.app')

@section('content')
    <div class="card">
        <a href="{{ route('admin.home') }}" class="btn btn-primary rounded-lg w-25 m-3">
            <i class="bi bi-arrow-left me-2"></i>
            Kembali
        </a>
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror" name="name" id="name">
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror" name="email"
                        placeholder="user@example.com" id="email">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="phone" class="form-label">Nomor Handphone</label>
                    <input type="phone" value="{{ old('phone') }}"
                        class="form-control @error('phone') is-invalid @enderror" name="phone" placeholder="081xxxxxxxxx"
                        id="phone">
                    @error('phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                        id="password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="role" class="form-label">Jenis Akun</label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role"
                        aria-label="Default select example">
                        <option selected disabled>Pilih Jenis</option>
                        @foreach ($getRoles as $item)
                            <option value="{{ $item }}" @selected(old('role') == $item)>{{ Str::title($item) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div id="forDoctor" class="visually-hidden">
                    <div class="mb-4">
                        <label for="specialization" class="form-label">Spesialisasi</label>
                        <select class="form-select @error('specialization') is-invalid @enderror" id="specialization"
                            name="specialization" aria-label="Default select example">
                            <option selected disabled>Pilih Jenis</option>
                            <option value="anak" @selected(old('specialization') == 'anak')>Anak</option>
                            <option value="penyakit dalam" @selected(old('specialization') == 'penyakit dalam')>Penyakit Dalam</option>
                            <option value="lainnya" @selected(old('specialization') == 'lainnya')>Lainnya</option>
                        </select>
                        @error('specialization')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="str_number" class="form-label">Nomor STR</label>
                        <input type="text" value="{{ old('str_number') }}"
                            class="form-control @error('str_number') is-invalid @enderror" name="str_number"
                            id="str_number">
                        @error('str_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="signature" class="form-label">Tanda Tangan</label>
                        <input type="file" accept="image/*" class="form-control @error('signature') is-invalid @enderror"
                            name="signature" id="signature">
                        @error('signature')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div id="forPharmacist" class="visually-hidden">
                    <div class="mb-4">
                        <label for="work_unit" class="form-label">Unit Kerja</label>
                        <select class="form-select @error('work_unit') is-invalid @enderror" id="work_unit"
                            name="work_unit" aria-label="Default select example">
                            <option selected disabled>Unit Kerja</option>
                            <option value="rawat jalan" @selected(old('work_unit') == 'rawat jalan')>Rawat Jalan</option>
                            <option value="farmasi igd" @selected(old('work_unit') == 'farmasi igd')>Farmasi IGD</option>
                            <option value="lainnya" @selected(old('work_unit') == 'lainnya')>Lainnya</option>
                        </select>
                        @error('work_unit')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="sipa_number" class="form-label">Nomor SIPA</label>
                        <input type="text" value="{{ old('sipa_number') }}"
                            class="form-control @error('sipa_number') is-invalid @enderror" name="sipa_number"
                            id="sipa_number">
                        @error('sipa_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-success d-block ms-auto w-25" type="submit">
                    <i class="bi bi-floppy m-0 me-1"></i>
                    Simpan
                </button>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            function toggleFields(role) {
                if (role === 'doctor') {
                    // Tampilkan form Dokter, Sembunyikan form Apoteker
                    $("#forDoctor").removeClass("visually-hidden");
                    $("#forPharmacist").addClass("visually-hidden");

                    // Bersihkan input Apoteker agar tidak terkirim ke backend
                    $("#forPharmacist").find('input').val('');
                    $("#forPharmacist").find('select').val($("#forPharmacist").find('select option:first').val());
                } else if (role === 'pharmacist') {
                    // Tampilkan form Apoteker, Sembunyikan form Dokter
                    $("#forPharmacist").removeClass("visually-hidden");
                    $("#forDoctor").addClass("visually-hidden");

                    // Bersihkan input Dokter agar tidak terkirim ke backend
                    $("#forDoctor").find('input').val('');
                    $("#forDoctor").find('select').val($("#forDoctor").find('select option:first').val());
                    // Khusus file input tidak bisa di-reset valuenya ke string, tapi bisa di-clear
                    $("#signature").val('');
                } else {
                    // Jika Admin, sembunyikan semua dan bersihkan semua
                    $("#forDoctor, #forPharmacist").addClass("visually-hidden");
                    $("#forDoctor, #forPharmacist").find('input').val('');
                    $("#forDoctor, #forPharmacist").find('select').val($("option:first").val());
                }
            }

            // Jalankan saat pertama kali load (handle old values)
            let getSession = {{ Js::from(old('role')) }};
            if (getSession) {
                toggleFields(getSession);
            }

            // Jalankan saat role diubah
            $("#role").change(function() {
                toggleFields($(this).val());
            });
        })
    </script>
@endpush
