<?php
// admin/products/add-product.php
$current_page = 'view-products.php';
require_once '../includes/header.php';

// Fetch Categories, Classes, Subjects
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$classes = $pdo->query("SELECT * FROM book_classes")->fetchAll();
$subjects = $pdo->query("SELECT * FROM subjects")->fetchAll();

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = sanitize_input($_POST['product_name']);
    $category_id = $_POST['category_id'];
    $class_id = !empty($_POST['class_id']) ? $_POST['class_id'] : null;
    $subject_id = !empty($_POST['subject_id']) ? $_POST['subject_id'] : null;
    $description = sanitize_input($_POST['description']);
    $wholesale_price = $_POST['wholesale_price'];
    $retail_price = $_POST['retail_price'];
    $stock = $_POST['stock'];
    $status = $_POST['status'];
    
    $image_name = null;

    if (empty($product_name) || empty($category_id) || empty($retail_price)) {
        $error = "Product Name, Category, and Retail Price are required.";
    } else {
        // Image Upload Handle
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $image_name = time() . '_' . rand(1000,9999) . '.' . $ext;
                $destination = '../../assets/images/products/' . $image_name;
                move_uploaded_file($_FILES['image']['tmp_name'], $destination);
            } else {
                $error = "Invalid image format. Only JPG, PNG, GIF are allowed.";
            }
        }

        if (empty($error)) {
            $stmt = $pdo->prepare("INSERT INTO products (category_id, class_id, subject_id, product_name, description, wholesale_price, retail_price, stock, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$category_id, $class_id, $subject_id, $product_name, $description, $wholesale_price, $retail_price, $stock, $image_name, $status])) {
                redirect('view-products.php?msg=added');
            } else {
                $error = "Failed to add product.";
            }
        }
    }
}
?>

    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Add Product</h1>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Product Details</h3>
            </div>
            
            <form action="add-product.php" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                    <?php if(!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="product_name" id="product_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Class (For Books)</label>
                            <select name="class_id" class="form-control">
                                <option value="">None</option>
                                <?php foreach($classes as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['class_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Subject (For Books)</label>
                            <select name="subject_id" class="form-control">
                                <option value="">None</option>
                                <?php foreach($subjects as $s): ?>
                                    <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['subject_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Wholesale Price (Optional)</label>
                            <input type="number" step="0.01" name="wholesale_price" class="form-control" value="0">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Retail Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="retail_price" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Stock</label>
                            <input type="number" name="stock" class="form-control" value="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Product Image</label>
                            <input type="file" name="image" class="form-control-file" accept="image/*">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Save Product</button>
                    <a href="view-products.php" class="btn btn-default float-right">Cancel</a>
                </div>
            </form>
        </div>
      </div>
    </section>

<script>
document.getElementById('product_name').addEventListener('input', function() {
    var name = this.value.toLowerCase();
    var catSelect = document.getElementById('category_id');
    var options = catSelect.options;
    
    var targetCategory = null;
    
    if (name.includes('pen')) targetCategory = 'pens';
    else if (name.includes('marker')) targetCategory = 'markers';
    else if (name.includes('book') || name.includes('notebook')) targetCategory = 'books';
    else if (name.includes('bag') || name.includes('backpack')) targetCategory = 'bags';
    else if (name.includes('calculator')) targetCategory = 'calculators';
    else if (name.includes('stapler')) targetCategory = 'office';

    if (targetCategory) {
        for (var i = 0; i < options.length; i++) {
            var optText = options[i].text.toLowerCase();
            if (optText.includes(targetCategory)) {
                catSelect.selectedIndex = i;
                break;
            }
        }
    }
});
</script>

<?php
require_once '../includes/footer.php';
?>
