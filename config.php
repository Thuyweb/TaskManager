<?php
$servername = "localhost";
$username = "root"; // Username mặc định của MySQL
$password = ""; // Password mặc định của MySQL nếu bạn chưa thay đổi
$dbname = "TaskManagement"; // Tên database bạn đã tạo

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
