<?php
require_once('../model/stores.php');

$success = '';
$error = '';

if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'success':
            $success = 'Toko berhasil dihapus.';
            break;
        case 'error':
            $error = 'Gagal menghapus Toko.';
            break;
        case 'invalid':
            $error = 'ID Toko tidak valid.';
            break;
    }
}
?>

<h2 class="mt-4 mb-4"><i class="news"></i> Daftar Toko</h2>

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


<a href="<?='dashboard.php?module=store&pages=insert-store';?>" class="btn btn-success" style="margin-left: 85%; width: auto;">Tambah Toko Baru</a>
<div class="row">
    <div class="col">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Gmabar Toko</th>
                    <th>Nama Toko</th>
                    <th>Nama Owner</th>
                    <th>Alamat</th>
                    <th>Email</th>
                    <th>Kontak</th>
                    <th>Toko Dibuat Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    require_once('../model/stores.php');
                    $store = new Store();
                    $stores = $store->getAllStores();
                    
                    
                    $index = 0;
                    foreach($stores as $row):
                        ?>
                        <tr id="row-<?= $index ?>">
                            <td><?=$row['store_id'] ?></td>
                            <td><img id="imagePreview" src="<?= $row['store_image'] ? '../assets/images/store/' . $row['store_image'] : '' ?>" alt="Preview" style="max-width: 100%; max-height: 1000px;"></td>
                            <td><?=$row['store_name'] ?></td>
                            <td><?=$row['store_owner'] ?></td>
                            <td><?=$row['store_address'] ?></td>
                            <td><?=$row['store_email'] ?></td>
                            <td><?=$row['store_contact'] ?></td>
                            <td><?=$row['created_at'] ?></td>
                            <td>
                                <a href="#" class="unsee"><i class="fa fa-eye text-success"></i></a> 
                                <a href="<?='dashboard.php?module=store&pages=update-store&id=' . $row['store_id']; ?>" class="edit"><i class="fa fa-edit text-warning"></i></a> 
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
        Apakah Anda yakin ingin menghapus Toko ini?
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

            const id = tr.querySelector('td').innerText;

            // Set href target pada tombol konfirmasi modal
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            confirmBtn.href = `dashboard.php?module=store&pages=delete-store&id=${id}`;

            // Tampilkan modal konfirmasi
            $('#confirmDeleteModal').modal('show');
        });
    });

    function previewImage(event) {
        const preview = document.getElementById('imagePreview');
            preview.src = URL.createObjectURL(event.target.files[0]);
    }
</script>