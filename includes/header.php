<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Service Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-link navbar-brand fw-bold" href="index.php">Campus Service Hub</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="search.php">Search Services</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="add_edit_service.php">Add Service</a></li>
        <?php endif; ?>
      </ul>
      <div class="d-flex align-items-center text-white">
        <?php if (isset($_SESSION['user_id'])): ?>
          <span class="me-3">Hi, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong> (<?= $_SESSION['role']; ?>)</span>
          <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        <?php else: ?>
          <a href="login.php" class="btn btn-light btn-sm">Login / Register</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<div class="container">