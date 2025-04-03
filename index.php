<?php
require 'config.php';
$sql = "SELECT * FROM tasks ORDER BY due_date ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Công việc</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Danh sách Công việc</h1>
    <table>
        <tr>
            <th>Tiêu đề</th>
            <th>Mô tả</th>
            <th>Hạn chót</th>
            <th>Ưu tiên</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['due_date']) ?></td>
                <td><?= htmlspecialchars($row['priority']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>