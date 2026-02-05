<?php
/**
 * @var \Illuminate\Database\Eloquent\Collection $ingredients
 * @var \Illuminate\Database\Eloquent\Collection $ingredient
 * @var array $childrenIds
 * @var array $parentIds
 */
?>

<x-layouts.dashboard>
    <h1>Editar nuevo ingrediente</h1>

    <form action="{{ route('admin.ingredients.update', ['id' => $ingredient['id']]) }}" method="post">
        @method('patch')
        @csrf

        <div class="mb-3">
            <label class="form-label" for="name">Nombre</label>
            <input
                id="name"
                @class([
                    "form-control",
                    "is-invalid" => $errors->has('name')
                ])
                type="text"
                name="name"
                value="{{ old('name', $ingredient['name']) }}"
                @error('name')
                aria-invalid="true"
                aria-errormessage="name-error"
                @enderror
            >
            @error('name')
            <p class="small text-danger-emphasis mt-1" id="name-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label" for="aliases">Alias</label>
            <textarea
                id="aliases"
                @class([
                    "form-control",
                    "is-invalid" => $errors->has('aliases')
                ])
                type="text"
                name="aliases"
                placeholder="Separados por coma"
                @error('aliases')
                aria-invalid="true"
                aria-errormessage="aliases-error"
                @enderror
            >{{ old('aliases', $ingredient['aliases']) }}</textarea>
            @error('aliases')
            <p class="small text-danger-emphasis mt-1" id="aliases-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label" id="ingredients-label" for="ingredient-children">Este ingrediente es padre de...</label>
            <select
                multiple
                id="ingredient-children"
                @class([
                    "form-select",
                    "is-invalid" => $errors->has('ingredient-children')
                ])
                name="ingredient-children[]"
                @error('ingredient-children')
                aria-invalid="true"
                aria-errormessage="ingredient-children-error"
                @enderror
            >
                <option value="" hidden>Seleccione los ingredientes</option>
                @foreach($ingredients as $ingredient)
                    <option
                        value="{{ $ingredient['id'] }}"
                        @selected(in_array($ingredient['id'], old('ingredient-children', $childrenIds)))
                    >{{ $ingredient['name'] }}</option>
                @endforeach
            </select>
            @error('ingredient-children')
            <p id="ingredient-children-error" class="small text-danger-emphasis mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label" id="ingredients-label" for="ingredient-parent">Este ingrediente es hijo de...</label>
            <select
                multiple
                id="ingredient-parent"
                @class([
                    "form-select",
                    "is-invalid" => $errors->has('ingredient-parent')
                ])
                name="ingredient-parent[]"
                @error('ingredient-parent')
                aria-invalid="true"
                aria-errormessage="ingredient-parent-error"
                @enderror
            >
                <option value="" hidden>Seleccione los ingredientes</option>
                @foreach($ingredients as $ingredient)
                    <option
                        value="{{ $ingredient['id'] }}"
                        @selected(in_array($ingredient['id'], old('ingredient-children', $parentIds)))
                    >{{ $ingredient['name'] }}</option>
                @endforeach
            </select>
            @error('ingredient-parent')
            <p id="ingredient-parent-error" class="small text-danger-emphasis mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button class="btn btn-primary">Añadir</button>
    </form>

    <script defer src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script defer src="{{ url('js/ingredient-create-form.js') }}"></script>
</x-layouts.dashboard>
