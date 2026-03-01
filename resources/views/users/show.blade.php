<?php
/**
 * @var \Illuminate\Database\Eloquent\Model $user
 */
?>

<x-layouts.dashboard>
    <div class="user-card">
    <div class="user-header">
        <h1 class="user-name">{{$user->name}}</h1>
    </div>

    <div class="user-body">
        <div class="user-row">
            <span class="label">Email</span>
            <span class="value">{{$user->email}}</span>
        </div>

        <div class="user-row">
            <span class="label">Rol</span>
            <span class="value">{{$user->role}}</span>
        </div>

        <div class="user-row">
            <span class="label">Plan</span>
            <span class="value plan active">{{$user->subscription->plan->plan}}</span>
            <!-- alternativo -->
            <!-- <span class="value plan inactive">Sin suscripción</span> -->
        </div>

        <div class="user-row">
            <span class="label">Se unió en</span>
            <span class="value plan active">{{$user->created_at}}</span>
        </div>
    </div>
</div>

</x-layouts.dashboard>
