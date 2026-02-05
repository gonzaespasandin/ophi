<?php
/**
 * @var \Illuminate\Database\Eloquent\Model $user
 */
?>

<x-layouts.dashboard>
    <h1>{{ $user['name'] }}</h1>
    <p>{{__($user['role'])}} - {{ $user['email'] }}</p>
</x-layouts.dashboard>
