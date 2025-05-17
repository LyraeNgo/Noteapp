<?php
session_start();

require_once("../admin/db-con.php");
$conn=create_connection();
if (!$conn) {
    die("Connection failed.");
}
$user="";
$pass= "";
if (isset($_POST['username']) && isset($_POST['password'])) {
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);

    if (empty($user) && empty($pass)) {
        $error = 'Please enter Username and Password';
    } elseif (empty($user)) {
        $error = 'Please enter your username';
    } elseif (empty($pass)) {
        $error = 'Please enter your password';
    } else {
        // Get user record by username
        $stmt = $conn->prepare("SELECT * FROM user WHERE display_name = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();  

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Now verify the password
            if (password_verify($pass, $row['password'])) {
                $_SESSION['username'] = $row['display_name'];
                header("Location: /index.php");
                exit(); 
            } else {
                $error = 'Incorrect username or password';
            }
        } else {
            $error = 'Incorrect username or password';
        }

        $stmt->close();
    }
}


$conn->close();
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

        <div class="container ">
            <div class="row justify-content-center align-items-center  " style="height:100vh">
                <div class="col-lg-5 border p-3">
                    <h2  class="text-center mx-5">NOTE MANAGEMENT APPLICATION</h2>
                    <form method="post" action="">
                        <div class="form-group loginform" >
                            <label for="username">Username</label>
                            <input id="username" class="form-control" type="text" name="username" value="<?= $user?>">
                        </div>
                        <div class="form-group loginform mb-0">
                            <label for="password">Password</label>
                            <input id="password" class="form-control" type="password" name="password" value="<?= $pass?>">
                        </div>
                        <div class="form-group ">
                            <a href="fogotpass.php"><small>Fogot Password?</small></a>
                        </div>
                        <div class="form-group loginform">
                            <?php
                                if(!empty($error)){
                                    echo "<div class='alert alert-danger'>$error</div>";
                                };
                            ?>
                        </div>
                        <button class="btn btn-success px-5" style="width: 100%;">Login</button>
                        <div class="form-group ">
                            <small>Need an account? <a href="/pages/Register.php">Register</a></small>
                        </div>
                        
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