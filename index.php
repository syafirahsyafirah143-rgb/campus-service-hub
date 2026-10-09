<?php
require_once 'config/db.php';
include 'includes/header.php';

// SQL JOIN to get user details (Task 6b)
$query = "SELECT services.*, users.username FROM services 
          JOIN users ON services.user_id = users.id 
          ORDER BY services.created_at DESC";
$result = $conn->query($query);
?>

<div class="p-5 mb-4 bg-light rounded-3 text-center">
  <h1 class="display-5 fw-bold">Campus Service Hub</h1>
  <p class="lead">Discover, share, and offer skills & services within your campus community.</p>
</div>

<h3>Available Services</h3>
<div class="row">
  <?php while($row = $result->fetch_assoc()): ?>
    <div class="col-md-4 mb-4">
      <div class="card h-100 shadow-sm">
        <img src="uploads/<?= htmlspecialchars($row['image']); ?>" class="card-img-top" style="height:200px; object-fit:cover;" alt="Service Image">
        <div class="card-body">
          <h5 class="card-title"><?= htmlspecialchars($row['title']); ?></h5>
          <p class="card-text text-truncate"><?= htmlspecialchars($row['description']); ?></p>
          <p class="fw-bold text-success">RM <?= number_format($row['price'], 2); ?></p>
        </div>
        <div class="card-footer text-muted small">
          Provided by: <?= htmlspecialchars($row['username']); ?>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<?php include 'includes/footer.php'; ?>