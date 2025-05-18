<?php
session_start();
require_once('../admin/db-con.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = create_connection();
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        die("User not authenticated.");
    }

    $note_id = isset($_POST['note_id']) ? (int)$_POST['note_id'] : null;
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $label_ids = isset($_POST['label_ids']) && is_array($_POST['label_ids']) ? $_POST['label_ids'] : [];
    $delete_images = isset($_POST['delete_images']) && is_array($_POST['delete_images']) ? $_POST['delete_images'] : [];

    try {
        $conn->begin_transaction();

        // Create or update the note
        if ($note_id) {
            $stmt = $conn->prepare("UPDATE note SET tieu_de = ?, noi_dung = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
            if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
            $stmt->bind_param("ssii", $title, $content, $note_id, $user_id);
            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
            $stmt->close();
            $is_update = true;
        } else {
            $stmt = $conn->prepare("INSERT INTO note (user_id, tieu_de, noi_dung, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
            $stmt->bind_param("iss", $user_id, $title, $content);
            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
            $note_id = $stmt->insert_id;
            $stmt->close();
            $is_update = false;
        }

        // Delete selected images
        if (!empty($delete_images)) {
            $stmt = $conn->prepare("DELETE FROM note_image WHERE id = ? AND note_id = ?");
            if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
            foreach ($delete_images as $img_id) {
                $img_id = (int)$img_id;
                $stmt->bind_param("ii", $img_id, $note_id);
                if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
            }
            $stmt->close();
        }

        // Upload new image if available
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $filename = uniqid() . '_' . basename($_FILES['image']['name']);
            $target_path = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $path = 'uploads/' . $filename;
                $stmt = $conn->prepare("INSERT INTO note_image (note_id, path) VALUES (?, ?)");
                if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
                $stmt->bind_param("is", $note_id, $path);
                if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
                $stmt->close();
            }
        }

        // Manage note labels
        $stmt = $conn->prepare("DELETE FROM note_label WHERE note_id = ?");
        if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
        $stmt->bind_param("i", $note_id);
        if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
        $stmt->close();

        if (!empty($label_ids)) {
            $stmt = $conn->prepare("INSERT INTO note_label (note_id, label_id) VALUES (?, ?)");
            if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
            foreach ($label_ids as $label_id) {
                $label_id = (int)$label_id;
                $stmt->bind_param("ii", $note_id, $label_id);
                if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
            }
            $stmt->close();
        }

        $conn->commit();

        $_SESSION['success'] = $is_update ? "Note updated successfully." : "Note created successfully.";
        header("Location: /index.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        die("Error processing note: " . $e->getMessage());
    }

} else {
    http_response_code(405);
    echo "Method Not Allowed";
}
