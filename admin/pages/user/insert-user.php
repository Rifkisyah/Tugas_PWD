<?php
  require_once('../model/users.php');
  $user = new Users();
  $roles = $user->getAllRoles();

  $error = '';
  $success = '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = trim($_POST['username']);
      $email = trim($_POST['email']);
      $password = $_POST['password'];
      $role_id = $_POST['role'];
      $image_name = $existingProduct['photo_profile'] ?? 'default-photo-profile.jpg';

      // Validasi input
      if (strlen($username) < 3) {
          $error = "Gagal menambahkan akun pengguna. Username minimal 3 karakter.";
      } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $error = "Gagal menambahkan akun pengguna. Format email tidak valid.";
      } elseif (strlen($password) < 8) {
          $error = "Gagal menambahkan akun pengguna. Password minimal 8 karakter.";
      } elseif (empty($role_id)) {
          $error = "Gagal menambahkan akun pengguna. Pilih Role terlebih dahulu.";
      } elseif (empty($username)) {
          $error = "Gagal menambahkan akun pengguna. Username tidak boleh kosong.";
      }else if(empty($email)) {
          $error = "Gagal menambahkan akun pengguna. Email tidak boleh kosong.";
      } elseif (empty($password)) {
        $error = "Gagal menambahkan akun pengguna. Password tidak boleh kosong.";
      } elseif (empty($role_id)) {
          $error = "Gagal menambahkan akun pengguna. Role tidak boleh kosong.";
      } else {
          // Proses gambar jika ada
          if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
              $fileTmpPath = $_FILES['image']['tmp_name'];
              $fileName = $_FILES['image']['name'];
              $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
              $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

              if (in_array($fileExtension, $allowedExtensions)) {
                  $newFileName = uniqid('profile_') . '.' . $fileExtension;
                  $destPath = '../assets/images/' . $newFileName;

                  if (move_uploaded_file($fileTmpPath, $destPath)) {
                      $image_name = $newFileName;
                  } else {
                      $error = "Gagal mengunggah gambar profil.";
                  }
              } else {
                  $error = "Format gambar tidak valid. Hanya JPG, JPEG, PNG, dan GIF.";
              }
          }

          // Insert user jika tidak ada error
          if (empty($error)) {
              if ($user->insert($email, $username, $password, $role_id, $image_name)) {
                  $success = "Akun pengguna berhasil ditambahkan.";
              } else {
                  $error = "Gagal menambahkan akun pengguna. Email atau username sudah terdaftar.";
              }
          }
      }
  }
?>


<h2 class="mt-4 mb-4"><i class="fa fa-user"></i> Tambahkan Akun Pengguna</h2>
<hr style="height: 20px;">

<?php if($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= $error ?>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
<?php elseif($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= $success ?>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
<?php else: ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
      <strong>Hi,</strong> Silahkan isi form dibawah ini untuk menambahkan akun pengguna baru.
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
<?php endif; ?>

<form action="" method="POST" enctype="multipart/form-data" class="p-4 border rounded shadow-sm" style="max-width: 900px; justify-content: center; margin: auto;">
  <div class="row g-3">
    <!-- Kiri -->
    <div class="col-md-6">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" required>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>

      <div class="mb-3">
        <label for="role" class="form-label">Pilih Role :</label>
        <select class="form-select" id="role" name="role" required>
          <option value="" disabled selected>--Role Belum Dipilih--</option>
          <?php foreach($roles as $role): ?>
            <option value="<?= $role['role_id'] ?>"><?= $role['role_name'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <!-- Kanan -->
    <div class="col-md-6">
      <div class="mb-3">
        <label for="image" class="form-label">Foto Profil</label>
        <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(event)">
        <div class="mt-3">
          <img id="imagePreview" src="<?= !empty($existingStore['store_image']) ? '../assets/images/' . $existingStore['store_image'] : '' ?>" alt="Preview" style="max-width: 100%; max-height: 200px;">
        </div>
      </div>

    </div>
  </div>    

    <div class="d-flex justify-content-between">
      <a href="<?='dashboard.php?module=user&pages=list-user';?>" class="btn btn-secondary">Kembali</a>
      <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    function previewImage(event) {
    const preview = document.getElementById('imagePreview');
        preview.src = URL.createObjectURL(event.target.files[0]);
    }
</script>