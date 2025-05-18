<?php
session_start();
require_once('../admin/db-con.php');

$note_id = $_GET['id'] ?? null;
echo $note_id;
$note = null;

if ($note_id) {
    $conn = create_connection();
    if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized";
    exit;
    }
    $stmt = $conn->prepare("SELECT * FROM note WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $note_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $note = $result->fetch_assoc();
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

        <div class="modal-body">
          <input type="file" name="image" id="imageInput" class="custom-file-input" accept="image/*">
          <label for="imageInput" class="custom-file-label" style="padding:8px 12px;">Choose Image</label>
          <br><br>
          <span id="file-name" style="color:red;"></span>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-secondary" id="saveBtn" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>

  </div>
</div>