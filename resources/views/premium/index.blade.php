<x-layouts.dashboard>

    <div class="d-flex justify-content-between align-items-center">
        <h1>Usuarios premium</h1>
        <a class="btn btn-primary" href="{{ route('admin.premium.give') }}">Otorgar premium</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Correo electrónico</th>
                <th>Monto</th>
                <th>Expira</th>
                <th>Contratado</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($premiumUsers as $user)
                <tr class="align-middle">
                    <td>{{ $user['id'] }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', ['id' => $user['id']]) }}">
                            {{ $user['email'] }}
                        </a>
                    </td>
                    <td>${{$user['subscription']['amount']}}</td>
                    <td>{{ $user['subscription']['expires_at']}} ({{ ceil(now()->floatDiffInDays($user->subscription->expires_at)) }} días)</td>
                    <td>{{ $user['subscription']['updated_at']}}</td>
                    <td><a class="btn btn-danger" href="{{ route('admin.premium.cancel', ['user' => $user['id']])}}"><i class="fa-solid fa-cancel"></i> Cancelar suscripción</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.dashboard>
