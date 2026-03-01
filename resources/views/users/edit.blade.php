<x-layouts.dashboard>
    <h1>Editar rol de usuario</h1>

    <form action="{{ route('admin.users.update', ['id' => $user['id']]) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label" for="role">Rol de {{ $user->name }}</label>
            <select
                id="role"
                class="form-select"
                name="role"
            >
                <option value="{{$user->role}}" hidden>{{$user->role}}</option>
                <option value="<?= ($user->role == 'admin' ? 'user' : 'admin') ?>">{{ ($user->role == 'admin' ? 'user' : 'admin') }}</option>
            </select>
        </div>

        <button class="btn btn-primary">Guardar</button>
    </form>
</x-layouts.dashboard>
