<div class="d-flex justify-content-center gap-3">
    <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-success me-2">
        <i class="bi bi-eye-fill"></i>
    </a>
    @if (auth()->user()->hasRole('admin'))
        <a href="{{ route('patients.destroy', $patient->id) }}" class="btn btn-danger" data-confirm-delete="true">
            <i class="bi bi-trash"></i>
        </a>
    @endif
</div>