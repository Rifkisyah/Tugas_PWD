<?php
require_once('../model/stores.php');
$stores = new Stores();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $store_name = trim($_POST['store_name']);
    $owner_name = trim($_POST['owner_name']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);

    $store_image = $existingProduct['store_image'] ?? 'no-image-store.jpg';

    // Handle upload gambar
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = uniqid('img_') . '.' . $fileExtension;
            $destPath = '../assets/images/' . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $store_image = $newFileName;
            } else {
                $error = "Gagal memindahkan file gambar.";
            }
        } else {
            $error = "Format file gambar tidak didukung. Hanya JPG, PNG, GIF.";
        }
    }

    // Validasi input
    if (empty($error)) {
        if (strlen($store_name) < 3) {
            $error = "Gagal Menambahkan Toko, Nama toko minimal 3 karakter.";
        } elseif (strlen($owner_name) < 3) {
            $error = "Gagal Menambahkan Toko, Nama owner minimal 3 karakter.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Gagal Menambahkan Toko, Format email tidak valid.";
        } elseif (strlen($contact) < 8) {
            $error = "Gagal Menambahkan Toko, Nomor kontak minimal 8 digit.";
        } elseif (strlen($address) < 5) {
            $error = "Gagal Menambahkan Toko, Alamat minimal 5 karakter.";
        } else if(empty($store_name)){
            $error = "Gagal Menambahkan Toko, Nama Toko Tidak Boleh Kosong";
        }else if(empty($owner_name)) {
            $error = "Gagal Menambahkan Toko, Nama Owner Toko tidak boleh kosong.";
        } elseif (empty($address)) {
            $error = "Gagal Menambahkan Toko, Alamat Toko tidak boleh kosong.";
        } elseif (empty($email)) {
            $error = "Gagal Menambahkan Toko, Email Toko tidak boleh kosong.";
        }else if(empty($contact)){
            $error = "Gagal Menambahkan Toko, Kontak Toko Tidak Boleh Kosong";
        } else {
            // Jika validasi lolos, simpan data toko
            if ($stores->insert($store_name, $owner_name, $address, $email, $contact, $store_image)) {
                $success = "Data toko berhasil ditambahkan.";
            } else {
                $error = "Gagal menambahkan data toko. Email mungkin sudah digunakan.";
            }
        }
    }
}
?>

<h2 class="mt-4 mb-4"><i class="bi bi-shop-window"></i> Tambahkan Toko</h2>
<hr style="height: 20px;">

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $error ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php elseif ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $success ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php else: ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>Hi,</strong> Silahkan isi form dibawah ini untuk menambahkan Toko.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<form action="" method="POST" enctype="multipart/form-data" class="p-4 border rounded shadow-sm" style="max-width: 600px; margin: auto;">
  <div class="row g-3">
    <!-- Kiri -->
    <div class="col-md-6">
      <div class="mb-3">
        <label for="store_name" class="form-label">Nama Toko</label>
        <input type="text" class="form-control" id="store_name" name="store_name" required>
      </div>

      <div class="mb-3">
        <label for="owner_name" class="form-label">Nama Owner</label>
        <input type="text" class="form-control" id="owner_name" name="owner_name" required>
      </div>

      <div class="mb-3">
        <label for="address" class="form-label">Alamat</label>
        <textarea class="form-control" id="address" name="address" rows="10" required></textarea>
      </div>
    </div>

    <!-- Kanan -->
    <div class="col-md-6">
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>

      <div class="mb-3">
        <label for="contact" class="form-label">Kontak</label>
        <input type="text" class="form-control" id="contact" name="contact" required>
      </div>

      <!-- Upload Gambar -->
      <div class="mb-3">
        <label for="image" class="form-label">Gambar Toko</label>
        <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(event)">
        <div class="mt-3">
          <img id="imagePreview" src="" alt="Preview" style="max-width: 100%; max-height: 200px;">
        </div>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-between mt-3">
    <a href="dashboard.php?module=store&pages=list-store" class="btn btn-secondary">Kembali</a>
    <button type="submit" class="btn btn-primary">Simpan</button>
  </div>
</form>

<script>
  function previewImage(event) {
    const preview = document.getElementById('imagePreview');
    preview.src = URL.createObjectURL(event.target.files[0]);
  }
</script>
