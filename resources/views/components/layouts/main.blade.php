<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? ''}} :: Ophi</title>
    <link rel="stylesheet" href="<?= url('css/styles.css');?>">
    {{ $head ?? '' }}
</head>
<body>
    <nav class="">
        <ul>
            @guest
                <li><x-nav-link route="auth.register.show">Registrarse</x-nav-link></li>
                <li><x-nav-link route="auth.login.show">Iniciar sesión</x-nav-link></li>
            @endguest
            @auth
                <li><x-nav-link route="home">Home</x-nav-link></li>
                <li><x-nav-link route="scanner.index">Escaner</x-nav-link></li>
                <li><x-nav-link route="profile.userProfile">Mi perfil</x-nav-link></li>
                <li>
                    <form action="{{ route('auth.logout.process') }}" method="POST">
                        @csrf
                        <button type="submit">Cerrar sesión</button>
                    </form>
                </li>
            @endauth
        </ul>
    </nav>
    <h1>Ophi</h1>

    <main class="container">
        {{-- Renderización de las views --}}
        {{ $slot }}
    </main>
    <footer>
        <p>Da vinci &copy; 2025</p>
    </footer>
</body>
</html>
