<?php
require_once('../model/stores.php');
$stores = new Store();

if (!isset($_GET['id'])) {
    header("Location: dashboard.php?module=store&pages=list-store");
    exit();
}
$store_id = $_GET['id'];
$existingStore = $stores->getStoreById($store_id);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $store_name = isset($_POST['store_name']) && $_POST['store_name'] !== ''
    ? trim($_POST['store_name'])
    : ($existingStore['store_name'] ?? '');

  $owner_name = isset($_POST['owner_name']) && $_POST['owner_name'] !== ''
    ? trim($_POST['owner_name'])
    : ($existingStore['owner_name'] ?? '');

  $address = isset($_POST['address']) && $_POST['address'] !== ''
    ? trim($_POST['address'])
    : ($existingStore['address'] ?? '');

  $email = isset($_POST['email']) && $_POST['email'] !== ''
    ? trim($_POST['email'])
    : ($existingStore['email'] ?? '');

  $contact = isset($_POST['contact']) && $_POST['contact'] !== ''
    ? trim($_POST['contact'])
    : ($existingStore['contact'] ?? '');

  $store_image = isset($_POST['store_image']) && $_POST['store_image'] !== ''
    ? trim($_POST['store_image'])
    : ($existingStore['store_image'] ?? '');


    // Handle Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = uniqid('img_') . '.' . $fileExtension;
            $destPath = '../assets/images/store/' . $newFileName;
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $store_image = $newFileName;
            } else {
                $error = "Gagal mengunggah gambar toko.";
            }
        } else {
            $error = "Format file tidak valid. Hanya JPG, JPEG, PNG, GIF yang diperbolehkan.";
        }
    }

    // Validasi input
    if (empty($store_name) || strlen($store_name) < 3) {
        $error = "Nama toko harus diisi minimal 3 karakter.";
    } elseif (empty($owner_name) || strlen($owner_name) < 3) {
        $error = "Nama pemilik minimal 3 karakter.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (strlen($contact) < 8 || strlen($contact) > 19) {
        $error = "Kontak harus antara 8 hingga 19 digit.";
    } elseif (empty($address) || strlen($address) < 5) {
        $error = "Alamat minimal 5 karakter.";
    }

    // Update jika tidak ada error
    if (empty($error)) {
        if ($stores->update($store_id, $store_name, $owner_name, $address, $email, $contact, $store_image)) {
            $success = "Data toko berhasil diperbarui.";
            $existingStore = $stores->getStoreById($store_id); // Refresh data
        } else {
            $error = "Gagal memperbarui data toko. Email mungkin sudah digunakan.";
        }
    }
}
?>

<h2 class="mt-4 mb-4"><i class="bi bi-shop-window"></i> Edit Toko</h2>
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
    <strong>Hi,</strong> Silahkan isi form dibawah ini untuk mengedit Toko. ID Toko Saat ini adalah <strong><?php echo $store_id ?></strong>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    </div>
<?php endif; ?>

<form action="" method="POST" enctype="multipart/form-data" class="container p-4 border rounded shadow-sm mt-4" style="max-width: 900px;">
  <div class="row g-3">
    <div class="col-md-6">
      <div class="mb-3">
        <label for="store_name" class="form-label">Nama Toko</label>
        <input type="text" class="form-control" id="store_name" name="store_name"
               value="<?= htmlspecialchars($_POST['store_name'] ?? $existingStore['store_name']) ?>" required>
      </div>

      <div class="mb-3">
        <label for="owner_name" class="form-label">Nama Pemilik</label>
        <input type="text" class="form-control" id="owner_name" name="owner_name"
               value="<?= htmlspecialchars($_POST['owner_name'] ?? $existingStore['store_owner']) ?>" required>
      </div>

      <div class="mb-3">
        <label for="address" class="form-label">Alamat</label>
        <textarea class="form-control" id="address" name="address" rows="10"><?= htmlspecialchars($_POST['address'] ?? $existingStore['store_address']) ?></textarea>
      </div>
    </div>

    <div class="col-md-6">
      <div class="mb-3">
        <label for="email" class="form-label">Email Toko</label>
        <input type="email" class="form-control" id="email" name="email"
               value="<?= htmlspecialchars($_POST['email'] ?? $existingStore['store_email']) ?>" required>
      </div>

      <div class="mb-3">
        <label for="contact" class="form-label">Nomor Kontak</label>
        <input type="text" class="form-control" id="contact" name="contact"
               value="<?= htmlspecialchars($_POST['contact'] ?? $existingStore['store_contact']) ?>" required>
      </div>

      <div class="mb-3">
        <label for="image" class="form-label">Foto Toko</label>
        <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(event)">
        <div class="mt-3">
          <img id="imagePreview" src="<?= !empty($existingStore['store_image']) ? '../assets/images/' . $existingStore['store_image'] : '' ?>"
               alt="Preview" style="max-width: 100%; max-height: 200px;">
        </div>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-between mt-4">
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
