<?php
require_once('../model/users.php');
$user = new Users();
$roles = $user->getAllRoles();

if (!isset($_GET['id'])) {
    header("Location: dashboard.php?module=user&pages=list-user");
    exit();
}
$user_id = $_GET['id'];
$existingStore = $user->getById($user_id);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) && $_POST['username'] !== '' 
        ? trim($_POST['username']) 
        : $existingUser['username'];

    $email = isset($_POST['email']) && $_POST['email'] !== '' 
        ? trim($_POST['email']) 
        : $existingUser['email'];

    $password = isset($_POST['password']) && $_POST['password'] !== '' 
        ? password_hash($_POST['password'], PASSWORD_DEFAULT) 
        : $existingUser['password'];

    $role_id = isset($_POST['role']) && $_POST['role'] !== '' 
        ? intval($_POST['role']) 
        : $existingUser['role_id'];


    $profileImage = $existingStore['photo_profile'] ?? '';

    // Perbaikan: gunakan name="image" bukan product_image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = uniqid('img_') . '.' . $fileExtension;
            $destPath = '../assets/images/' . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $profileImage = $newFileName;
            } else {
                $error = "Gagal memindahkan file gambar.";
            }
        } else {
            $error = "Format file gambar tidak didukung. Hanya JPG, PNG, GIF.";
        }
    }

    // Validasi input
    if (strlen($username) < 3) {
        $error = "Gagal mengedit akun pengguna. Username minimal 3 karakter.";
    } elseif (!preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        $error = "Gagal mengedit akun pengguna. Format email tidak valid.";
    } elseif (strlen($password) < 8) {
        $error = "Gagal mengedit akun pengguna. Password minimal 8 karakter.";
    } elseif (empty($role_id)) {
        $error = "Gagal mengedit akun pengguna. Pilih Role terlebih dahulu.";
    } else {
        // Jika validasi lolos, update data
        if ($user->update($user_id, $email, $username, $password, $role_id, $profileImage)) {
            $success = "Akun pengguna berhasil di edit.";
            $_SESSION['username'] = $username;
        } else {
            $error = "Gagal mengedit akun pengguna. Email atau username sudah ada.";
        }
    }
}
?>

<h2 class="mt-4 mb-4"><i class="fa fa-user"></i> Edit Akun Pengguna</h2>
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
    <strong>Hi,</strong> Silahkan isi form dibawah ini untuk mengedit akun pengguna. ID Pengguna Saat ini adalah <strong><?php echo $user_id ?></strong>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    </div>
<?php endif; ?>

<!-- ... bagian PHP sebelum form tetap sama ... -->

<form action="" method="POST" enctype="multipart/form-data" class="p-4 border rounded shadow-sm" style="max-width: 900px; justify-content: center; margin: auto;">
    <div class="row g-3">
        <!-- Kiri -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="username" class="form-label">Username Baru</label>
                <input type="text" class="form-control" id="username" name="username"
                    value="<?= htmlspecialchars($_POST['username'] ?? $existingStore['username']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Baru</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="<?= htmlspecialchars($_POST['email'] ?? $existingStore['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password Baru</label>
                <input type="password" class="form-control" id="password" name="password"
                value="<?= htmlspecialchars($_POST['password'] ?? $existingStore['password']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="" disabled <?= !isset($_POST['role']) && !$existingStore['role_id'] ? 'selected' : '' ?>>
                        Pilih Role Baru
                    </option>
                    <?php foreach($roles as $role): ?>
                        <option value="<?= $role['role_id'] ?>"
                            <?= 
                                (isset($_POST['role']) && $_POST['role'] == $role['role_id']) || 
                                (!isset($_POST['role']) && $existingStore['role_id'] == $role['role_id']) 
                                ? 'selected' : '' 
                            ?>>
                            <?= htmlspecialchars($role['role_name']) ?>
                        </option>
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
                    <img id="imagePreview" 
                        src="<?= !empty($existingStore['photo_profile']) ? '../assets/images/' . $existingStore['photo_profile'] : '' ?>" 
                        alt="Preview" 
                        style="max-width: 100%; max-height: 200px;">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="dashboard.php?module=user&pages=list-user" class="btn btn-secondary">Kembali</a>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
  function previewImage(event) {
    const preview = document.getElementById('imagePreview');
    preview.src = URL.createObjectURL(event.target.files[0]);
  }
</script>
