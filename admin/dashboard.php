<?php
    session_start();

    if(!isset($_SESSION['admin_user_id'])){
        header('location: index.php');
    }

    require_once('../model/users.php');
    $user = new Users();
    require_once('../model/stores.php');
    $store = new Store();
    require_once('../model/products.php');
    $product = new Product();
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/bootstrap_admin/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/bootstrap_admin/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="../assets/bootstrap_admin/css/simple-sidebar.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">


    <title>::. Administrator .::</title>
  </head>
  <body>
  <div class="d-flex" id="wrapper">

    <!-- Sidebar -->
    <div class="bg-primary border-right text-white" id="sidebar-wrapper">
      <div class="sidebar-heading">My E-Commerce </div>
      <div class="list-group list-group-flush bg-primary text-white">
        <a href="<?='dashboard.php';?>" class="list-group-item list-group-item-action bg-primary text-white"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="<?='dashboard.php?module=user&pages=list-user';?>" class="list-group-item list-group-item-action bg-primary text-white"><i class="fas fa-user"></i> Akun Pengguna</a>
        <a href="<?='dashboard.php?module=store&pages=list-store';?>" class="list-group-item list-group-item-action bg-primary text-white"><i class="fas fa-layer-group"></i> Toko</a>
        <a href="<?='dashboard.php?module=product&pages=list-product';?>" class="list-group-item list-group-item-action bg-primary text-white"><i class="fas fa-wrench"></i> Produk</a>
        <a href="<?='dashboard.php?module=about&pages=about-app';?>" class="list-group-item list-group-item-action bg-primary text-white"><i class="fas fa-info"></i> Tentang Aplikasi</a>
      </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">

      <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <button class="btn btn-default" id="menu-toggle"><i class="fas fa-bars"></i> </button>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-user"></i> <?php echo $_SESSION['admin_username']; ?>
              </a>
              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="<?='dashboard.php?module=profile&pages=edit-profile';?>"><i class="fas fa-edit"></i> Profile</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="signout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
              </div>
            </li>
          </ul>
        </div>
      </nav>

      <div class="container-fluid">
          <?php
            $page = 'pages/dashboard-main.php';
            if(isset($_GET['module'])){
              $page = 'pages/'. $_GET['module'].'/'.$_GET['pages'].'.php';
            }
            require($page);
          ?>
      </div><!--END container-fluid-->
    </div>
    <!-- /#page-content-wrapper -->

  </div>
  <!-- /#wrapper -->


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="../assets/bootstrap_admin/js/jquery-3.4.1.slim.min.js" ></script>
    <script src="../assets/bootstrap_admin/js/popper.min.js" ></script>
    <script src="../assets/bootstrap_admin/js/bootstrap.min.js"></script>

  <!-- Menu Toggle Script -->
  <script>
    $("#menu-toggle").click(function(e) {
      e.preventDefault();
      $("#wrapper").toggleClass("toggled");
    });

    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })    
  </script>

  </body>
</html>