<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- STYLES --}}
    <link rel="stylesheet" href="{{ url('css/global.css') }}">

    {{-- BOOTSTRAP --}}
    <link rel="stylesheet" href="{{ url('css/bootstrap.min.css') }}">
    <script defer src="{{ url('js/bootstrap.min.js') }}"></script>
    <title>Admin de ophi</title>
</head>
<body>
    <div id="login" class="d-flex justify-content-around align-items-center">
        <div class="d-flex justify-content-around align-items-center">
            <img src="/img/ophi-logo-white.svg">
        </div>
        <div class="d-flex justify-content-around align-items-center">
            <form action="{{ route('login.post') }}" method="post" class="">
                @csrf

                @if (session()->has('feedback.message'))
                    <div>
                        <p>Type: {{ session()->get('feedback.type') }}</p>
                        {{ session()->get('feedback.message') }}
                    </div>
                @endif
                <span>Iniciá sesión</span>
                <div class="my-4">
                    <label for="email" class="d-block">Email</label>
                    <input type="text" id="email" name="email">
                </div>

                <div class="mb-4">
                    <label for="password" class="d-block">Contraseña</label>
                    <input type="password" id="password" name="password">
                </div>

                <button>Iniciar sesión</button>
            </form>
        </div>
        
    </div>
</body>
</html>

