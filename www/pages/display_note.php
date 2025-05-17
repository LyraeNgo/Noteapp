<?php
require_once('../admin/db-con.php');
$conn = create_connection();

session_start();
$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT id, tieu_de, noi_dung FROM note WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($note = $result->fetch_assoc()) {
?>
  <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
    <div class="border rounded p-3 shadow-sm h-100 bg-white">
        
      <!-- Title & 3-dot menu -->
      <div class="d-flex justify-content-between align-items-start">
        <div class="font-weight-bold text-truncate" style="max-width: 85%;">
          <?= htmlspecialchars($note['tieu_de']) ?>
        </div>

        <div class="dropdown">
          <button class="btn btn-sm btn-light p-1" type="button" data-toggle="dropdown" aria-expanded="false">
            <span class="text-dark"><i class="fa-solid fa-ellipsis-vertical"></i></span>
          </button>
          <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="#">Modify</a>
            <a class="dropdown-item text-danger" href="#">Delete</a>
          </div>
        </div>
      </div>

      <!-- Note content -->
      <div class="text-muted mt-2 text-truncate" style="max-height: 4.5em; overflow: hidden;">
        <?= nl2br(htmlspecialchars($note['noi_dung'])) ?>
      </div>
      <?php if (!empty($note['image_path'])): ?>
        <img src="<?= htmlspecialchars($note['image_path']) ?>" class="img-fluid mt-2 rounded">
      <?php endif; ?>
    </div>
  </div>
<?php
    }
    $stmt->close();
} else {
    echo "<p>Vui lòng đăng nhập để xem ghi chú.</p>";
}
$conn->close();
?>
