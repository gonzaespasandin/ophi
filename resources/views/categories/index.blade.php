<?php

/**
 * @var \Illuminate\Database\Eloquent\Collection $categories
 */
?>

<x-layouts.dashboard>
    <h1>Categorías</h1>

    <form action="{{ route('admin.categories.store') }}" method="post">
        @csrf

        <h2 class="visually-hidden">Añadir nueva categoría</h2>
        <div class="input-group my-3">
            <input
                @class([ 'form-control' , 'is-invalid'=> $errors->has('name')
            ])
            type="search"
            name="name"
            placeholder="Nombre de nueva categoría"
            aria-label="Nombre de nueva categoría"
            value="{{ old('name') }}"
            @error('name')
            aria-invalid="true"
            aria-errormessage="name-error"
            @enderror
            >
            <button class="btn btn-secondary">Añadir</button>
        </div>

        @error('name')
        <p id="name-error" class="small text-danger-emphasis">{{ $message }}</p>
        @enderror
    </form>

    @if(!count($categories))
    <p>No se encontraron categorías</p>
    @else
    <h2 class="visually-hidden">Listado completo</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Categoría</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($categories as $category)
                <tr class="align-middle">
                    <td>{{ $category['id'] }}</td>
                    <td>{{ $category['name'] }}</td>
                    <td>
                        <div class="d-flex gap-2 align-items-center">
                            <button
                                class="btn btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#modal-edit-inline"
                                data-name="{{ $category['name'] }}"
                                data-id="{{ $category['id'] }}"
                                title="Editar categoría"><x-icons.edit /></button>
                            <button
                                class="btn btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#modal-confirm-delete"
                                data-name="{{ $category['name'] }}"
                                data-id="{{ $category['id'] }}"
                                title="Eliminar categoría"><x-icons.trash /></button>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{ $categories->links() }}

    <x-modal-edit-inline action="{{ route('admin.categories.update') }}" />
    <x-modal-confirm-delete action="{{ route('admin.categories.destroy') }}" />

    <script defer src="{{ url('js/inline-modals.js') }}"></script>
</x-layouts.dashboard>
