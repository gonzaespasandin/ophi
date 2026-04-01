<?php
/**
 * @var string $action
 */
?>

<div id="modal-confirm-delete" class="modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Confirmar eliminación</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que querés eliminar "<span id="modal-confirm-delete-input-name" class="fw-semibold"></span>"?</p>
            </div>
            <form class="modal-footer" action="{{ $action }}" method="post">
                @method('delete')
                @csrf

                <input id="modal-confirm-delete-input-id" type="hidden" name="id">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Sí, quiero eliminarlo</button>
            </form>
        </div>
    </div>
</div>

@once
    <script defer src="{{ url('js/modal-confirm-delete.js') }}"></script>
@endonce
