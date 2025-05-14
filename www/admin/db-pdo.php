<?php

	#  test kết nối đến mysql server bằng PDO
 
    $host = 'mysql-server'; // tên mysql server
    $user = 'root';
    $pass = 'root';
    $db = 'note_management'; // tên databse

	try {
		$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
		// set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		echo "Kết nối thành công tới database bằng PDO<br><br>";
	} catch(PDOException $e) {
		echo "Kết nối PDO thất bại: " . $e->getMessage();
		die();
	}

?>
