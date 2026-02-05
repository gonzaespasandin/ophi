<?php
/**
 * @var \Illuminate\Pagination\LengthAwarePaginator $ingredients
 */
?>

<x-layouts.dashboard>
    <h1>Ingredientes</h1>

    <a href="{{ route('admin.ingredients.create') }}">Añadir nuevo ingrediente</a>

    <form action="{{ route('admin.ingredients') }}" method="get">
        <h2 class="visually-hidden">Buscador</h2>
        <div class="input-group my-3">
            <input
                class="form-control"
                type="search"
                name="q"
                placeholder="Buscar ingrediente por nombre o alias..."
                aria-label="Buscar ingrediente"
                value="{{ $query }}"
                @if($query)
                    autofocus
                @endif
            >
            <button class="btn btn-secondary" aria-label="Buscar"><x-icons.search /></button>
        </div>
    </form>

    @if(!count($ingredients))
        <p>No se encontraron ingredientes</p>
    @else
        <table class="table table-striped table-responsive">
            <thead>
            <tr>
                <th>#</th>
                <th>Ingrediente</th>
                <th>¿Es grupo?</th>
                <th>Alias</th>
                <th>Hijos</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($ingredients as $ingredient)
                <tr class="align-middle">
                    <td>{{ $ingredient['id'] }}</td>
                    <td>{{ $ingredient['name'] }}</td>
                    <td>{{ $ingredient->ingredients()->count() ? 'Si' : 'No' }}</td>
                    <td>{{ $ingredient['aliases'] ? $ingredient['aliases'] : '-' }}</td>
                    <td>{{ join(', ', array_map(fn($i) => $i['name'], $ingredient['ingredients']->toArray())) }}</td>
                    <td class="d-flex gap-2 justify-content-end align-items-center">
                        <a
                            class="btn btn-primary"
                            href="{{ route('admin.ingredients.edit', ['id' => $ingredient['id']]) }}"
                            title="Editar ingrediente"
                        ><x-icons.edit /></a>
                        <button
                            class="btn btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#modal-confirm-delete"
                            data-name="{{ $ingredient['name'] }}"
                            data-id="{{ $ingredient['id'] }}"
                            title="Eliminar ingrediente"
                        ><x-icons.trash /></button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    {{ $ingredients->links() }}

    <x-modal-confirm-delete action="{{ route('admin.ingredients.destroy') }}" />
</x-layouts.dashboard>
