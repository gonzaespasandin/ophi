<?php

$foodConditions = [
  "Vegano",
  "Vegetariano",
  "Intolerancia a la lactosa",
  "Intolerancia a la fructosa",
  "Intolerancia a la histamina",
  "Intolerancia a la sacarosa",
  "Intolerancia a la glucosa",
  "Intolerancia a la avena",
  "Intolerancia al gluten",
  "Alergia a huevo",
  "Alergia a leche",
  "Alergia a frutos secos",
  "Alergia a cacahuate",
  "Alergia a soja",
  "Alergia a trigo",
  "Alergia a pescado",
  "Alergia a mariscos",
  "Alergia a sésamo",
  "Alergia a mostaza",
  "Alergia a apio",
  "Alergia a sulfitos",
  "Alergia a colorantes artificiales",
  "Alergia a aditivos químicos",
  "Alergia a crustáceos"
];

?>


<x-layouts.main>
    <x-slot:title>Crear perfil médico</x-slot:title>
    @auth
     <h2>{{$user->name}}, creá tu perfil médico</h2>
    @if($errors->any()) 
        <div>El formulario tiene errores</div>
    @endif

    <form action="{{route('profile.create.store')}}" method="POST">
        @csrf
        <div class="mb-3 text-2xl">
            <label for="title">Condición alimentaria</label>
            <select id="title" name="title">
                @foreach($foodConditions as $foodCondition)
                    <option value="{{ $foodCondition }}">{{ $foodCondition }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit">Crear</button>
    </form>
   @endauth
</x-layouts.main>