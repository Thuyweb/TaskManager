<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Kết nối cơ sở dữ liệu
require 'config.php'; // Đảm bảo bạn đã kết nối cơ sở dữ liệu

// Lấy danh sách công việc sắp đến hạn chót
$query = "SELECT title, description, due_date FROM tasks WHERE due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $title = $row['title'];
        $description = $row['description'];
        $dueDate = $row['due_date']; // Gán giá trị cho $dueDate

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
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'phpkt2muc5@gmail.com';
                        $mail->Password = 'dsng cdbe cktc ctux';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        // Cấu hình email
                        $mail->CharSet = 'UTF-8';
                        $mail->setFrom('phpkt2muc5@gmail.com', 'Task Manager');
                        $mail->addAddress($email);
                        $mail->Subject = 'Nhắc nhở: Công việc mới được thêm';
                        $mail->Body = $emailBody;

                        // Gửi email
                        if ($mail->send()) {
                            echo "Email đã được gửi đến: $email\n";
                        } else {
                            echo "Không thể gửi email đến $email: " . $mail->ErrorInfo . "\n";
                        }
                    } catch (Exception $e) {
                        echo "Đã xảy ra lỗi khi gửi email đến $email: " . $mail->ErrorInfo . "\n";
                    }
                }
            } else {
                echo "Không có email nào được đăng ký.\n";
            }
        }
    }
} else {
    echo "Không có công việc nào sắp đến hạn chót.\n";
}
?>