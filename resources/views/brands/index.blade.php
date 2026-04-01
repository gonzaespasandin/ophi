<?php

/**
 * @var \Illuminate\Database\Eloquent\Collection $brands
 */
?>

<x-layouts.dashboard>
    <h1>Marcas</h1>

    <form action="{{ route('admin.brands.store') }}" method="post">
        @csrf

        <h2 class="visually-hidden">Añadir nueva marca</h2>
        <div class="input-group my-3">
            <input
                @class([ 'form-control' , 'is-invalid'=> $errors->has('name')
            ])
            type="search"
            name="name"
            placeholder="Nombre de nueva marca"
            aria-label="Nombre de nueva marca"
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

    @if(!count($brands))
    <p>No se encontraron marcas</p>
    @else
    <h2 class="visually-hidden">Listado de marcas</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Marca</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($brands as $brand)
                <tr class="align-middle">
                    <td>{{ $brand['id'] }}</td>
                    <td>{{ $brand['name'] }}</td>
                    <td>
                        <div class="d-flex justify-content-end gap-2 align-items-center">
                            <button
                                class="btn btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#modal-edit-inline"
                                data-name="{{ $brand['name'] }}"
                                data-id="{{ $brand['id'] }}"
                                title="Editar marca"><x-icons.edit /></button>
                            <button
                                class="btn btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#modal-confirm-delete"
                                data-name="{{ $brand['name'] }}"
                                data-id="{{ $brand['id'] }}"
                                title="Eliminar marca"><x-icons.trash /></button>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{ $brands->links() }}

    <x-modal-edit-inline action="{{ route('admin.brands.update') }}" />
    <x-modal-confirm-delete action="{{ route('admin.brands.destroy') }}" />

    <script defer src="{{ url('js/inline-modals.js') }}"></script>
</x-layouts.dashboard>
