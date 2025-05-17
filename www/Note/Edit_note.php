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
$all_labels = [];
$current_labels = [];

try {
    // Lấy thông tin ghi chú
    $stmt = $conn->prepare("SELECT * FROM note WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $note_id, $user_id);
    $stmt->execute();
    $note = $stmt->get_result()->fetch_assoc();

    // Lấy hình ảnh
    $stmt = $conn->prepare("SELECT * FROM note_image WHERE note_id = ?");
    $stmt->bind_param("i", $note_id);
    $stmt->execute();
    $images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Lấy tất cả label của user
    $stmt = $conn->prepare("SELECT * FROM label WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $all_labels = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Lấy các label đang gán cho note
    $stmt = $conn->prepare("SELECT label_id FROM note_label WHERE note_id = ?");
    $stmt->bind_param("i", $note_id);
    $stmt->execute();
    $current_labels_result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $current_labels = array_column($current_labels_result, 'label_id');

} catch (Exception $e) {
    die("Lỗi: " . $e->getMessage());
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

    <form method="POST" action="update_note_handler.php?id=<?= $note_id ?>" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Tiêu đề</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($note['tieu_de']) ?>"  required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nội dung</label>
            <textarea name="content" class="form-control" rows="5">
                <?= htmlspecialchars($note['tieu_de']) ?>
            </textarea>
        </div>

        

        <div class="mb-3">
            <label class="form-label">Hình ảnh đính kèm</label>
            <div class="row">
                <?php foreach ($images as $image): ?>
                    <div class="col-md-4">
                        <img src="<?= $image['path'] ?>" class="img-fluid mb-2" />
                        <div class="form-check">
                            <input type="checkbox" name="delete_images[]" value="<?= $image['id'] ?>" class="form-check-input">
                            <label class="form-check-label">Xóa ảnh này</label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Thêm hình ảnh mới</label>
            <input type="file" name="new_images[]" multiple class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="notes.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>
</body>
</html>
