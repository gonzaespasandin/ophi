<?php
/**
 * @var string $route
 */
?>

<a
    href="{{ route($route) }}"
    @class([
        'list-group-item border-transparent',
        'bg-body-tertiary' => !request()->routeIs($route),
        'active' => request()->routeIs($route)
    ])
    @if(request()->routeIs($route))
        aria-current="page"
    @endif
>{{ $slot }}</a>
