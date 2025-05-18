<?php
session_start();
require_once('../admin/db-con.php');
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
$user_id = $_SESSION['user_id'];
$search = $_GET['q'] ?? '';
$conn = create_connection();

// Tìm kiếm theo tiêu đề, nội dung và label
$sql = "SELECT DISTINCT n.tieu_de, n.noi_dung, n.created_at, n.updated_at, n.is_pinned 
        FROM note n 
        LEFT JOIN note_label nl ON n.id = nl.note_id 
        LEFT JOIN label l ON nl.label_id = l.id 
        WHERE n.user_id = ? 
        AND (
            n.tieu_de LIKE ? 
            OR n.noi_dung LIKE ? 
            OR l.name LIKE ?
        ) 
        ORDER BY n.is_pinned DESC, n.updated_at DESC";

$stmt = $conn->prepare($sql);
$likeSearch = "%$search%";
$stmt->bind_param("isss", $user_id, $likeSearch, $likeSearch, $likeSearch);
$stmt->execute();
$result = $stmt->get_result();
$notes = [];
while ($row = $result->fetch_assoc()) {
    $notes[] = $row;
}
$stmt->close();
$conn->close();
echo json_encode($notes); 