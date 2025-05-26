<?php
require_once('../model/users.php');

$success = '';
$error = '';

if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'success':
            $success = 'Akun pengguna berhasil dihapus.';
            break;
        case 'error':
            $error = 'Gagal menghapus akun pengguna.';
            break;
        case 'invalid':
            $error = 'ID pengguna tidak valid.';
            break;
    }
}
?>

<h2 class="mt-4 mb-4"><i class="news"></i> Daftar Akun Pengguna</h2>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $success ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php elseif ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $error ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>


<a href="<?='dashboard.php?module=user&pages=insert-user';?>" class="btn btn-success" style="margin-left: 80%; width: auto;">Tambah Pengguna Baru</a>
<div class="row">
    <div class="col">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Photo Profile</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role Name</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    require_once('../model/users.php');
                    $user = new Users();
                    $users = $user->getUsers();
                    
                    $index = 0;
                    foreach($users as $row):
                        $photo_profile = $user->getById($row['user_id']);
                        ?>
                        <tr id="row-<?= $index ?>">
                            <td><?=$row['user_id'] ?></td>
                            <td><img id="imagePreview" src="<?= $photo_profile['photo_profile'] ? '../assets/images/' . $photo_profile['photo_profile'] : '' ?>" alt="Preview" style="max-width: 100%; max-height: 100px;"></td>
                            <td><?=$row['username'] ?></td>
                            <td><?=$row['email'] ?></td>
                            <td><?=$row['role_name'] ?></td>
                            <td>
                                <a href="#" class="unsee"><i class="fa fa-eye text-success"></i></a> 
                                <a href="<?='dashboard.php?module=user&pages=update-user&id=' . $row['user_id']; ?>" class="edit"><i class="fa fa-edit text-warning"></i></a> 
                                <a href="#" class="delete"><i class="fa fa-trash text-danger"></i></a> 
                            </td>
                        </tr>
                    <?php $index++; endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-between">
    <a href="<?='dashboard.php';?>" class="btn btn-secondary">Kembali</a>

</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Penghapusan</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin menghapus akun pengguna ini?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Ya, Hapus</a>
      </div>
    </div>
  </div>
</div>


<script>
    document.querySelectorAll('a.unsee').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault(); // supaya link gak jalan default

            // cari tr induk dari <a> ini
            const tr = this.closest('tr');
            if (!tr) return;

            // contoh "unsee": sembunyikan baris
            tr.style.display = 'none';
        });
    });

    document.querySelectorAll('a.delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            const tr = this.closest('tr');
            if (!tr) return;

            const userId = tr.querySelector('td').innerText;

            // Set href target pada tombol konfirmasi modal
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            confirmBtn.href = `dashboard.php?module=user&pages=delete-user&id=${userId}`;

            // Tampilkan modal konfirmasi
            $('#confirmDeleteModal').modal('show');
        });
    });

    function previewImage(event) {
        const preview = document.getElementById('imagePreview');
        preview.src = URL.createObjectURL(event.target.files[0]);
    }
</script>