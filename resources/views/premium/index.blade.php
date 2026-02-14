<x-layouts.dashboard>
    <h1>Usuarios premium</h1>
    <div class=" table-responsive">
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
                    <td>{{ $user['email'] }}</td>
                    <td>${{$user['subscription']['amount']}}</td>
                    <td>{{ $user['subscription']['expires_at']}} ({{ ceil(now()->floatDiffInDays($user->subscription->expires_at)) }} días)</td>
                    <td>{{ $user['subscription']['updated_at']}}</td>
                    <td><a href="{{ route('admin.users.edit', ['id' => $user['id']])}}">Cancelar suscripción</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.dashboard>
 