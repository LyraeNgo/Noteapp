<?php
require_once("../admin/db-con.php");
$conn = create_connection();

$token = $_GET["token"] ?? '';
$valid = false;
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "GET" && $token) {
    $stmt = $conn->prepare("SELECT id FROM user WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $valid = $stmt->get_result()->num_rows === 1;
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST["token"];
    $newPassword = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE user SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?");
    $stmt->bind_param("ss", $newPassword, $token);
    $stmt->execute();

    $success = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-4 text-center">Reset Password</h4>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        Your password has been reset successfully. <a href="/pages/Login.php">Login here</a>.
                    </div>
                <?php elseif ($valid): ?>
                    <form method="POST">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" name="password" id="password" class="form-control" required placeholder="Enter new password">
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Reset Password</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-danger">The reset link is invalid or has expired.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
