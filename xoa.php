<?php
require 'config.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra nếu có id trong URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Xóa công việc từ cơ sở dữ liệu
    $sql_delete = "DELETE FROM tasks WHERE id = $id";
    
    if ($conn->query($sql_delete) === TRUE) {
        echo "<p style='color: green;'>Công việc đã được xóa thành công!</p>";
        header("Location: index.php"); // Chuyển hướng về trang chính sau khi xóa thành công
        exit;
    } else {
        echo "<p style='color: red;'>Lỗi: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: red;'>Không có ID công việc để xóa!</p>";
}
?>
