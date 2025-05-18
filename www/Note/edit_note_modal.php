<?php
session_start();
require_once('../admin/db-con.php');

$note_id = $_GET['id'] ?? null;
$note = null;
$images = [];

if ($note_id) {
    $conn = create_connection();

    // Kiểm tra đăng nhập
    if (!isset($_SESSION['user_id'])) {
        echo "Unauthorized";
        exit;
    }

    // Lấy nội dung ghi chú
    $stmt = $conn->prepare("SELECT * FROM note WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $note_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $note = $result->fetch_assoc();
    $stmt->close();

    // Lấy ảnh liên kết với note
    $stmt = $conn->prepare("SELECT * FROM note_image WHERE note_id = ?");
    $stmt->bind_param("i", $note_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $images[] = $row['path'];
    }
    $stmt->close();

    $conn->close();
}
?>
<div id="editNoteModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <form id="noteForm" action="Note/save_note.php" method="POST" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <input id="title" class="form-control mb-2" type="text" name="title" placeholder="Title" 
            value="<?= htmlspecialchars($note['tieu_de'] ?? '') ?>" required />
        </div>
        <div class="modal-body">
          <input type="hidden" name="note_id" value="<?= htmlspecialchars($note['id'] ?? '') ?>">
          <textarea name="content" placeholder="Content" id="content" style="width: 100%; height:150px"><?= htmlspecialchars($note['noi_dung'] ?? '') ?></textarea>
        </div>
        <?php if (!empty($images)): ?>
          <div id="existing-images" class="mb-3">
            <label>Attached Images:</label>
              <?php foreach ($images as $index => $img): ?>
              <div class="mb-2 d-flex align-items-start">
                <img src="<?= htmlspecialchars($img) ?>" alt="Note Image" style="max-width: 100px; max-height: 100px;" class="mr-2">
                
                <div>
                  <div title="<?= htmlspecialchars($img) ?>">
                    <?= htmlspecialchars(strlen($img) > 30 ? '...' . substr($img, -30) : $img) ?>
                  </div>
                  <button type="button" class="btn btn-sm btn-danger mt-1 delete-image-btn" 
                          data-img="<?= htmlspecialchars($img) ?>">Delete</button>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        <div class="custom-file mb-3">
          <input type="file" name="images[]" id="imageInput" class="custom-file-input" accept="image/*" multiple>
          <label class="custom-file-label bg-primary text-white" for="imageInput">Choose images</label>
        </div>
        <br><br>
        <div id="file-name" class="mt-2 text-muted small"></div>
              
                      
        <div class="modal-footer">
          <button type="submit" class="btn btn-success" href = "">Save</button>
          <button type="button" class="btn btn-secondary" id="saveBtn" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>

  </div>
</div>