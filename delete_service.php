<?php
require_once 'config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT user_id FROM services WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Enforce role-based permission
if ($res && ($_SESSION['role'] === 'admin' || $res['user_id'] == $_SESSION['user_id'])) {
    $del = $conn->prepare("DELETE FROM services WHERE id = ?");
    $del->bind_param("i", $id);
    $del->execute();
    $del->close();
}

header("Location: dashboard.php");
exit;
?>