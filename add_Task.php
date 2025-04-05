<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $due_date = $conn->real_escape_string($_POST['due_date']);
    $priority = $conn->real_escape_string($_POST['priority']);

    $sql = "INSERT INTO tasks (title, description, due_date, priority) 
            VALUES ('$title', '$description', '$due_date', '$priority')";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit;
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Công việc</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Thêm Công việc mới</h1>
    <form action="" method="POST">
        <label for="title">Tiêu đề:</label>
        <input type="text" id="title" name="title" required><br>

        <label for="description">Mô tả:</label>
        <textarea id="description" name="description" required></textarea><br>

        <label for="due_date">Hạn chót:</label>
        <input type="date" id="due_date" name="due_date" required><br>

        <label for="priority">Ưu tiên:</label>
        <select id="priority" name="priority" required>
            <option value="High">Cao</option>
            <option value="Medium">Trung bình</option>
            <option value="Low">Thấp</option>
        </select><br>

        <button type="submit">Lưu Công việc</button>
    </form>
</body>
</html>
