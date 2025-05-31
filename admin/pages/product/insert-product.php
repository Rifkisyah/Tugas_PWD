<?php
require_once('../model/products.php');
require_once('../model/stores.php');
$product = new Product();
$store = new Store();
$stores = $store->getAllStore();
$category_products = $product->getAllCategoryProduct();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name']);
    $store_id = $_POST['store'];
    $category_product_id = $_POST['category'];
    $product_description = trim($_POST['product_description']);
    $product_price = floatval($_POST['product_price']);
    $product_stock = intval($_POST['product_stock']);
    $product_condition = trim($_POST['product_condition']);
    
    $product_images = []; // array penampung gambar content
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    // ========== Proses upload preview ==========
    $product_preview = 'no-image-product.jpg';
    if (isset($_FILES['product_preview']) && $_FILES['product_preview']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['product_preview']['tmp_name'];
        $name = $_FILES['product_preview']['name'];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    
        if (in_array($ext, $allowed)) {
            $newName = uniqid('preview_') . '.' . $ext;
            $dest = '../assets/images/product/' . $newName;
    
            if (move_uploaded_file($tmp, $dest)) {
                $product_preview = $newName;
            } else {
                $error = "Gagal upload gambar preview.";
            }
        } else {
            $error = "Format gambar preview tidak valid.";
        }
    }
    
    // ========== Proses upload gambar tambahan ==========
    if (!empty($_FILES['product_images']['name'][0])) {
        foreach ($_FILES['product_images']['tmp_name'] as $index => $tmpName) {
            if ($_FILES['product_images']['error'][$index] === UPLOAD_ERR_OK) {
                $origName = $_FILES['product_images']['name'][$index];
                $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                $newFileName = uniqid('content_') . '.' . $ext;
                $dest = '../assets/images/product/' . $newFileName;
    
                if (in_array($ext, $allowed) && move_uploaded_file($tmpName, $dest)) {
                    $product_images[] = $newFileName;
                }
            }
        }
    }    

    // Validasi input
    if (strlen($product_name) < 3) {
        $error = "Gagal Menambahkan Produk. Nama produk minimal 3 karakter.";
    } elseif ($product_price <= 0) {
        $error = "Gagal Menambahkan Produk. Harga produk harus lebih dari 0.";
    } elseif ($product_stock < 0) {
        $error = "Gagal Menambahkan Produk. Stok produk tidak boleh negatif.";
    }else if(empty($product_name)){
        $error = "Gagal Menambahkan Produk. Nama Produk Tidak Boleh Kosong";
    }else if(empty($product_description)) {
        $error = "Gagal Menambahkan Produk. Deskripsi produk tidak boleh kosong.";
    } elseif (empty($store_id)) {
        $error = "Gagal Menambahkan Produk. Toko tidak boleh kosong.";
    } elseif (empty($category_product_id)) {
        $error = "Gagal Menambahkan Produk. Kategori produk tidak boleh kosong.";
    }else if(empty($product_price)){
        $error = "Gagal Menambahkan Produk. Harga Produk Tidak Boleh Kosong";
    }else if(empty($product_stock)){
        $error = "Gagal Menambahkan Produk. Stok Produk Tidak Boleh Kosong";
    } else if (!is_numeric($product_price) || !is_numeric($product_stock)) {
        $error = "Harga dan stok harus berupa angka.";
    } elseif (!empty($product_condition) && !in_array($product_condition, ['New', 'Second-hand'])) {
        $error = "Kondisi produk harus 'Baru' atau 'Bekas'.";
    } else {
        // Cek apakah produk sudah ada di toko yang sama
        if ($product->isProductExist($store_id, $product_name)) {
            $error = "Produk dengan nama yang sama sudah ada di toko ini.";
        } else {
            // Insert data produk
            if($product->insert($store_id, $product_name, $product_description, $product_price, $product_stock, $product_condition, $product_preview, $category_product_id, $product_images)){
                $success = "Produk berhasil ditambahkan.";
            } else {
                $error = "Gagal menambahkan produk.";
            }
        }
    }
}
?>

<h2 class="mt-4 mb-4"><i class="bi bi-shop-window"></i> Tambahkan Produk</h2>
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
      <strong>Hi,</strong> Silahkan isi form dibawah ini untuk menambahkan Produk.
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
        <label for="category" class="form-label">Kategori Produk    :</label><br>
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
        <label for="product_condition" class="form-label">Kondisi Produk</label>
        <input type="text" class="form-control" id="product_condition" name="product_condition"
          value="<?= htmlspecialchars($existingProduct['product_condition'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label for="product_preview" class="form-label">Gambar Preview Produk</label>
        <input type="file" class="form-control" id="product_preview" name="product_preview" accept="image/*" onchange="previewImage(event)">
        <div class="mt-3">
          <img id="imagePreview" src="<?= $existingProduct['product_preview'] ? '../assets/images/product/' . $existingProduct['preview_image'] : '' ?>" alt="Preview" style="max-width: 100%; max-height: 200px;">
        </div>
      </div>

      <div class="mb-3">
        <label for="product_images" class="form-label">Gambar Produk (Bisa lebih dari satu)</label>
        <input type="file" class="form-control" id="product_images" name="product_images[]" accept="image/*" multiple>
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