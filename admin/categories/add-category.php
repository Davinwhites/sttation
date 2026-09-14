<?php
// admin/categories/add-category.php
$current_page = 'categories.php';
require_once '../includes/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = sanitize_input($_POST['category_name']);
    $description = sanitize_input($_POST['description']);

    if (empty($category_name)) {
        $error = "Category name is required.";
    } else {
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid() . '.' . $ext;
                $destination = '../../assets/images/categories/' . $new_filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                    $image = $new_filename;
                } else {
                    $error = "Failed to upload image.";
                }
            } else {
                $error = "Invalid image format. Only JPG, PNG, GIF allowed.";
            }
        }
        
        if (empty($error)) {
            $stmt = $pdo->prepare("INSERT INTO categories (category_name, description, image) VALUES (?, ?, ?)");
            if ($stmt->execute([$category_name, $description, $image])) {
                redirect('categories.php?msg=added');
            } else {
                $error = "Failed to add category.";
            }
        }
    }
}
?>

    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Add Category</h1>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Category Details</h3>
            </div>
            
            <form action="add-category.php" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                    <?php if(!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="category_name" class="form-control" placeholder="Enter name" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Enter description"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Category Image</label>
                        <input type="file" name="image" class="form-control-file" accept="image/*">
                        <small class="text-muted">Optional. Recommended size: 400x400px.</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Save Category</button>
                    <a href="categories.php" class="btn btn-default float-right">Cancel</a>
                </div>
            </form>
        </div>
      </div>
    </section>

<?php
require_once '../includes/footer.php';
?>
