<div class="d-flex gap-3">
    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary me-2">
        <i class="bi bi-pencil-square"></i>
    </a>
    <a href="{{ route('users.destroy', $user->id) }}" class="btn btn-danger" data-confirm-delete="true">
        <i class="bi bi-trash"></i>
    </a>
</div>
