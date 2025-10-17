<div>
    <a class="nav-link {{ request()->routeIs($route) ? 'active' : '' }}" href="<?= route($route);?>">{{ $slot }}</a>
</div>