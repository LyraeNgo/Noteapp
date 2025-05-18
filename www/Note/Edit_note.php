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

$note = [];
$images = [];
$current_labels = [];
$all_labels = [];

try {
    $stmt = $conn->prepare("
        SELECT n.*, GROUP_CONCAT(nl.label_id) AS label_ids 
        FROM note n
        LEFT JOIN note_label nl ON n.id = nl.note_id
        WHERE n.id = ? AND n.user_id = ?
        GROUP BY n.id
    ");
    $stmt->bind_param("ii", $note_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();



    $note = $result->fetch_assoc();
    $current_labels = explode(',', $note['label_ids'] ?? '');

    $stmt = $conn->prepare("SELECT * FROM note_image WHERE note_id = ?");
    $stmt->bind_param("i", $note_id);
    $stmt->execute();
    $images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM label WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $all_labels = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    die("Lỗi: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $new_labels = $_POST['labels'] ?? [];
    $delete_images = $_POST['delete_images'] ?? [];

    try {
        $conn->begin_transaction();

        $stmt = $conn->prepare("
            UPDATE note 
            SET tieu_de = ?, noi_dung = ?, updated_at = NOW()
            WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param("ssii", $title, $content, $note_id, $user_id);
        $stmt->execute();

        foreach ($delete_images as $image_id) {
            $stmt = $conn->prepare("DELETE FROM note_image WHERE id = ? AND note_id = ?");
            $stmt->bind_param("ii", $image_id, $note_id);
            $stmt->execute();
        }

        if (!empty($_FILES['new_images']['name'][0])) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($_FILES['new_images']['tmp_name'] as $key => $tmp_name) {
                $filename = uniqid() . '_' . basename($_FILES['new_images']['name'][$key]);
                $target_path = $uploadDir . $filename;

                if (move_uploaded_file($tmp_name, $target_path)) {
                    $stmt = $conn->prepare("INSERT INTO note_image (note_id, path) VALUES (?, ?)");
                    $image_path = 'uploads/' . $filename;
                    $stmt->bind_param("is", $note_id, $image_path);
                    $stmt->execute();
                }
            }
        }

        $stmt = $conn->prepare("DELETE FROM note_label WHERE note_id = ?");
        $stmt->bind_param("i", $note_id);
        $stmt->execute();

        if (!empty($new_labels)) {
            $stmt = $conn->prepare("INSERT INTO note_label (note_id, label_id) VALUES (?, ?)");
            foreach ($new_labels as $label_id) {
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
        $error = "Lỗi cập nhật: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa ghi chú</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-4">Chỉnh sửa ghi chú</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form id="noteForm" method="POST" enctype="multipart/form-data" >
            <div class="mb-3">
                <label class="form-label">Tiêu đề</label>
                <input type="text" name="title" class="form-control" 
                       value="<?= htmlspecialchars($note['tieu_de']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nội dung</label>
                <textarea name="content" class="form-control" rows="5"><?= htmlspecialchars($note['noi_dung']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Hình ảnh đính kèm</label>
                <div class="row g-3">
                    <?php foreach ($images as $image): ?>
                        <div class="col-6 col-md-4">
                            <div class="card">
                                <img src="<?= htmlspecialchars($image['path']) ?>" class="card-img-top" alt="Hình ảnh">
                                <div class="card-body text-center">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteImage(<?= $image['id'] ?>)">Xóa</button>
                                    <input type="checkbox" name="delete_images[]" value="<?= $image['id'] ?>" id="delete_<?= $image['id'] ?>" style="display: none;">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Thêm hình ảnh mới</label>
                <input type="file" name="new_images[]" class="form-control" multiple accept="image/*">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"   >Lưu thay đổi</button>
                <a href="/index.php" class="btn btn-secondary">Hủy bỏ</a>
            </div>
        </form>
    </div>


</body>
</html>
