<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? ''}} :: Ophi</title>
    <link rel="stylesheet" href="<?= url('css/styles.css');?>">
</head>
<body>
    <nav class="">
        <ul>
            <li><x-nav-link route="home">Home</x-nav-link></li>
            <li><x-nav-link route="auth.register.show">Registrarse</x-nav-link></li>
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
