<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Kết nối cơ sở dữ liệu
require 'config.php';

try {
    // Lấy danh sách công việc sắp đến hạn chót
    $query = "SELECT title, description, due_date FROM tasks WHERE due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
    $stmt = $pdo->query($query);
    $tasks = $stmt->fetchAll();

    if (count($tasks) > 0) {
        // Tạo danh sách công việc
        $taskList = "Danh sách công việc sắp đến hạn chót:\n\n";
        foreach ($tasks as $row) {
            $title = $row['title'];
            $description = $row['description'];
            $dueDate = $row['due_date'];

            $taskList .= "- Tiêu đề: $title\n";
            $taskList .= "  Hạn chót: $dueDate\n";
            $taskList .= "  Mô tả: $description\n\n";
        }

        // Lấy danh sách email đã đăng ký
        $emailQuery = "SELECT email FROM registered_emails";
        $emailStmt = $pdo->query($emailQuery);
        $emails = $emailStmt->fetchAll();

        if (count($emails) > 0) {
            foreach ($emails as $emailRow) {
                $email = $emailRow['email'];

                $emailBody = "Xin chào,\n\n$taskList";
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
                    $mail->Subject = 'Nhắc nhở: Danh sách công việc sắp đến hạn chót';
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
    } else {
        echo "Không có công việc nào sắp đến hạn chót.\n";
    }
} catch (Exception $e) {
    echo "Đã xảy ra lỗi: " . $e->getMessage();
}
?>