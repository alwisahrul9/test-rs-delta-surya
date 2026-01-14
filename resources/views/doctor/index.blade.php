@extends('layouts.app')

@push('styles')
    {{-- For DataTable --}}
    <link rel="stylesheet"
        href="{{ asset('assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/table-datatable-jquery.css') }}" />
@endpush

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('patients.create') }}" class="btn btn-primary">
                    Tambah Pasien Baru
                    <i class="bi bi-plus-circle ms-1"></i>
                </a>
                <div class="table-responsive mt-4">
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- For DataTable --}}
    <script src="{{ asset('assets/extensions/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
