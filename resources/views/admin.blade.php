<x-layouts.dashboard>

  <div class="dashboard">


  <div class="main">
    <div class="header border-bottom">
      <h1>Dashboard</h1>
      <div class="user">
        <i class="fa-solid fa-user"></i>
      </div>
    </div>

    <!-- Cards -->
    <section class="cards">
      <div class="card">
        <i class="fa-solid fa-users"></i>
        <div>
          <h3 class="fw-normal">Usuarios</h3>
          <span id="users-count" class="d-block text-center fs-2">{{ $users }}</span>
        </div>
      </div>

      <div class="card">
        <i class="fa-solid fa-box"></i>
        <div>
          <h3 class="fw-normal">Productos</h3>
          <span id="products-count" class="d-block text-center  fs-2">{{ $products }}</span>
        </div>
      </div>

      <div class="card">
        <i class="fa-solid fa-users"></i>
        <div>
          <h3 class="fw-normal">Usuarios últ. mes</h3>
          <span id="users-count" class="d-block text-center fs-2">{{$usersLastMonth}}</span>
        </div>
      </div>

      <div class="card">
        <i class="fa-solid fa-barcode"></i>
        <div>
          <h3 class="fw-normal">Escaneos efectivos</h3>
          <span id="users-count" class="d-block text-center fs-2">{{$effectiveScans}}</span>
        </div>
      </div>

      <div class="card">
        <i class="fa-solid fa-star"></i>
        <div>
          <h3 class="fw-normal">Usuarios premium</h3>
          <span id="users-count" class="d-block text-center fs-2">{{$premiumUsers}}</span>
        </div>
      </div>

      <div class="card">
        <i class="fa-solid fa-money-bill"></i>
        <div>
          <h3 class="fw-normal">Dinero total</h3>
          <span id="users-count" class="d-block text-center fs-2">${{$totalMoney}}</span>
        </div>
      </div>
    </section>

    <canvas id="usersChart" width="400" height="200"></canvas>
  </div>
</div>



</x-layouts.dashboard>
