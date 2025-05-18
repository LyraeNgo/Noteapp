<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: /pages/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['theme'] = isset($_POST['theme']) && $_POST['theme'] === 'dark' ? 'dark' : 'light';
    $_SESSION['fontSize'] = $_POST['font_size'] ?? 'medium';
    $_SESSION['noteColor'] = $_POST['note_color'] ?? '#ffffff';

    header("Location: profile.php");
    exit();
}

$defaultFontSize = $_SESSION['fontSize'] ?? 'medium';
$defaultNoteColor = $_SESSION['noteColor'] ?? '#ffffcc';
$defaultTheme = $_SESSION['theme'] ?? 'light';
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
  <title>Profile</title>
</head>
<body class="<?= $defaultTheme === 'dark' ? 'dark-mode' : '' ?>">

<div class="container border my-4 p-4">
  <h2 class="mb-4 text-center">SETTING</h2>

  <div class="d-flex justify-content-center">
    <form method="post" action="" class="w-50">
      <div class="form-group">
        <label for="fontSize">Font Size</label>
        <select class="form-control rounded" id="fontSize" name="font_size">
          <option value="small" <?= $defaultFontSize === 'small' ? 'selected' : '' ?>>Small</option>
          <option value="medium" <?= $defaultFontSize === 'medium' ? 'selected' : '' ?>>Medium</option>
          <option value="large" <?= $defaultFontSize === 'large' ? 'selected' : '' ?>>Large</option>
        </select>
      </div>

      <div class="form-group">
        <label for="noteColor">Note Color</label>
        <input type="color" id="noteColor" name="note_color" value="<?= htmlspecialchars($defaultNoteColor) ?>" class="form-control rounded" />
      </div>

      <div class="form-group">
        <label for="themeToggle">Dark Mode</label><br>
        <label class="switch">
          <input type="checkbox" name="theme" value="dark" onchange="this.form.submit()" <?= $defaultTheme === 'dark' ? 'checked' : '' ?>>
          <span class="slider round"></span>
        </label>
        
      </div>


      <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary">Save Preferences</button>
        <a href="../index.php" class="btn btn-outline-primary">Back</a>
      </div>
    </form>
  </div>

  <hr>
  <h4 class="text-center"> Preview</h4>
  <div id="notePreview" class="note-preview mt-3 mx-auto p-3 rounded" style="max-width: 500px; background-color: <?= htmlspecialchars($defaultNoteColor) ?>; font-size: <?= $defaultFontSize === 'large' ? '20px' : ($defaultFontSize === 'small' ? '14px' : '16px') ?>;">
    <p class="note-text mb-0 mx-auto">This is how your notes will look.</p>
  </div>
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="/main.js"></script>
</body>
</html>
