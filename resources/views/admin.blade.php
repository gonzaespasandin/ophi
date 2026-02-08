<x-layouts.dashboard>
    <h1>Bienvenido al Panel de Administración de ophi</h1>
    <div class="dashboard">


  <main class="main">

    <header class="header">
      <h1>Dashboard</h1>
      <div class="user">
        <i class="fa-solid fa-user"></i>
      </div>
    </header>

    <!-- Cards -->
    <section class="cards">
      <div class="card">
        <i class="fa-solid fa-users"></i>
        <div>
          <h3>Usuarios</h3>
          <span id="users-count">{{ $users }}</span>
        </div>
      </div>

      <div class="card">
        <i class="fa-solid fa-box"></i>
        <div>
          <h3>Productos</h3>
          <span id="products-count">{{ $products }}</span>
        </div>
      </div>

      <div class="card">
        <i class="fa-solid fa-users"></i>
        <div>
          <h3>Usuarios últ. mes</h3>
          <span id="users-count">1.245</span>
        </div>
      </div>
    </section>

    <canvas id="usersChart" width="400" height="200"></canvas>




  </main>
</div>



</x-layouts.dashboard>
