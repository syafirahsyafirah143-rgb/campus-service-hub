<?php
require_once 'config/db.php';

// Handle AJAX Request
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    $query = isset($_GET['q']) ? trim($_GET['q']) : '';
    
    $sql = "SELECT services.*, users.username FROM services 
            JOIN users ON services.user_id = users.id 
            WHERE title LIKE ? OR description LIKE ? 
            ORDER BY created_at DESC";
    $searchTerm = "%" . $query . "%";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id' => $row['id'],
            'title' => htmlspecialchars($row['title']),
            'description' => htmlspecialchars($row['description']),
            'price' => number_format($row['price'], 2),
            'image' => htmlspecialchars($row['image']),
            'username' => htmlspecialchars($row['username'])
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

include 'includes/header.php';
?>

<h2>Search Services (Live AJAX Search)</h2>
<div class="mb-4">
    <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Type to search live dynamic services...">
</div>

<div class="row" id="searchResults">
    <!-- AJAX results render here -->
</div>

<script src="js/live_search.js"></script>

<?php include 'includes/footer.php'; ?>