<?php
// admin/categories/categories.php
$current_page = 'categories.php';
require_once '../includes/header.php';

// Fetch all categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY id DESC");
$categories = $stmt->fetchAll();
?>

    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Categories</h1>
          </div>
          <div class="col-sm-6 text-right">
              <a href="add-category.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Category</a>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
            <div class="alert alert-success">Category added successfully.</div>
        <?php endif; ?>
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
            <div class="alert alert-success">Category updated successfully.</div>
        <?php endif; ?>
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="alert alert-success">Category deleted successfully.</div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th style="width: 150px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categories as $cat): ?>
                        <tr>
                            <td><?php echo $cat['id']; ?></td>
                            <td>
                                <?php if (!empty($cat['image'])): ?>
                                    <img src="../../assets/images/categories/<?php echo $cat['image']; ?>" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    <div style="width: 50px; height: 50px; background: #f4f6f9; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                        <i class="fas fa-folder text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($cat['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($cat['description']); ?></td>
                            <td>
                                <a href="edit-category.php?id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                <a href="delete-category.php?id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this category?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(count($categories) == 0): ?>
                        <tr><td colspan="5" class="text-center">No categories found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

      </div>
    </section>

<?php
require_once '../includes/footer.php';
?>
