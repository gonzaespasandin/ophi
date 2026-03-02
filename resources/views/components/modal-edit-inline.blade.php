<?php
/**
 * @var string $action Form > action
 */
?>

<div id="modal-edit-inline" class="modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="{{ $action }}" method="post">
            @method('put')
            @csrf
            {{-- TODO: Abrir el formulario cuando hay error de validación, y que no se confunda con el form de añadir --}}

            <div class="modal-header">
                <h2 class="modal-title">Editar</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input id="modal-edit-inline-input-id" type="hidden" name="id">
                <label for="modal-edit-inline-input-name">Nombre</label>
                <input
                    id="modal-edit-inline-input-name"
                    @class([
                        'form-control',
                        'is-invalid' => $errors->has('name')
                    ])
                    type="text" name="name">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>
