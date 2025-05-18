<?php
session_start();
require_once('../admin/db-con.php');
$conn = create_connection();

$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("DELETE FROM label WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

$_SESSION['label_message'] = ['type' => 'warning', 'text' => 'Label deleted.'];
header("Location: ../index.php");
exit;
