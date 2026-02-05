<?php
/**
 * @var Illuminate\Database\Eloquent\Model $product
 * @var \Illuminate\Database\Eloquent\Collection $categories
 * @var \Illuminate\Database\Eloquent\Collection $brands
 * @var \Illuminate\Database\Eloquent\Collection $ingredients
 */
?>

<x-layouts.dashboard>
    <x-slot:title>Editar producto "{{$product['name']}}"</x-slot:title>

    <h1>Editar producto</h1>

    <form action="{{ route('admin.products.update', ['id' => $product['id']]) }}" method="post">
        @method('patch')
        @csrf

        <div class="mb-3">
            <label class="form-label" for="name">Nombre del producto</label>
            <input
                id="name"
                @class([
                    "form-control",
                    "is-invalid" => $errors->has('name')
                ])
                type="text"
                name="name"
                value="{{ old('name', $product['name']) }}"
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
            <label class="form-label" for="barcode">Código de barras</label>
            <input
                id="barcode"
                @class([
                    "form-control",
                    "is-invalid" => $errors->has('barcode')
                ])
                type="text"
                name="barcode"
                value="{{ old('barcode', $product['barcode']) }}"
                @error('barcode')
                aria-invalid="true"
                aria-errormessage="barcode-error"
                @enderror
            >
            @error('barcode')
            <p class="small text-danger-emphasis mt-1" id="barcode-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="rnpa">RNPA</label>
            <input
                id="rnpa"
                @class([
                    "form-control",
                    "is-invalid" => $errors->has('rnpa')
                ])
                type="text"
                name="rnpa"
                value="{{ old('rnpa', $product['rnpa']) }}"
                @error('rnpa')
                aria-invalid="true"
                aria-errormessage="rnpa-error"
                @enderror
            >
            @error('rnpa')
            <p class="small text-danger-emphasis mt-1" id="rnpa-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="origin">Origen</label>
            <input
                id="origin"
                @class([
                    "form-control",
                    "is-invalid" => $errors->has('origin')
                ])
                type="text"
                name="origin"
                placeholder="Lugar de origen (ej: Buenos Aires, Mar del Plata, etc...)"
                value="{{ old('origin', $product['origin']) }}"
                @error('origin')
                aria-invalid="true"
                aria-errormessage="origin-error"
                @enderror
            >
            @error('origin')
            <p class="small text-danger-emphasis mt-1" id="origin-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="brand">Marca</label>
            <select
                id="brand"
                @class([
                    "form-select",
                    "is-invalid" => $errors->has('brand')
                ])
                name="brand"
                @error('brand')
                aria-invalid="true"
                aria-errormessage="brand-error"
                @enderror
            >
                <option value="" hidden>Seleccione la marca que corresponda</option>
                @foreach($brands as $brand)
                    <option
                        @selected($brand['id'] == old('brand', $product['brand_id']))
                        value="{{ $brand['id'] }}"
                    >{{ $brand['name'] }}</option>
                @endforeach
                {{-- <option value="new">Añadir nueva marca</option> --}}
            </select>
            @error('brand')
            <p id="brand-error" class="small text-danger-emphasis mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="category">Categoría</label>
            <select
                id="category"
                @class([
                    "form-select",
                    "is-invalid" => $errors->has('category')
                ])
                name="category"
                @error('category')
                aria-invalid="true"
                aria-errormessage="category-error"
                @enderror
            >
                <option value="" hidden>Seleccione la categoría que corresponda</option>
                @foreach($categories as $category)
                    <option
                        @selected($category['id'] == old('category', $product['category_id']))
                        value="{{ $category['id'] }}"
                    >{{ $category['name'] }}</option>
                @endforeach
                {{-- <option value="new">Añadir nueva categoría</option> --}}
            </select>
            @error('category')
            <p id="category-error" class="small text-danger-emphasis mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="ingredients">Ingredientes</label>
            <select
                multiple
                id="ingredients"
                @class([
                    "form-select",
                    "is-invalid" => $errors->has('ingredients')
                ])
                name="ingredients[]"
                @error('ingredients')
                aria-invalid="true"
                aria-errormessage="ingredients-error"
                @enderror
            >
                <option value="" hidden>Seleccione los ingredientes</option>
                @foreach($ingredients as $ingredient)
                    <option
                        value="{{ $ingredient['id'] }}"
                        @selected(in_array($ingredient['id'], old('ingredients', $product->getIngredientIds())))
                    >{{ $ingredient['name'] }}</option>
                @endforeach
                {{-- <option value="new">Añadir nueva categoría</option> --}}
            </select>
            @error('ingredients')
            <p id="ingredients-error" class="small text-danger-emphasis mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button class="btn btn-primary">Actualizar</button>
    </form>

    <script defer src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script defer src="{{ url('js/product-create-form.js') }}"></script>
</x-layouts.dashboard>
