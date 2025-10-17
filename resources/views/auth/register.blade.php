<x-layouts.main>
    <x-slot:title>Registrarse</x-slot:title>
    <h2>Registrarse</h2>
    @if($errors->any()) 
        <div>El formulario tiene errores</div>
    @endif

    <form action="{{route('auth.register.process')}}" method="POST">
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
            <label for="name">Nombre</label>
            <input 
            type="name" 
            id="name" 
            name="name">
            @error('name')
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

        <button type="submit">Registrarse</button>
    
    </form>
</x-layouts.main>