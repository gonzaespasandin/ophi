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
            <img src="/img/ophi-logo-white.svg" alt="Logo de ophi">
        </div>
        <div class="d-flex justify-content-around align-items-center">
            <form action="{{ route('login.post') }}" method="post" class="">
                @csrf

                @if (session()->has('feedback.message'))
                    <div
                        @class([
                            "alert",
                            "alert-warning" => session()->get('feedback.type') === "warning",
                            "alert-danger" => session()->get('feedback.type') === "danger",
                            "alert-success" => session()->get('feedback.type') === "success",
                            "alert-info" => session()->get('feedback.type') === "info",
                        ])
                    >
                        {{ session()->get('feedback.message') }}
                    </div>
                @endif

                <h1>Iniciá sesión</h1>

                <div class="mb-3">
                    <label class="form-label" for="email">Correo electrónico</label>
                    <input
                        id="email"
                        @class([
                            "form-control",
                            "is-invalid" => $errors->has('email')
                        ])
                        type="text"
                        name="email"
                        autofocus
                        value="{{ old('email') }}"
                        @error('email')
                        aria-invalid="true"
                        aria-errormessage="email-error"
                        @enderror
                    >
                    @error('email')
                    <p class="small text-danger mt-1" id="email-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">Contraseña</label>
                    <input
                        id="password"
                        @class([
                            "form-control",
                            "is-invalid" => $errors->has('password')
                        ])
                        type="password"
                        name="password"
                        value="{{ old('password') }}"
                        @error('password')
                        aria-invalid="true"
                        aria-errormessage="password-error"
                        @enderror
                    >
                    @error('password')
                    <p class="small text-danger mt-1" id="password-error">{{ $message }}</p>
                    @enderror
                </div>

                <button>Iniciar sesión</button>
            </form>
        </div>

    </div>
</body>
</html>

