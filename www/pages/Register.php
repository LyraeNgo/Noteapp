<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require_once('../admin/db-con.php');

$conn = create_connection();
$error = '';
$username = '';
$emailUser = '';
$password = '';
function generateRandomString($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $emailUser = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirmpass'] ?? '';
    $token=generateRandomString();
    if (empty($username) || empty($emailUser) || empty($password) || empty($confirm)) {
        $error = "Please enter username, email, and password.";
    } elseif ($password !== $confirm) {
        $error = "Confirm password does not match.";
    } else {
        // Check if email or username already exists
        $stmt = $conn->prepare("SELECT * FROM user WHERE email = ? OR display_name = ?");
        $stmt->bind_param("ss", $emailUser, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "This email or username is already used!";
        } else {
            
            // Insert new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Secure password
            $stmt = $conn->prepare("INSERT INTO user (display_name, email, password,is_activated,token) VALUES (?, ?, ?,0,'$token')");
            $stmt->bind_param("sss", $username, $emailUser, $hashedPassword);

            if ($stmt->execute()) {
                // Send welcome email
                $subject = "Welcome to Our Website!";
                $body = "<h1>Hello, $username!</h1><p>Thank you for registering with us. Please activate your account by clicking to the link below.</p></br><a href='Noteapp\www\pages\acitvate.php'> here</a>";
                send_mail($emailUser, $subject, $body);

                // Redirect to index
                $_SESSION['username']=$username;

                header("Location: /index.php");
                exit;
            } else {
                $error = "Database error: " . $stmt->error;
            }
        }
        $stmt->close();
    }
    $conn->close();
}

// Send email function
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
?>




<!DOCTYPE html>   
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="/style.css"> <!-- Sử dụng link tuyệt đối tính từ root, vì vậy có dấu / đầu tiên -->
	<title>Home Page</title>
</head>

<body>

	<div class="container  ">
        <div class="row justify-content-center align-items-center  " style="height:100vh">
            <div class="col-lg-5 border p-3">
                <h2  class="text-center mx-5">Create an account</h2>
		        <form method="post" action="">
                    <div class="form-group loginform" >
                        <label for="email">EMAIL</label>
                        <input id="email" class="form-control" value="<?= $emailUser ?>" type="email" name="email" >
                    </div>
                    <div class="form-group">
                        <label for="username">USERNAME</label>
                        <input id="username" class="form-control" value="<?= $username?>" type="text" name="username">
                    </div>
                    <div class="form-group loginform ">
                        <label for="password">PASSWORD</label>
                        <input id="password" class="form-control" type="password" name="password">
                    </div>
                    <div class="form-group loginform ">
                        <label for="confirmPassword">CONFIRM PASSWORD</label>
                        <input id="confirmPassword" class="form-control" type="password" name="confirmpass">
                    </div>
                    
                    <div class="form-group loginform">
                        <?php
                            if(!empty($error)){
                                echo "<div class='alert alert-danger mb-0'>$error</div>";
                            };
                        ?>
                    </div>
                    <button type="submit" name="register" class="btn btn-success px-5" style="width: 100%;">Submit</button>
                </form>
            </div> 

        </div>
        
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<script src="/main.js"></script> <!-- Sử dụng link tuyệt đối tính từ root, vì vậy có dấu / đầu tiên -->
</body>


</html>