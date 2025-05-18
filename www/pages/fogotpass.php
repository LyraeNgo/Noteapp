<?php
require_once("../admin/db-con.php");
require '../vendor/autoload.php';
$conn = create_connection();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function generateRandomString($length = 32) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}

function send_mail($to, $subject, $body) {
    $emailSender = "ngominhtam26112005@gmail.com";
    $password = "nrekywgaozbbxaii";
    $first_name = "Tam";
    $last_name = "Ngo";

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $emailSender;
        $mail->Password = $password;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->setFrom($emailSender, "$first_name $last_name");
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false; // Optional: Log error
    }
}



$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    $stmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $token = generateRandomString();
        $expires = date("Y-m-d H:i:s", time() + 3600);

        $stmt = $conn->prepare("UPDATE user SET reset_token = ?, reset_expires = ? WHERE email = ?");
        $stmt->bind_param("sss", $token, $expires, $email);
        $stmt->execute();

        $resetLink = "http://localhost:8080/pages/reset_password.php?token=$token";

        // Send email (use PHPMailer in production)
        $subject = "Password Reset";
        $message = "Click the link to reset your password: $resetLink";
        send_mail($email, $subject, $message);

        $info = "A password reset link has been sent to your email.";
    } else {
        $error = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <h3 class="mb-4 text-center">Forgot Your Password?</h3>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php elseif (!empty($info)): ?>
                        <div class="alert alert-success"><?= $info ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-group">
                            <label for="email">Enter your registered email</label>
                            <input type="email" class="form-control form-control-lg" name="email" id="email" required placeholder="enter email here">
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block mt-3">Send Reset Link</button>
                    </form>

                    <div class="mt-4 text-center">
                        <a href="/pages/Login.php">← Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
