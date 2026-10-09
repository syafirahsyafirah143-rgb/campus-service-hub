<?php
require_once 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Admin manages all services, user manages own services only (Task 1d)
if ($_SESSION['role'] === 'admin') {
    $stmt = $conn->prepare("SELECT services.*, users.username FROM services JOIN users ON services.user_id = users.id ORDER BY created_at DESC");
} else {
    $stmt = $conn->prepare("SELECT services.*, users.username FROM services JOIN users ON services.user_id = users.id WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $_SESSION['user_id']);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Management Dashboard</h2>
    <a href="add_edit_service.php" class="btn btn-success">+ Add New Service</a>
</div>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
        <tr>
            <th>Image</th>
            <th>Title</th>
            <th>Owner</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><img src="uploads/<?= htmlspecialchars($row['image']); ?>" width="60" height="60" style="object-fit:cover;"></td>
            <td><?= htmlspecialchars($row['title']); ?></td>
            <td><?= htmlspecialchars($row['username']); ?></td>
            <td>RM <?= number_format($row['price'], 2); ?></td>
            <td>
                <a href="add_edit_service.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="delete_service.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this service?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>