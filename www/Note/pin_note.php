<?php
require_once('../admin/db-con.php');
session_start();

if (isset($_GET['id'])) {
    $note_id = (int) $_GET['id'];
    $conn = create_connection();

   
    $stmt = $conn->prepare("SELECT is_pinned FROM note WHERE id = ?");
    $stmt->bind_param("i", $note_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $note = $result->fetch_assoc();

    if ($note) {
        if ($note['is_pinned']) {
            
            $stmt = $conn->prepare("UPDATE note SET is_pinned = 0 WHERE id = ?");
            $stmt->bind_param("i", $note_id);
        } else {
            
            $stmt = $conn->prepare("UPDATE note SET is_pinned = 1 WHERE id = ?");
            $stmt->bind_param("i", $note_id);
        }
        $stmt->execute();
    }

    $conn->close();
}

header("Location: ../index.php");
exit;
?>