<?php
/**
 * @var \Illuminate\Database\Eloquent\Collection $users All users with the free-plan
 */
?>

<x-layouts.dashboard>
    <x-slot:title>Dar premium</x-slot:title>

    <h1>¿A qué usuario te gustaría darle premium?</h1>

    <form action="{{ route('admin.premium.upgrade') }}" method="post">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="user">Usuario</label>
            <select
                id="user"
                @class([
                    "form-select",
                    "is-invalid" => $errors->has('user')
                ])
                name="user"
                @error('user')
                aria-invalid="true"
                aria-errormessage="user-error"
                @enderror
            >
                <option value="" hidden>Seleccione al usuario</option>
                @foreach($users as $user)
                    <option
                        @selected($user['id'] == old('user', -1))
                        value="{{ $user['id'] }}"
                    >{{ $user['name'] }}</option>
                @endforeach
                {{-- <option value="new">Añadir nueva marca</option> --}}
            </select>
            @error('user')
            <p id="user-error" class="small text-danger-emphasis mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button class="btn btn-primary">Dar Premium</button>
    </form>
</x-layouts.dashboard>
