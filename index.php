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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

</head>
<body>
    <h1>Danh sách Công việc</h1>
    <!-- filepath: /Applications/XAMPP/xamppfiles/htdocs/php/index.php -->

    <div style="text-align: center; margin-bottom: 10px;">
    <!-- Nút Thêm Công việc -->
        <a href="add_task.php" style="
            background-color: rgb(0, 255, 55);
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        ">+ Thêm Công việc</a>

        <!-- Nút Thông báo với icon chuông -->
        <a href="notifications.php" style="
            background-color:rgb(0, 255, 55);
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin-left: 10px; /* Khoảng cách giữa 2 nút */
        ">
            <i class="fas fa-bell"></i> Thông báo
        </a>
    </div>


    <table id="task-table">
        <thead>
            <tr>
                <th>Tiêu đề</th>
                <th>Mô tả</th>
                <th>Hạn chót</th>
                <th>Ưu tiên</th>
                <th>Hoàn thành</th>
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
                <input type="checkbox" class="task-completed" data-id="<?= $row['id'] ?>" <?= $row['completed'] ? 'checked' : '' ?>>
            </td>
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

    <script>
document.querySelectorAll('.task-completed').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
        const id = this.getAttribute('data-id');
        const completed = this.checked ? 1 : 0;

        fetch('check_box.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id, completed })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Trạng thái hoàn thành đã được cập nhật!');
            } else {
                console.error('Lỗi khi cập nhật trạng thái:', data.error);
            }
        })
        .catch(error => {
            console.error('Lỗi:', error);
        });
    });
});
        </script>
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
