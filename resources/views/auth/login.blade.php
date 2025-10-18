<x-layouts.main>
    <x-slot:title>Iniciar sesión</x-slot:title>
    <h2>Iniciar sesión</h2>
    @if($errors->any()) 
        <div>El formulario tiene errores</div>
    @endif

    <form action="{{route('auth.login.process')}}" method="POST">
        @csrf
        <div class="mb-3 text-2xl">
            <label for="email">Email</label>
            <input 
            type="email" 
            id="email" 
            name="email">
            @error('email')
                <div>{{ $message }}</div>
            @endif

        </div>
        <div class="mb-3">
            <label for="password">Contraseña</label>
            <input 
            type="password" 
            id="password" 
            name="password">
            @error('password')
                <div>{{ $message }}</div>
            @endif
        </div>

        <button type="submit">Iniciar sesión</button>
    
    </form>
</x-layouts.main>