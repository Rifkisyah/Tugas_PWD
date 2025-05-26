<?php
    require_once('../model/users.php');

    $user = new Users();
    $roles = $user->getAllRoles();

    $error = '';
    $success = '';

    // Ambil foto dari database (untuk preview)
    $photo = $user->getPhotoProfile($_SESSION['id']);
    if (!$photo) {
        $photo = "default-photo-profile.jpg";
    }

    // Simpan nama file foto baru (kalau ada)
    $newPhotoProfile = null;

    // Jika form disubmit (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // === HANDLE FILE FOTO (kalau ada) ===
        if (
            isset($_FILES['photo_profile']) &&
            $_FILES['photo_profile']['error'] === 0
        ) {
            $target_dir = "/assets/images/";
            $filename = basename($_FILES["photo_profile"]["name"]);
            $target_file = $target_dir . $filename;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["photo_profile"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["photo_profile"]["tmp_name"], $target_file)) {
                    $newPhotoProfile = $filename; // ← ini yang akan dikirim ke updateProfile()
                } else {
                    $error = "Gagal mengunggah foto profil.";
                }
            } else {
                $error = "File yang dipilih bukan gambar.";
            }
        }

        // === VALIDASI INPUT LAINNYA ===
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($error)) { // Pastikan tidak ada error upload sebelum lanjut validasi lain
            if (strlen($username) < 3) {
                $error = "Username minimal 3 karakter.";
            } elseif (!preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
                $error = "Format email tidak valid.";
            } elseif (strlen($password) < 8) {
                $error = "Password minimal 8 karakter.";
            } else {
                // Semua validasi lolos, lakukan update
                $updated = $user->updateProfile(
                    $_SESSION['id'],
                    $email,
                    $username,
                    $password,
                    $newPhotoProfile // ← bisa null kalau tidak upload
                );

                if ($updated) {
                    $success = "Profil berhasil diperbarui.";
                } else {
                    $error = "Gagal mengedit profil. Email atau username sudah digunakan.";
                }
            }
        }
    }
?>

<h2 class="mt-4 mb-4"><i class="fas fa-user"></i> Profile</h2>
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
    <strong>Hi,</strong> Silahkan isi form dibawah ini untuk mengedit Profil. ID Pengguna Saat ini adalah <strong><?php echo $_SESSION['id']; ?></strong>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    </div>
<?php endif; ?>

<form action="" method="POST" class="p-4 border rounded shadow-sm" style="max-width: 400px; justify-content: center; margin: auto;">
    <div class="container mt-5">

        <img src="/assets/images/<?php echo htmlspecialchars($photo); ?>" 
            alt="Profile Picture" 
            class="rounded-circle border border-2 border-secondary shadow-sm"
            style="width: 150px; height: auto; object-fit: cover; margin-bottom: 20px; margin-left: 25%;"
        ">
        <div class="mb-3">
            <label for="profilePhoto" class="form-label">Photo Profile</label>
            <input 
            class="form-control" 
            type="file" 
            id="profilePhoto" 
            name="photo_profile" 
            accept="image/*" 
            onchange="previewImage(event)" />
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" />
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" />
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="New password" />
        </div>

        <div class="d-flex justify-content-between">
            <a href="<?='dashboard.php';?>" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </div>
</form>