<?php
session_start();
require_once('../admin/db-con.php');
$conn = create_connection();

$user_id = $_SESSION['user_id'];
$label_name = $_POST['label_name'] ?? '';

if ($label_name) {
    $stmt = $conn->prepare("INSERT INTO label (user_id, name) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $label_name);
    $stmt->execute();
    $_SESSION['label_message'] = ['type' => 'success', 'text' => 'Label added successfully.'];
} else {
    $_SESSION['label_message'] = ['type' => 'danger', 'text' => 'Label name is required.'];
}

header("Location: ../index.php");
exit;
