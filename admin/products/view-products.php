<?php
// admin/products/view-products.php
$current_page = 'view-products.php';
require_once '../includes/header.php';

// Fetch products with their category names
$query = "SELECT p.*, c.category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          ORDER BY p.id DESC";
$stmt = $pdo->query($query);
$products = $stmt->fetchAll();
?>

    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Products</h1>
          </div>
          <div class="col-sm-6 text-right">
              <a href="add-product.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Product</a>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
            <div class="alert alert-success">Product added successfully.</div>
        <?php endif; ?>
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
            <div class="alert alert-success">Product updated successfully.</div>
        <?php endif; ?>
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="alert alert-success">Product deleted successfully.</div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body p-0 table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Retail Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th style="width: 120px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $prod): ?>
                        <tr>
                            <td><?php echo $prod['id']; ?></td>
                            <td>
                                <?php if($prod['image']): ?>
                                    <img src="../../assets/images/products/<?php echo htmlspecialchars($prod['image']); ?>" width="50" height="50" style="object-fit:cover;">
                                <?php else: ?>
                                    <span class="text-muted">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($prod['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($prod['category_name']); ?></td>
                            <td><?php echo format_price($prod['retail_price']); ?></td>
                            <td>
                                <?php if($prod['stock'] > 0): ?>
                                    <span class="badge badge-success"><?php echo $prod['stock']; ?></span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Out of Stock</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($prod['status'] == 'Active'): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit-product.php?id=<?php echo $prod['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                <a href="delete-product.php?id=<?php echo $prod['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(count($products) == 0): ?>
                        <tr><td colspan="8" class="text-center">No products found.</td></tr>
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
