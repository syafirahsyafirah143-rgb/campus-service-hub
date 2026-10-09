<?php
require_once 'config/db.php';
include 'includes/header.php';

$error = '';
$success = '';

// Handle Registration
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($email) && !empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Task 1b
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        
        if ($stmt->execute()) {
            $success = "Registration successful! You can now login.";
        } else {
            $error = "Username or Email already exists.";
        }
        $stmt->close();
    } else {
        $error = "All fields are required.";
    }
}

// Handle Login
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) { // Task 1b
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role']; // Task 1c
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
    $stmt->close();
}
?>

<div class="row">
  <div class="col-md-6 border-end pe-4">
    <h3>Login</h3>
    <?php if($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if($success): ?><div class="alert alert-success"><?= htmlspecialchars($success); ?></div><?php endif; ?>
    <form action="login.php" method="POST">
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" name="login" class="btn btn-primary">Login</button>
    </form>
  </div>

  <div class="col-md-6 ps-4">
    <h3>Register</h3>
    <form action="login.php" method="POST">
      <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" name="register" class="btn btn-success">Register</button>
    </form>
  </div>
</div>

<?php include 'includes/footer.php'; ?>