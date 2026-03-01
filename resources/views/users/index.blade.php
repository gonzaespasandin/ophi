<?php
/**
 * @var \Illuminate\Pagination\LengthAwarePaginator $users
 * @var string $query
 */
?>

<x-layouts.dashboard>
    <x-slot:title>Usuarios</x-slot:title>

    <h1>Usuarios registrados</h1>

    <form action="{{ route('admin.users') }}" method="get">
        <h2 class="visually-hidden">Buscador</h2>
        <div class="input-group my-3">
            <input
                class="form-control"
                type="search"
                name="q"
                placeholder="Buscar usuario por nombre o correo electrónico..."
                aria-label="Buscar usuario"
                value="{{ $query }}"
                @if($query)
                    autofocus
                @endif
            >
            <button class="btn btn-secondary" aria-label="Buscar"><x-icons.search /></button>
        </div>
    </form>

    @if(!count($users))
        <p>No se encontraron usuarios</p>
    @else
        <h2 class="visually-hidden">Listado completo</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Correo electrónico</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr class="align-middle">
                        <td>{{ $user['id'] }}</td>
                        <td>
                            <a href="{{ route('admin.users.show', ['id' => $user['id']]) }}">
                                {{ $user['name'] }}
                            </a>
                        </td>
                        <td>{{ $user['email'] }}</td>
                        <td>{{ __($user['role']) }}</td>
                        <td>
                            <div class="d-flex align-items-center justify-content-end">
                                @if(auth()->user()->id === $user['id'])
                                    <p>(Usuario actual)</p>
                                @else
                                    <a class="btn btn-primary" href="{{ route('admin.users.edit', ['id' => $user['id']])}}"><i class="fa-solid fa-pen-to-square"></i> Editar rol</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{ $users->links() }}
</x-layouts.dashboard>
