<?php
  session_start();
  require_once("./admin/db-con.php");
  if (!isset($_SESSION['username'])) {
    header("Location: /pages/login.php");
    die();
  }


  $conn=create_connection();


  $theme = $_SESSION['theme'] ?? 'light';
  $fontSize = $_SESSION['fontSize'] ?? 'medium';
  $noteColor = $_SESSION['noteColor'] ?? '#ffffff';

// Map font size
  $fontMap = ['small' => '14px', 'medium' => '20px', 'large' => '30px'];
  $fontSizePx = $fontMap[$fontSize] ?? '16px';

?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="/style.css"/>
  <title>Home Page</title>
</head>
<body class="<?= $theme === 'dark' ? 'dark-mode' : '' ?>" style="font-size: <?= $fontSizePx ?>; ">

  <!-- Navigation -->
  <div class="container-fluid bg-light py-2">
  <div class="d-flex justify-content-between align-items-center">
  

    <!-- Left Section -->
    <div>
      <?php if (isset($_SESSION['username']) && $_SESSION['username'] === 'minhtam') { ?>
        <a href="/admin/db.php" class="btn btn-primary m-2">Go to Admin Page</a>
      <?php } ?>
    </div>

    <!-- Right Section -->
    <div class="ml-auto">
      <?php if (!empty($_SESSION['username'])): ?>
        <div class="dropdown">
          <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="profileMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?= htmlspecialchars($_SESSION['username']) ?>
            <i class="fa-solid fa-user"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileMenu">
            <a class="dropdown-item" href="/pages/profile.php"> <i class="fa-solid fa-gear"></i> Profile Settings  </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="../pages/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
          </div>
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>

  <?php 
    $username=$_SESSION['username'];
    $sql='SELECT is_activated FROM user WHERE display_name=?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();


    if($row['is_activated']===0){?>
      <div class="alert alert-warning position-fixed top-0 end-0 m-3 p-3 w-25" style="z-index: 1055;">
  Please activate your account using the email sent to you.
</div>

    <?php }
  ?>
<?php if (isset($_SESSION['label_message'])): ?>
  <div class="container mt-3" >
    <div class="alert alert-<?= $_SESSION['label_message']['type'] ?> alert-dismissible fade show"
         style="position: fixed; top: 20px; right: 20px; z-index: 1055; width: 500px;" role="alert">
      <?= htmlspecialchars($_SESSION['label_message']['text']) ?>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  </div>
  <?php unset($_SESSION['label_message']); ?>
<?php endif; ?>



  <!-- Header -->
  <div class="container text-center my-4 bg-dark text-white">
    <h1 >WELCOME TO NOTETATION, <?= $_SESSION['username'] ?></h1>
  </div>
<!-- Create + Search + View Toggle  -->
<div class="container mb-4">
  <div class="row align-items-center">

    <!-- Create Note Button -->
    <div class="col-md-3 d-flex p-1 ">
            <!-- Create Label Modal -->
<button class="btn btn-secondary m-1 p-1 w-50" data-toggle="modal" data-target="#labelModal"> Labels</button>

<!-- Create Label Modal -->
<div class="modal fade" id="labelModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title">Manage Labels</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">

        <!-- Add Label Form -->
        <form action="./Label/add_label.php" method="POST" class="mb-3 d-flex">
          <input type="text" name="label_name" class="form-control mr-2" placeholder="Label Name" required>
          <input type="color" name="label_color" class="form-control form-control-color mr-2"  title="Choose color">
          <button type="submit" class="btn btn-primary">Add</button>
        </form>

        <!-- Label Table -->
        <?php
          $conn = create_connection();
          $user_id = $_SESSION['user_id'];
          $labelResult = $conn->query("SELECT * FROM label WHERE user_id = $user_id");
        ?>

        <?php if ($labelResult->num_rows > 0): ?>
        <table class="table table-bordered table-sm">
          <thead class="thead-light">
            <tr>
              <th>Label</th>
              <th style="width: 100px;">Color</th>
              <th style="width: 80px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($label = $labelResult->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($label['name']) ?></td>
                <td>
  <span class="badge" style="background-color: <?= htmlspecialchars($label['color']) ?>;">
    <?= htmlspecialchars($label['name']) ?>
  </span>
</td>

                <td>
                  <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editLabelModal<?= $label['id'] ?>">
  <i class="fa fa-edit"></i>
</button>
                  <a href="./Label/delete_label.php?id=<?= $label['id'] ?>" class="btn btn-sm btn-danger btn-delete" onclick="return confirm('Delete this label?');"><i class="fa fa-trash"></i></a>

                  <div class="modal fade" id="editLabelModal<?= $label['id'] ?>" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="./Label/edit_label.php" class="modal-content">
      <input type="hidden" name="label_id" value="<?= $label['id'] ?>">
      <div class="modal-header">
        <h5 class="modal-title">Edit Label</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
  <input type="text" name="label_name" value="<?= htmlspecialchars($label['name']) ?>" class="form-control mb-2" required>
  <input type="color" name="label_color" value="<?= htmlspecialchars($label['color']) ?>" class="form-control form-control-color" title="Choose color">
</div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save changes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
        <?php else: ?>
          <p class="text-muted">No labels yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

      <button type="button" id="create" class="btn btn-warning w-50 m-1" data-toggle="modal" data-target="#myModal">
        Create 
      </button>
    </div>

    <!-- Search Input -->
    <div class="col-md-6">
      <div class="input-group">
        <input type="text" id="searchInput" name="search" class="form-control" placeholder="Search notes..." />
      </div>
    </div>

    <!-- View Toggle Buttons -->
    <div class="col-md-3 ">
      <div class="btn-group w-100 justify-content-end">
        <button class="btn btn-outline-primary active" id="grid-view">
          <i class="fas fa-th-large"></i>
        </button>
        <button class="btn btn-outline-primary" id="list-view">
          <i class="fas fa-list"></i>
        </button>
      </div>
    </div>

  </div>
  <div class="row">
    
  </div>
</div>

  </div>
  <!-- Note Form by TaPu-->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <form id="noteForm" action="Note/save_note.php" method="POST" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <input id="title" class="form-control mb-2" type="text" name="title" placeholder="Title" />
        </div>
        <div class="modal-body">
          <input type="hidden" name="note_id" value="<?= $_GET['id'] ?? '' ?>">
          <textarea name="content" placeholder="Content" id="content" style="width: 100%; height:150px"></textarea>
        </div>
        <!-- 
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
        -->
        <h4 class="p-1 px-3">Label</h4>
        <?php
$conn = create_connection();
$user_id = $_SESSION['user_id'];
$labels = $conn->query("SELECT * FROM label WHERE user_id = $user_id");
?>

<select name="label_ids" multiple class="form-control mb-3" id="noteForm">
  <?php while($label = $labels->fetch_assoc()): ?>
    <option value="<?= $label['id'] ?>"><?= htmlspecialchars($label['name']) ?></option>
  <?php endwhile; ?>
</select>
      </div>
    </form>

  </div>
</div>

<!-- Note Display -->
  <div class="container">
  <div id="notes-container" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4" >
    
    <?php
    require_once('admin/db-con.php');
    $conn = create_connection();
    $user_id = $_SESSION['user_id'] ?? null;
      
    if ($user_id) {
              $sql = "
                SELECT * FROM note 
                WHERE user_id = ? 
                ORDER BY 
                is_pinned DESC, 
                updated_at DESC
      ";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $_SESSION['user_id']);
      $stmt->execute();
      $result = $stmt->get_result();

        while ($note = $result->fetch_assoc()):
    ?>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4 note-item" >
        
        <div class="border rounded p-3 shadow-sm h-100"style="background-color: <?= $noteColor?>;" >
  
  <!-- Title + Pin -->
  <div class="font-weight-bold text-truncate mb-2 d-flex justify-content-between align-items-start" style="max-width: 100%;">
    <div class="text-truncate">
      <?= htmlspecialchars($note['tieu_de']) ?>
      <?php if ($note['is_pinned']): ?>
        <i class="fa-solid fa-thumbtack"></i>
      <?php endif; ?>
    </div>
    <!-- label -->
   <div class="mb-2">
  <?php
    $labelStmt = $conn->prepare("
      SELECT label.name, label.color 
      FROM note_label 
      JOIN label ON note_label.label_id = label.id 
      WHERE note_label.note_id = ?
    ");
    $labelStmt->bind_param("i", $note['id']);
    $labelStmt->execute();
    $labelResult = $labelStmt->get_result();
    while ($label = $labelResult->fetch_assoc()):
  ?>
    
    <span class="badge" style="background-color: <?= htmlspecialchars($label['color']) ?>;">
    <?= htmlspecialchars($label['name']) ?>
  </span>
  <?php endwhile; ?>
</div>
  </div>

  <!-- Content + Kebab -->
  <div class="d-flex justify-content-between align-items-start">
    
    <!-- Note content -->
    <div class="text-muted text-truncate pr-2" style="max-width: 90%;">
      <?= htmlspecialchars($note['noi_dung']) ?>
    </div>
      
    
    <!-- Kebab dropdown -->
    <div class="dropdown">
      <button class="btn btn-sm btn-light p-1" type="button" data-toggle="dropdown" aria-expanded="false">
        <span class="text-dark"><i class="fa-solid fa-ellipsis-vertical"></i></span>
      </button>
      <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="Note/pin_note.php?id=<?= $note['id'] ?>">
  <?php if ($note['is_pinned']): ?>
    <i class="fa-solid fa-circle-xmark"></i> Unpin
  <?php else: ?>
    <i class="fa-solid fa-thumbtack"></i> Pin
  <?php endif; ?>
</a>  


<div>
  <?php while ($label = $labelResult->fetch_assoc()): ?>
    <span class="badge badge-info"><?= htmlspecialchars($label['name']) ?></span>
  <?php endwhile; ?>
</div>


        <a class="dropdown-item btn-edit-note" href="#" data-id="<?= $note['id'] ?>"><i class="fa-solid fa-pen"></i> Modify</a>
        <a class="dropdown-item text-danger delete-note" href="#" data-note-id="<?= $note['id'] ?>">
          <i class="fa-solid fa-trash-can"></i> Delete
        </a>
      </div>
    </div>
  </div>

  <div id="editNoteModalContainer"></div>
</div>

      </div>
          <?php
              endwhile;
              $stmt->close();
          } else {
              echo '<p class="text-white">Vui lòng đăng nhập để xem ghi chú.</p>';
          }

          $conn->close();
        ?>

        
        
        </div>
      </div>
    </div>
  </div>
          
  <!-- Scripts -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="/main.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- Toast Notification -->
  <div aria-live="polite" aria-atomic="true" style="position: fixed; top: 20px; right: 20px; min-width: 250px; z-index: 9999;">
    <div id="toast-container"></div>
  </div>
  <script>
    window.userPreference = {
      noteColor: '<?= $noteColor ?>',
      fontSize: '<?= $fontSizePx ?>',
      theme: '<?= $theme ?>'
    };
  </script>
</body>
</html>
