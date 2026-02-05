<?php
/**
 * @var \Illuminate\Pagination\LengthAwarePaginator $users
 * @var string $query
 */
?>

<x-layouts.dashboard>
    <x-slot:title>Usuarios</x-slot:title>

    <h1>Listado de usuarios</h1>

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
        <table class="table table-striped table-responsive">
            <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Correo electrónico</th>
                <th>Rol</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr class="align-middle">
                    <td>{{ $user['id'] }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', ['user' => $user['id']]) }}">
                            {{ $user['name'] }}
                        </a>
                    </td>
                    <td>{{ $user['email'] }}</td>
                    <td>{{ __($user['role']) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    {{ $users->links() }}
</x-layouts.dashboard>
