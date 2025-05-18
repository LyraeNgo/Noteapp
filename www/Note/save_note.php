<?php
session_start();
require_once('../admin/db-con.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = create_connection();

    $user_id = $_SESSION['user_id'];
    $note_id = $_POST['note_id'] ?? null;
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $label_ids = $_POST['labels'] ?? [];
    $delete_images = $_POST['delete_images'] ?? [];

    try {
        $conn->begin_transaction();

        // INSERT or UPDATE NOTE
        if ($note_id) {
            $stmt = $conn->prepare("UPDATE note SET tieu_de = ?, noi_dung = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ssii", $title, $content, $note_id, $user_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO note (user_id, tieu_de, noi_dung, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            $stmt->bind_param("iss", $user_id, $title, $content);
        }

        $stmt->execute();
        if (!$note_id) {
            $note_id = $stmt->insert_id;
        }
        $stmt->close();

        // DELETE selected images
        if (!empty($delete_images)) {
            foreach ($delete_images as $img_id) {
                $stmt = $conn->prepare("DELETE FROM note_image WHERE id = ? AND note_id = ?");
                $stmt->bind_param("ii", $img_id, $note_id);
                $stmt->execute();
                $stmt->close();
            }
        }

        // ADD new image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $filename = uniqid() . '_' . basename($_FILES['image']['name']);
            $target_path = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $path = 'uploads/' . $filename;
                $stmt = $conn->prepare("INSERT INTO note_image (note_id, path) VALUES (?, ?)");
                $stmt->bind_param("is", $note_id, $path);
                $stmt->execute();
                $stmt->close();
            }
        }

        // LABELS
        $stmt = $conn->prepare("DELETE FROM note_label WHERE note_id = ?");
        $stmt->bind_param("i", $note_id);
        $stmt->execute();
        $stmt->close();

        if (!empty($label_ids)) {
            $stmt = $conn->prepare("INSERT INTO note_label (note_id, label_id) VALUES (?, ?)");
            foreach ($label_ids as $label_id) {
                $stmt->bind_param("ii", $note_id, $label_id);
                $stmt->execute();
            }
            $stmt->close();
        }

        $conn->commit();
        $_SESSION['success'] = $note_id ? "Success modify" : "Success create";
        header("Location: /index.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Lỗi xử lý: " . $e->getMessage());
    }

    $conn->close();
    
} else {
    http_response_code(405);
    echo "Method Not Allowed";
}

<?php
session_start();
require_once('../admin/db-con.php');
if(!isset($_SESSION['username'])){
    header('Location: ./pages/login.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = create_connection();
    
    $display_name =$_SESSION['username'];
    $user_id = $_SESSION['user_id'];
    
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $label_ids = $_POST['labels'] ?? [];

    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/uploads/';
    

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $image_path = uniqid() . '_' . basename($_FILES['image']['name']);
    $target_path = $uploadDir . $image_path;
    move_uploaded_file($_FILES['image']['tmp_name'], $target_path);
    
    
    $image_path = 'uploads/' . $image_path;
    }

    try {
        $conn->begin_transaction();

        $stmt = $conn->prepare("INSERT INTO note (user_id, tieu_de, noi_dung, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("iss", $user_id, $title, $content);
        $stmt->execute();
        $note_id = $conn->insert_id;
        $stmt->close();

        if ($image_path) {
            $stmt = $conn->prepare("INSERT INTO note_image (note_id, path) VALUES (?, ?)");
            $stmt->bind_param("is", $note_id, $image_path);
            $stmt->execute();
            $stmt->close();
        }

        
        if (!empty($display_name)) {
            $stmt = $conn->prepare("INSERT INTO label (user_id, name) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $display_name);
            $stmt->execute();
            $new_label_id = $conn->insert_id;
            $stmt->close();

            // Thêm label mới vào mảng label_ids để liên kết với note
            $label_ids[] = $new_label_id;
        }


        if (!empty($label_ids)) {
            $stmt = $conn->prepare("INSERT INTO note_label (note_id, label_id) VALUES (?, ?)");
            foreach ($label_ids as $label_id) {
                $stmt->bind_param("ii", $note_id, $label_id);
                $stmt->execute();
            }
            $stmt->close();
        }

        
       

    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Note saved successfully.']);

    

} else {
    http_response_code(405);
    echo "Method Not Allowed";
}
$conn->close();
?>

