<?php
// admin/categories/edit-category.php
$current_page = 'categories.php';
require_once '../includes/header.php';

if (!isset($_GET['id'])) {
    redirect('categories.php');
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    redirect('categories.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = sanitize_input($_POST['category_name']);
    $description = sanitize_input($_POST['description']);

    if (empty($category_name)) {
        $error = "Category name is required.";
    } else {
        $image = $category['image']; // Default to existing image
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid() . '.' . $ext;
                $destination = '../../assets/images/categories/' . $new_filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                    $image = $new_filename;
                    
                    // Optionally delete old image
                    if (!empty($category['image']) && file_exists('../../assets/images/categories/' . $category['image'])) {
                        unlink('../../assets/images/categories/' . $category['image']);
                    }
                } else {
                    $error = "Failed to upload image.";
                }
            } else {
                $error = "Invalid image format. Only JPG, PNG, GIF allowed.";
            }
        }
        
        if (empty($error)) {
            $update_stmt = $pdo->prepare("UPDATE categories SET category_name = ?, description = ?, image = ? WHERE id = ?");
            if ($update_stmt->execute([$category_name, $description, $image, $id])) {
                redirect('categories.php?msg=updated');
            } else {
                $error = "Failed to update category.";
            }
        }
    }
}
?>

    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Edit Category</h1>
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
            
            <form action="edit-category.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                    <?php if(!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="category_name" class="form-control" value="<?php echo htmlspecialchars($category['category_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($category['description']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Current Image</label><br>
                        <?php if (!empty($category['image'])): ?>
                            <img src="../../assets/images/categories/<?php echo $category['image']; ?>" alt="Category Image" width="100" class="img-thumbnail mb-2">
                        <?php else: ?>
                            <p class="text-muted">No image uploaded.</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label>Upload New Image (Replaces current)</label>
                        <input type="file" name="image" class="form-control-file" accept="image/*">
                        <small class="text-muted">Optional. Recommended size: 400x400px.</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update Category</button>
                    <a href="categories.php" class="btn btn-default float-right">Cancel</a>
                </div>
            </form>
        </div>
      </div>
    </section>

<?php
require_once '../includes/footer.php';
?>
