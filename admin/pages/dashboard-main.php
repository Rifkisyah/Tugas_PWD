<h2 class="mt-4 mb-4"><i class="fas fa-tachometer-alt"></i> Dashboard</h2>

<div class="alert alert-info alert-dismissible fade show" role="alert">
  <strong>Hi,</strong> Selamat Datang <?php echo $_SESSION['admin_username']; ?> di halaman Dashboard.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="row">
  <div class="col-xl-4 col-md-6 mb-4">
    <a href="<?='dashboard.php?module=user&pages=list-user';?>">
      <div class="card border-left-primary shadow h-100 py-2 bg-primary">
            <div class="card-body ">
                <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-white mb-1">Akun Pengguna</div>
                    <div class="h5 mb-0 font-weight-bold text-white"><?php echo $user->countAll(); ?></div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-user fa-4x text-white"></i>
                </div>
                </div>
            </div>
        </div>
    </div>
  </a>

  <div class="col-xl-4 col-md-6 mb-4">
    <a href="<?='dashboard.php?module=product&pages=list-product';?>">
      <div class="card border-left-primary shadow h-100 py-2 bg-warning">
          <div class="card-body ">
              <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-white mb-1">Produk</div>
                  <div class="h5 mb-0 font-weight-bold text-white rounded-circle"><?php echo $product->countAll(); ?></div>
              </div>
              <div class="col-auto">
                  <i class="fas fa-layer-group fa-4x text-white"></i>
              </div>
              </div>
          </div>
      </div>
  </div>
    </a>

  <div class="col-xl-4 col-md-6 mb-4">
    <a href="<?='dashboard.php?module=store&pages=list-store';?>">
      <div class="card border-left-primary shadow h-100 py-2 bg-success">
          <div class="card-body ">
              <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-white mb-1">Toko</div>
                  <div class="h5 mb-0 font-weight-bold text-white"><?php echo $store->countAll(); ?></div>
              </div>
              <div class="col-auto">
                  <i class="fa fa-wrench fa-4x text-white"></i>
              </div>
              </div>
          </div>
      </div>
  </div>
    </a>
</div><!--END row   