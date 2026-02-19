<?php
/**
 * @var \Illuminate\Pagination\LengthAwarePaginator $products
 * @var string $query
 */
?>

<x-layouts.dashboard>
    <x-slot:title>Productos</x-slot:title>

    <div class="d-flex justify-content-between align-items-center">
        <h1>Productos</h1>
        <a class="btn btn-primary" href="{{ route('admin.products.create') }}"><i class="fa-solid fa-plus"></i> Añadir nuevo producto</a>
    </div>

    <form action="{{ route('admin.products') }}" method="get">
        <h2 class="visually-hidden">Buscador</h2>
        <div class="input-group my-3">
            <input
                class="form-control"
                type="search"
                name="q"
                placeholder="Buscar producto por nombre..."
                aria-label="Buscar producto"
                value="{{ $query }}"
                @if($query)
                    autofocus
                @endif
            >
            <button class="btn btn-secondary" aria-label="Buscar"><x-icons.search /></button>
        </div>
    </form>

    @if(!count($products))
        <p>No se encontraron productos</p>
    @else
        <table class="table table-striped table-responsive">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Marca</th>
                    <th>Origen</th>
                    <th>Categoría</th>
                    <th>Código de barras</th>
                    <th>RNPA</th>
                    <th>Ingredientes</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr class="align-middle">
                        {{-- IMG - Ingredients --}}
                        <td>{{ $product['name'] }}</td>
                        <td>{{ $product['brand']['name'] }}</td>
                        <td>{{ $product['origin'] }}</td>
                        <td>{{ $product['category']['name'] }}</td>
                        <td>{{ $product['barcode'] }}</td>
                        <td>{{ $product['rnpa'] }}</td>
                        <td>{{ join(', ', array_map(fn($i) => $i['name'], $product['ingredients']->toArray())) }}</td>
                        <td>
                            <div class="d-flex gap-2 justify-content-end align-items-center">
                                <a
                                    class="btn btn-primary"
                                    href="{{ route('admin.products.edit', ['id' => $product['id']]) }}"
                                    title="Editar producto"
                                ><x-icons.edit /></a>
                                <button
                                    class="btn btn-danger"
                                    title="Eliminar producto"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-confirm-delete"
                                    data-id="{{ $product['id'] }}"
                                    data-name="{{ $product['name'] }}"
                                ><x-icons.trash /></button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{ $products->links() }}

   <x-modal-confirm-delete action="{{ route('admin.products.destroy') }}" />
</x-layouts.dashboard>
