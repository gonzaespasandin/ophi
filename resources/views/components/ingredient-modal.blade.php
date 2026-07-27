<div class="modal fade" id="modal-new-ingredient" tabindex="-1" aria-labelledby="modal-new-ingredient-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-new-ingredient-label">Nuevo ingrediente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="new-ingredient-name">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="new-ingredient-name" placeholder="Ej: Lactosa">
                    <p class="small text-danger mt-1 d-none" id="new-ingredient-name-error">El nombre es obligatorio</p>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="new-ingredient-aliases">Aliases <span class="text-muted">(opcional)</span></label>
                    <input type="text" class="form-control" id="new-ingredient-aliases" placeholder="Ej: leche, lácteo">
                </div>
                <p class="text-danger d-none" id="new-ingredient-server-error">Error al crear el ingrediente.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="new-ingredient-submit">Crear ingrediente</button>
            </div>
        </div>
    </div>
</div>