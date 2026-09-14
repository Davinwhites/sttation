<?php
require_once '../includes/header.php';

$stmt = $pdo->query("SELECT * FROM customers ORDER BY created_at DESC");
$customers = $stmt->fetchAll();
?>

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Customers</h1>
      </div>
    </div>
  </div>
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
  <div class="container-fluid">
    <div class="card card-primary card-outline">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-users"></i> Registered Customers</h3>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Address</th>
              <th>Registered At</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($customers) > 0): ?>
              <?php foreach ($customers as $customer): ?>
                <tr>
                  <td><?= $customer['id'] ?></td>
                  <td><?= htmlspecialchars($customer['fullname']) ?></td>
                  <td><?= htmlspecialchars($customer['email']) ?></td>
                  <td><?= htmlspecialchars($customer['phone']) ?></td>
                  <td><?= htmlspecialchars($customer['address']) ?></td>
                  <td><?= date('M d, Y h:i A', strtotime($customer['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center">No customers found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</div>
<!-- /.content -->

<?php require_once '../includes/footer.php'; ?>
