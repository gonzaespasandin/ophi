<form action="{{ route('login.post') }}" method="post">
    @csrf

    @if (session()->has('feedback.message'))
        <div>
            <p>Type: {{ session()->get('feedback.type') }}</p>
            {{ session()->get('feedback.message') }}
        </div>
    @endif

    <div>
        <label for="email">Email</label>
        <input type="text" id="email" name="email">
    </div>

    <div>
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password">
    </div>

    <button>Iniciar sesión</button>
</form>
