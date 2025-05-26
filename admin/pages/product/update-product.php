<?php
    require_once('../model/products.php');
    $product = new Product();
    $stores = $product->getAllStore();
    $category_products = $product->getAllCategoryProduct();

    if (!isset($_GET['id'])) {
        header("Location: dashboard.php?module=user&pages=list-user");
        exit();
    }
    $product_id = $_GET['id'];

    $error = '';
    $success = '';

    $existingProduct = $product->getById($product_id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$existingProduct) {
            $error = "Produk tidak ditemukan.";
        } else {
            // Gunakan data baru jika diisi, jika tidak pakai data lama
            $product_name = !empty(trim($_POST['product_name'])) ? trim($_POST['product_name']) : $existingProduct['product_name'];
            $store_id = !empty($_POST['store']) ? $_POST['store'] : $existingProduct['store_id'];
            $category_product_id = !empty($_POST['category']) ? $_POST['category'] : $existingProduct['category_product_id'];
            $product_description = !empty(trim($_POST['product_description'])) ? trim($_POST['product_description']) : $existingProduct['product_description'];
            $product_price = !empty($_POST['product_price']) ? floatval($_POST['product_price']) : $existingProduct['product_price'];
            $product_stock = isset($_POST['product_stock']) && $_POST['product_stock'] !== '' ? intval($_POST['product_stock']) : $existingProduct['product_stock'];

            $product_image = $existingProduct['product_image'] ?? '';
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['product_image']['tmp_name'];
                $fileName = $_FILES['product_image']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = uniqid('img_') . '.' . $fileExtension;
                    $destPath = '../assets/images/' . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $product_image = $newFileName;
                    } else {
                        $error = "Gagal memindahkan file gambar.";
                    }
                } else {
                    $error = "Format file gambar tidak didukung. Hanya JPG, PNG, GIF.";
                }
            }

            // Validasi sederhana (opsional tergantung kasus)
            if (strlen($product_name) < 3) {
                $error = "Nama produk minimal 3 karakter.";
            } elseif ($product_price <= 0) {
                $error = "Harga produk harus lebih dari 0.";
            } elseif ($product_stock < 0) {
                $error = "Stok produk tidak boleh negatif.";
            } else {
                if ($product->update($product_id, $store_id, $product_name, $product_description, $product_price, $product_stock, $product_image, $category_product_id)) {
                    $success = "Produk berhasil diperbarui.";
                } else {
                    $error = "Gagal memperbarui produk.";
                }
            }
        }
    }
?>

<h2 class="mt-4 mb-4"><i class="bi bi-shop-window"></i> Edit Produk</h2>
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
      <strong>Hi,</strong> Silahkan isi form dibawah ini untuk mengedit Produk.
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
<?php endif; ?>

<form action="" method="POST" enctype="multipart/form-data" class="container p-4 border rounded shadow-sm mt-4" style="max-width: 900px;">
  <div class="row g-3">
    <!-- Kiri -->
    <div class="col-md-6">
      <div class="mb-3">
        <label for="product_name" class="form-label">Nama Produk</label>
        <input type="text" class="form-control" id="product_name" name="product_name"
          value="<?= htmlspecialchars($existingProduct['product_name'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label for="store" class="form-label">Pilih Toko    :</label><br>
        <select class="form-select" id="store" name="store">
          <option value="" disabled <?= !isset($existingProduct['store_id']) ? 'selected' : '' ?>>--Toko Belum Dipilih--</option>
          <?php foreach ($stores as $store): ?>
            <option value="<?= $store['store_id'] ?>"
              <?= $store['store_id'] == ($existingProduct['store_id'] ?? '') ? 'selected' : '' ?>>
              <?= $store['store_name'] ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="category" class="form-label">Pilih Kategori Produk  :</label><br>
        <select class="form-select" id="category" name="category">
          <option value="" disabled <?= !isset($existingProduct['category_product_id']) ? 'selected' : '' ?>>--Kategori Produk Belum Dipilih--</option>
          <?php foreach ($category_products as $category_product): ?>
            <option value="<?= $category_product['category_product_id'] ?>"
              <?= $category_product['category_product_id'] == ($existingProduct['category_product_id'] ?? '') ? 'selected' : '' ?>>
              <?= $category_product['category_product_name'] ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="product_description" class="form-label">Deskripsi Produk</label>
        <textarea class="form-control" id="product_description" name="product_description" rows="10"><?= htmlspecialchars($existingProduct['product_description'] ?? '') ?></textarea>
      </div>
    </div>

    <!-- Kanan -->
    <div class="col-md-6">
      <div class="mb-3">
        <label for="product_price" class="form-label">Harga Produk</label>
        <input type="text" class="form-control" id="product_price" name="product_price"
          value="<?= htmlspecialchars($existingProduct['product_price'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label for="product_stock" class="form-label">Stok Produk</label>
        <input type="text" class="form-control" id="product_stock" name="product_stock"
          value="<?= htmlspecialchars($existingProduct['product_stock'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label for="product_image" class="form-label">Gambar Produk</label>
        <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*" onchange="previewImage(event)">
        <div class="mt-3">
          <img id="imagePreview" src="<?= $existingProduct['product_image'] ? '../assets/images/' . $existingProduct['product_image'] : '' ?>" alt="Preview" style="max-width: 100%; max-height: 200px;">
        </div>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-between mt-3">
    <a href="dashboard.php?module=product&pages=list-product" class="btn btn-secondary">Kembali</a>
    <button type="submit" class="btn btn-primary">Simpan</button>
  </div>
</form>

<script>
    function previewImage(event) {
    const preview = document.getElementById('imagePreview');
        preview.src = URL.createObjectURL(event.target.files[0]);
    }
</script>
