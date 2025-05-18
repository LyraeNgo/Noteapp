<?php
session_start();
require_once('../admin/db-con.php');
$conn = create_connection();



$label_name = $_POST['label_name'] ?? '';
$label_color = $_POST['label_color'] ?? '#000000'; // default to black if empty

if ($label_name) {
    $stmt = $conn->prepare("INSERT INTO label (name, color, user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $label_name, $label_color, $user_id);
    $stmt->execute();
    $_SESSION['label_message'] = ['type' => 'success', 'text' => 'Label added successfully.'];
} else {
    $_SESSION['label_message'] = ['type' => 'danger', 'text' => 'Label name is required.'];
}

header("Location: ../index.php");
exit;
