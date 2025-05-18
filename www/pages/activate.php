<?php
require_once "../admin/db-con.php";
$conn = create_connection();
$error = '';

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid Email';
    } elseif (strlen($token) != 32) {
        $error = 'Invalid token format';
    } else {
        $sql = 'SELECT display_name FROM user WHERE email = ? AND token = ? AND is_activated = 0';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $email, $token);
        if (!$stmt->execute()) {
            $error = 'Failed to execute select query.';
        } else {
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $error = 'Email or token is invalid or already activated.';
            } else {
                $sql = "UPDATE user SET is_activated = 1, token = '' WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $email);
                if (!$stmt->execute()) {
                    $error = 'Failed to activate account.';
                }
            }
        }
    }
} else {
    $error = 'Invalid URL';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Activation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php else: ?>
        <div class="alert alert-success">Your account has been successfully activated!</div>
    <?php endif; ?>
</div>
</body>
</html>
