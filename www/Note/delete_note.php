<?php
session_start();
require_once("../admin/db-con.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if note_id is provided
if (!isset($_POST['note_id'])) {
    echo json_encode(['success' => false, 'message' => 'Note ID is required']);
    exit;
}

$note_id = $_POST['note_id'];
$user_id = $_SESSION['user_id'];

// Create database connection
$conn = create_connection();

// Prepare and execute delete query
$stmt = $conn->prepare("DELETE FROM note WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $note_id, $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Note deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Note not found or you do not have permission to delete it']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting note']);
}

$stmt->close();
$conn->close();
?>
