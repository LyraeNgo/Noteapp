<?php
session_start();
require_once("../admin/db-con.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: /pages/login.php");
    exit();
}

$conn = create_connection();
$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $label_id = $_POST['label_id'] ?? null;
    $new_name = trim($_POST['label_name'] ?? '');

    if ($label_id && $new_name !== '') {
        $stmt = $conn->prepare("UPDATE label SET name = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $new_name, $label_id, $user_id);
        if ($stmt->execute()) {
            $_SESSION['label_message'] = [
                'type' => 'success',
                'text' => 'Label updated successfully.'
            ];
        } else {
            $_SESSION['label_message'] = [
                'type' => 'danger',
                'text' => 'Failed to update label.'
            ];
        }
        $stmt->close();
    } else {
        $_SESSION['label_message'] = [
            'type' => 'warning',
            'text' => 'Invalid label data.'
        ];
    }

    header("Location: ../index.php"); // or wherever you list the labels
    exit();
}

// If reached here without POST, redirect
header("Location: ../index.php");
exit();
?>
