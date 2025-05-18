<?php
	session_start();

	#  test kết nối đến mysql server bằng MySQLi
 
    $host = 'mysql-server'; // tên mysql server
    $user = 'root';
    $pass = 'root';
    $db = 'noteapp'; // tên databse
	if($_SESSION['username']!='minhtam'){
		
		echo "<script>alert('You do not have permission to access this page!'); window.location.href='/index.php';</script>";
		die();
	}
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8");
    if ($conn->connect_error) {
        die('Không thể kết nối database: ' . $conn->connect_error);
    }

	echo "Kết nối thành công tới database<br><br>";

	$sql = "SELECT * from user";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) 
		{
			echo json_encode($row);
			echo "<br>";
		}
	}
	else {
		echo "Bảng chưa có dữ liệu";
	}

	// Sử dụng link tuyệt đối tính từ root, vì vậy có dấu / đầu tiên
	echo "<br><img src='/images/tdt-logo.png' />";
	echo "<p>Đây là ảnh mẫu, lấy từ thư mục images tại web root.</p>";
?>
