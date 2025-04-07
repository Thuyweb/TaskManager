<?php
require 'config.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra nếu có id trong URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Lấy thông tin công việc từ cơ sở dữ liệu
    $sql = "SELECT * FROM tasks WHERE id = $id";
    $result = $conn->query($sql);
    $task = $result->fetch_assoc();
    
    // Nếu không tìm thấy công việc
    if (!$task) {
        echo "Công việc không tồn tại!";
        exit;
    }
}

// Xử lý khi người dùng gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $due_date = $conn->real_escape_string($_POST['due_date']);
    $priority = $conn->real_escape_string($_POST['priority']);
    
    // Cập nhật dữ liệu công việc vào cơ sở dữ liệu
    $sql_update = "UPDATE tasks SET title = '$title', description = '$description', due_date = '$due_date', priority = '$priority' WHERE id = $id";
    
    if ($conn->query($sql_update) === TRUE) {
        echo "<p style='color: green;'>Công việc đã được cập nhật thành công!</p>";
        header("Location: index.php"); // Chuyển hướng về trang chính
        exit;
    } else {
        echo "<p style='color: red;'>Lỗi: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Công việc</title>
    <link rel="stylesheet" href="sua_style.css">
</head>
<body>
    <h1>Sửa Công việc</h1>
    
    <form action="" method="POST">
        <label for="title">Tiêu đề:</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>
        <br>
        
        <label for="description">Mô tả:</label>
        <textarea id="description" name="description" required><?= htmlspecialchars($task['description']) ?></textarea>
        <br>
        
        <label for="due_date">Hạn chót:</label>
        <input type="date" id="due_date" name="due_date" value="<?= htmlspecialchars($task['due_date']) ?>" required>
        <br>
        
        <label for="priority">Ưu tiên:</label>
        <select id="priority" name="priority" required>
            <option value="High" <?= $task['priority'] == 'High' ? 'selected' : '' ?>>Cao</option>
            <option value="Medium" <?= $task['priority'] == 'Medium' ? 'selected' : '' ?>>Trung bình</option>
            <option value="Low" <?= $task['priority'] == 'Low' ? 'selected' : '' ?>>Thấp</option>
        </select>
        <br>
        
        <button type="submit" name="update_task">Cập nhật Công việc</button>
    </form>
</body>
</html>
