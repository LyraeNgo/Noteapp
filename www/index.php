<?php
	session_start();
	if(!isset($_SESSION['username'])){
		header("Location: /pages/login.php");
		die();
	};

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

	<div class="indexfield">	
		<div class="index-nav" >
			<a class="btn btn-outline-primary border m-3" type="button" href="/pages/logout.php">Signout</a>
			<!-- admin permission -->
			<?php if (isset($_SESSION['username']) && $_SESSION['username'] === 'admin') { ?>
				<div><a href="/admin/db.php" class="btn btn-primary m-3">Go to Admin Page</a></div>
			<?php } ?>
		</div>
		<div class="header">
				<h1>WELCOME TO NOTETATION, <?= $_SESSION['username']?></h1>
		</div>
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<script src="/main.js"></script> <!-- Sử dụng link tuyệt đối tính từ root, vì vậy có dấu / đầu tiên -->

	<div class="create" >
		<button class="btn btn-warning m-3 rounded" id="create"> Create Note</button>
	</div>
	<div class="note-form container border" id="note-form" style="display:none">
		<div class="form-group">
			<input id="title" class="title" type="text" name="title" placeholder="Title">
		</div>
		<div class="form-group">
			<input id="contents" class="contents" type="text" name="contents" placeholder="take notes here">
		</div>
    </div>
	<div class="noteshow">
		<div class="row d-flex justify-content-around">
			<?php for ($i = 0; $i < 10; $i++) {?>
				<!-- test -->
				<div class="col-2 noteblock m-2">
					<div class="titlePlace">Title</div>
					<div class="contentsPlace">hehehhe, <?=$i?></div>
				</div>
			<?php } ?>
		</div>
	</div>
</body>


</html>