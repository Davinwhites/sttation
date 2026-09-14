<?php
// client/profile.php
require_once '../includes/header.php';

if (!is_customer_logged_in()) {
    redirect('login.php');
}

$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$_SESSION['customer_id']]);
$customer = $stmt->fetch();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = sanitize_input($_POST['fullname']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);

    $update = $pdo->prepare("UPDATE customers SET fullname = ?, phone = ?, address = ? WHERE id = ?");
    if ($update->execute([$fullname, $phone, $address, $_SESSION['customer_id']])) {
        $msg = "Profile updated successfully.";
        $_SESSION['customer_name'] = $fullname;
        // refresh data
        $customer['fullname'] = $fullname;
        $customer['phone'] = $phone;
        $customer['address'] = $address;
    } else {
        $msg = "Failed to update profile.";
    }
}
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="list-group shadow-sm">
            <a href="profile.php" class="list-group-item list-group-item-action active">My Profile</a>
            <a href="orders.php" class="list-group-item list-group-item-action">My Orders</a>
            <a href="logout.php" class="list-group-item list-group-item-action text-danger">Logout</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h4 class="mb-0">Profile Information</h4>
            </div>
            <div class="card-body">
                <?php if(!empty($msg)): ?>
                    <div class="alert alert-success"><?php echo $msg; ?></div>
                <?php endif; ?>
                
                <form action="profile.php" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Full Name</label>
                            <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($customer['fullname']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Email Address</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($customer['email']); ?>" disabled>
                            <small class="text-muted">Email cannot be changed.</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Delivery Address</label>
                        <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($customer['address']); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>
