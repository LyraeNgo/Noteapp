<?php
session_start();
require_once('./admin/db-con.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$search = $_GET['q'] ?? '';

$conn = create_connection();

$sql = "SELECT tieu_de, noi_dung FROM note WHERE user_id = ? AND (tieu_de LIKE ? OR noi_dung LIKE ?) ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$likeSearch = "%$search%";
$stmt->bind_param("iss", $user_id, $likeSearch, $likeSearch);
$stmt->execute();
$result = $stmt->get_result();

$notes = [];
while ($row = $result->fetch_assoc()) {
    $notes[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode($notes);
