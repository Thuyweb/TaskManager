<?php
require 'vendor/autoload.php'; // Đảm bảo bạn đã cài đặt PHPMailer qua Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Kết nối cơ sở dữ liệu
require 'config.php'; // Tạo file config.php chứa thông tin kết nối cơ sở dữ liệu

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

// Thêm công việc mới
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $dueDate = $_POST['due_date'] ?? '';
    $priority = $_POST['priority'] ?? 'Medium';

    if (!empty($title) && !empty($dueDate)) {
        // Thêm công việc vào cơ sở dữ liệu
        $stmt = $conn->prepare("INSERT INTO tasks (title, description, due_date, priority, status) VALUES (?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("ssss", $title, $description, $dueDate, $priority);

        if ($stmt->execute()) {
            echo "Công việc mới đã được thêm thành công.\n";

            // Kiểm tra nếu hạn chót nằm trong vòng 3 ngày
            $currentDate = date('Y-m-d');
            $threeDaysLater = date('Y-m-d', strtotime('+3 days'));

            if ($dueDate >= $currentDate && $dueDate <= $threeDaysLater) {
                // Gửi email nhắc nhở
                $emailQuery = "SELECT email FROM registered_emails";
                $emailResult = $conn->query($emailQuery);

                if ($emailResult->num_rows > 0) {
                    while ($emailRow = $emailResult->fetch_assoc()) {
                        $email = $emailRow['email'];

                        $emailBody = "Xin chào,\n\nMột công việc mới đã được thêm vào danh sách của bạn:\n";
                        $emailBody .= "- Tiêu đề: $title\n";
                        $emailBody .= "- Hạn chót: $dueDate\n";
                        $emailBody .= "- Mô tả: $description\n\n";
                        $emailBody .= "Vui lòng kiểm tra hệ thống để biết thêm chi tiết.\n\nTrân trọng,\nTask Manager";

                        $mail = new PHPMailer(true);

                        try {
                            // Cấu hình SMTP với Gmail
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com'; // Máy chủ SMTP của Gmail
                            $mail->SMTPAuth = true;
                            $mail->Username = 'phpkt2muc5@gmail.com'; // Thay bằng email Gmail của bạn
                            $mail->Password = 'dsng cdbe cktc ctux'; // Thay bằng mật khẩu ứng dụng Gmail
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Sử dụng mã hóa TLS
                            $mail->Port = 587; // Cổng SMTP của Gmail

                            // Cấu hình email
                            $mail->CharSet = 'UTF-8'; // Thiết lập mã hóa UTF-8
                            $mail->setFrom('phpkt2muc5@gmail.com', 'Task Manager'); // Email gửi đi
                            $mail->addAddress($email); // Email người nhận
                            $mail->Subject = 'Nhắc nhở: Công việc mới được thêm';
                            $mail->Body = $emailBody;

                            // Gửi email
                            $mail->send();
                            echo "Thông báo đã được gửi đến email: $email\n";
                        } catch (Exception $e) {
                            echo "Đã xảy ra lỗi khi gửi email đến $email: " . $mail->ErrorInfo . "\n";
                        }
                    }
                } else {
                    echo "Không có email nào được đăng ký.\n";
                }
            }
        } else {
            echo "Đã xảy ra lỗi khi thêm công việc: " . $stmt->error . "\n";
        }

        $stmt->close();
    } else {
        echo "Vui lòng nhập đầy đủ thông tin công việc.\n";
    }
}

$conn->close();
?>