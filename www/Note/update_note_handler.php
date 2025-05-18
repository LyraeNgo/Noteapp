<?php
session_start();
require_once('../admin/db-con.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = create_connection();
$user_id = $_SESSION['user_id'];
$note_id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $label_ids = $_POST['labels'] ?? [];
    $delete_images = $_POST['delete_images'] ?? [];

    try {
        $conn->begin_transaction();

        // Cập nhật nội dung ghi chú
        $stmt = $conn->prepare("UPDATE note SET tieu_de = ?, noi_dung = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssii", $title, $content, $note_id, $user_id);
        $stmt->execute();

        // Xóa ảnh nếu người dùng chọn
        foreach ($delete_images as $img_id) {
            $stmt = $conn->prepare("DELETE FROM note_image WHERE id = ? AND note_id = ?");
            $stmt->bind_param("ii", $img_id, $note_id);
            $stmt->execute();
        }

        // Thêm ảnh mới
        if (!empty($_FILES['new_images']['name'][0])) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($_FILES['new_images']['tmp_name'] as $key => $tmp_name) {
                $filename = uniqid() . '_' . basename($_FILES['new_images']['name'][$key]);
                $target_path = $uploadDir . $filename;

                if (move_uploaded_file($tmp_name, $target_path)) {
                    $path = 'uploads/' . $filename;
                    $stmt = $conn->prepare("INSERT INTO note_image (note_id, path) VALUES (?, ?)");
                    $stmt->bind_param("is", $note_id, $path);
                    $stmt->execute();
                }
            }
        }

        // Cập nhật lại nhãn
        $stmt = $conn->prepare("DELETE FROM note_label WHERE note_id = ?");
        $stmt->bind_param("i", $note_id);
        $stmt->execute();

        if (!empty($label_ids)) {
            $stmt = $conn->prepare("INSERT INTO note_label (note_id, label_id) VALUES (?, ?)");
            foreach ($label_ids as $label_id) {
                $stmt->bind_param("ii", $note_id, $label_id);
                $stmt->execute();
            }
        }

        $conn->commit();
        $_SESSION['success'] = "Cập nhật ghi chú thành công!";
        header("Location: notes.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        die("Lỗi cập nhật: " . $e->getMessage());
    }
} else {
    http_response_code(405);
    echo "Method Not Allowed";
}
