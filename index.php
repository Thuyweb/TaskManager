<?php
require 'config.php';
$sql = "SELECT * FROM tasks ORDER BY due_date ASC";
$sql = "SELECT * FROM tasks ORDER BY position ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Công việc</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

</head>
<body>
    <h1>Danh sách Công việc</h1>
    <!-- filepath: /Applications/XAMPP/xamppfiles/htdocs/php/index.php -->
    <table id="task-table">
        <thead>
            <tr>
                <th>Tiêu đề</th>
                <th>Mô tả</th>
                <th>Hạn chót</th>
                <th>Ưu tiên</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody id="task-list">
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr data-id="<?= $row['id'] ?>">
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td><?= htmlspecialchars($row['due_date']) ?></td>
                    <td><?= htmlspecialchars($row['priority']) ?></td>
                    <td>
                        <a href="sua.php?id=<?= $row['id'] ?>">Sửa</a> | 
                        <a href="xoa.php?id=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa công việc này không?')">Xóa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <!-- java script để xử lý kéo và thả -->
    <script>
        // Khởi tạo SortableJS cho bảng công việc
        const taskList = document.getElementById('task-list');
        
        new Sortable(taskList, {
            animation: 150,
            onEnd(evt) {
                // Lấy thứ tự mới sau khi kéo thả
                const order = [];
                const rows = taskList.querySelectorAll('tr');
                
                rows.forEach((row, index) => {
                    order.push({
                        id: row.getAttribute('data-id'),
                        position: index + 1
                    });
                });
                
                // Gửi yêu cầu AJAX để cập nhật thứ tự trong cơ sở dữ liệu
                fetch('update_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ order })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Thứ tự đã được cập nhật thành công!');
                    } else {
                        console.log('Có lỗi khi cập nhật thứ tự!');
                    }
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                });
            }
        });
    </script>
    <form action="" method="POST">
        <label for="title">Tiêu đề:</label>
        <input type="text" id="title" name="title" required>
        <br>
        <label for="description">Mô tả:</label>
        <textarea id="description" name="description" required></textarea>
        <br>
        <label for="due_date">Hạn chót:</label>
        <input type="date" id="due_date" name="due_date" required>
        <br>
        <label for="priority">Ưu tiên:</label>
        <select id="priority" name="priority" required>
            <option value="High">Cao</option>
            <option value="Medium">Trung bình</option>
            <option value="Low">Thấp</option>
        </select>
        <br>
        <button type="submit" name="add_task">Thêm Công việc</button>
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
        $title = $conn->real_escape_string($_POST['title']);
        $description = $conn->real_escape_string($_POST['description']);
        $due_date = $conn->real_escape_string($_POST['due_date']);
        $priority = $conn->real_escape_string($_POST['priority']);
    
        $sql_insert = "INSERT INTO tasks (title, description, due_date, priority) 
                       VALUES ('$title', '$description', '$due_date', '$priority')";
    
        if ($conn->query($sql_insert) === TRUE) {
            echo "<p style='color: green;'>Công việc đã được thêm thành công!</p>";
        } else {
            echo "<p style='color: red;'>Lỗi: " . $conn->error . "</p>";
        }
    }
    ?>
    <script src="drag_and_drop.js"></script>
</body>
</html>
<style>
    
/* Định dạng chung cho form */
form {
    background-color: #f9f9f9;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    max-width: 500px;
    margin: 20px auto;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    font-family: Arial, sans-serif;
}

/* Định dạng cho các nhãn (label) */
form label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    color: #333;
}

/* Định dạng cho các ô nhập liệu (input, textarea, select) */
form input[type="text"],
form input[type="date"],
form textarea,
form select {
    width: 100%; /* Đảm bảo các ô nhập liệu có cùng chiều rộng */
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box; /* Đảm bảo padding không làm thay đổi kích thước */
}

/* Định dạng cho textarea để đồng bộ với các ô input */
form textarea {
    resize: vertical; /* Cho phép thay đổi chiều cao nhưng không thay đổi chiều rộng */
    min-height: 100px; /* Đặt chiều cao tối thiểu */
}

/* Định dạng cho nút bấm */
form button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    transition: background-color 0.3s ease;
    width: 100%; /* Đặt nút bấm có cùng chiều rộng với các ô nhập liệu */
}

form button:hover {
    background-color: #45a049;
}
</style>