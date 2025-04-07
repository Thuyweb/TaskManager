<?php
require 'config.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra nếu có id trong URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Câu lệnh SQL để xóa công việc
        $sql_delete = "DELETE FROM tasks WHERE id = :id";
        $stmt = $pdo->prepare($sql_delete);
        
        // Gán giá trị cho tham số :id
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        // Thực thi câu lệnh SQL
        if ($stmt->execute()) {
            echo "<p style='color: green;'>Công việc đã được xóa thành công!</p>";
            header("Location: index.php"); // Chuyển hướng về trang chính sau khi xóa thành công
            exit;
        } else {
            echo "<p style='color: red;'>Lỗi khi xóa công việc.</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Lỗi: " . $e->getMessage() . "</p>"; // Xử lý lỗi PDO
    }
} else {
    echo "<p style='color: red;'>Không có ID công việc để xóa!</p>";
}
?>


