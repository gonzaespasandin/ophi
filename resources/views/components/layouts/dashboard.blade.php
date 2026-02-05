<!doctype html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Panel de Administración' }} | ophi</title>

    {{-- STYLES --}}
    <link rel="stylesheet" href="{{ url('css/global.css') }}">

    {{-- BOOTSTRAP --}}
    <link rel="stylesheet" href="{{ url('css/bootstrap.min.css') }}">
    <script defer src="{{ url('js/bootstrap.min.js') }}"></script>

    {{-- CHOICES (https://github.com/Choices-js/Choices) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <link rel="stylesheet" href="{{ url('css/choices.css') }}">
</head>
<body class="min-vh-100">
    <a class="skip-link" href="#main">Saltar al contenido principal</a>

    <div class="d-flex flex-column min-vh-100">
        <div class="bg-body-secondary">
            <header class="container-xxl d-flex justify-content-between align-items-center py-3">
                <a href="{{ route('admin.index') }}">
                    <img src="{{ url('img/ophi-logo-white.svg') }}" alt="Logo de ophi">
                </a>
                <form class="d-flex align-items-center gap-2" action="{{ route('logout.post') }}" method="post">
                    @csrf
                    <div class="d-flex flex-column align-items-start">
                        <p class="m-0">{{ auth()->user()->name }}</p>
                        <p class="m-0 text-body-tertiary small">{{ auth()->user()->email }}</p>
                    </div>
                    <button class="btn btn-danger">Cerrar sesión</button>
                </form>
            </header>
        </div>

        <div class="container-xxl flex-grow-1 d-flex flex-column">
            <div class="row flex-grow-1">
                <div class="col-2 p-0">
                    <nav class="container-fluid px-0 h-100 bg-body-tertiary" aria-label="Navegación principal">
                        <div class="list-group list-group-flush">
                            <x-nav-link route="admin.index">Inicio</x-nav-link>
                            <x-nav-link route="admin.products">Productos</x-nav-link>
                            <x-nav-link route="admin.brands">Marcas</x-nav-link>
                            <x-nav-link route="admin.categories">Categorías</x-nav-link>
                            <x-nav-link route="admin.ingredients">Ingredientes</x-nav-link>
                            <x-nav-link route="admin.premium">Plan Premium</x-nav-link>
                            <x-nav-link route="admin.users">Usuarios</x-nav-link>
                        </div>
                    </nav>
                </div>
                <div class="col p-0">
                    <main id="main" class="bg-body container-fluid h-100 pt-2">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
