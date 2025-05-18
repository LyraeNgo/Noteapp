<?php
// Kết nối cơ sở dữ liệu
require_once '../db_con.php'; // Thay bằng file kết nối của bạn

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $img = $_POST['img'] ?? '';
    $noteId = $_POST['note_id'] ?? '';

    if (empty($img) || empty($noteId)) {
        echo 'missing data';
        exit;
    }

    // Tránh SQL injection
    $img = trim($img);
    $noteId = intval($noteId);

    try {
        // Truy vấn kiểm tra ảnh có tồn tại không
        $stmt = $pdo->prepare("SELECT * FROM note_image WHERE note_id = :note_id AND path = :path LIMIT 1");
        $stmt->execute([
            ':note_id' => $noteId,
            ':path' => $img
        ]);
        $image = $stmt->fetch();

        if ($image) {
            // Xoá file vật lý
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($img, '/');
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Xoá bản ghi trong DB
            $stmt = $pdo->prepare("DELETE FROM note_image WHERE id = :id");
            $stmt->execute([':id' => $image['id']]);

            echo 'success';
        } else {
            echo 'not found';
        }

    } catch (PDOException $e) {
        error_log('Delete image error: ' . $e->getMessage());
        echo 'error';
    }
} else {
    echo 'invalid method';
}