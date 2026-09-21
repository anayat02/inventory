<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="/" class="brand-link">
    <img src="<?php echo e(asset('backend/dist/img/logo.png')); ?>" alt="<?php echo e(config('app.name')); ?>" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light"><?php echo e(config('app.name')); ?></span>
  </a>
  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="<?php echo e(asset('backend/dist/img/user.png')); ?>" class="" alt="Logo Inventory">
      </div>
      <div class="info">
        <a href="#" class="d-block"><?php echo e(Auth::user()->lastname); ?> <?php echo e(Auth::user()->firstname); ?></a>
      </div>
    </div>
    <!-- SidebarSearch Form -->
    <div class="form-inline">
      <div class="input-group" data-widget="sidebar-search">
        <input class="form-control form-control-sidebar" type="search" placeholder="Поиск" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-sidebar">
            <i class="bi bi-search"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <?php echo $__env->make('backend.layouts.menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
<?php /**PATH F:\OSPanel\domains\inv.metu\resources\views/backend/layouts/sidebar.blade.php ENDPATH**/ ?>