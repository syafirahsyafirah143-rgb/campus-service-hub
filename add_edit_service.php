<?php
require_once 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$title = $description = $price = $image = "";
$errors = [];

if ($id > 0) {
    // Check ownership / admin privileges
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $service = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$service || ($_SESSION['role'] !== 'admin' && $service['user_id'] != $_SESSION['user_id'])) { // Task 1d
        die("Unauthorized access.");
    }

    $title = $service['title'];
    $description = $service['description'];
    $price = $service['price'];
    $image = $service['image'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $file = $_FILES['image'];

    // Server-Side Validation (Task 4b)
    if (empty($title)) $errors[] = "Title is required.";
    if (empty($description)) $errors[] = "Description is required.";
    if ($price <= 0) $errors[] = "Price must be greater than zero.";

    $filename = $image ? $image : 'default.jpg';

    // File Upload Handling (Task 3c)
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxSize = 2 * 1024 * 1024; // 2MB limit

        if (!in_array($file['type'], $allowedTypes)) {
            $errors[] = "Only JPG and PNG files are allowed.";
        }
        if ($file['size'] > $maxSize) {
            $errors[] = "File size must not exceed 2MB.";
        }

        if (empty($errors)) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($file['tmp_name'], 'uploads/' . $filename);
        }
    }

    if (empty($errors)) {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE services SET title=?, description=?, price=?, image=? WHERE id=?");
            $stmt->bind_param("ssdsi", $title, $description, $price, $filename, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO services (user_id, title, description, price, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issds", $_SESSION['user_id'], $title, $description, $price, $filename);
        }
        $stmt->execute();
        $stmt->close();
        header("Location: dashboard.php");
        exit;
    }
}
?>

<h2><?= $id > 0 ? 'Edit' : 'Add'; ?> Service</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e): ?>
            <div><?= htmlspecialchars($e); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Client-side JavaScript Validation (Task 4a) -->
<form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
  <div class="mb-3">
    <label>Title</label>
    <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($title); ?>" required>
  </div>
  <div class="mb-3">
    <label>Description</label>
    <textarea id="description" name="description" class="form-control" required><?= htmlspecialchars($description); ?></textarea>
  </div>
  <div class="mb-3">
    <label>Price (RM)</label>
    <input type="number" step="0.01" id="price" name="price" class="form-control" value="<?= htmlspecialchars($price); ?>" required>
  </div>
  <div class="mb-3">
    <label>Service Image (JPG/PNG, Max 2MB)</label>
    <input type="file" id="image" name="image" class="form-control" accept="image/png, image/jpeg">
  </div>
  <button type="submit" class="btn btn-primary">Save Service</button>
</form>

<script>
function validateForm() {
    let title = document.getElementById('title').value.trim();
    let price = document.getElementById('price').value;
    let fileInput = document.getElementById('image');

    if (title === "") {
        alert("Title cannot be empty.");
        return false;
    }
    if (price <= 0) {
        alert("Please enter a valid price.");
        return false;
    }
    if (fileInput.files.length > 0) {
        let file = fileInput.files[0];
        let allowed = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!allowed.includes(file.type)) {
            alert("Invalid file format. Only JPG and PNG allowed.");
            return false;
        }
        if (file.size > 2 * 1024 * 1024) {
            alert("File is too large. Max size is 2MB.");
            return false;
        }
    }
    return true;
}
</script>

<?php include 'includes/footer.php'; ?>