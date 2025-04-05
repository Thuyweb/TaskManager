<?php
require 'vendor/autoload.php'; // Đảm bảo bạn đã cài đặt PHPMailer qua Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Kết nối cơ sở dữ liệu
require 'config.php'; // Kết nối cơ sở dữ liệu bằng PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['notify']) && $_POST['notify'] === 'yes') {
        $email = $_POST['email'] ?? '';
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Lưu email vào cơ sở dữ liệu
            $stmt = $pdo->prepare("INSERT IGNORE INTO registered_emails (email) VALUES (:email)");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            if ($stmt->execute()) {
                echo "Email đã được lưu trữ thành công.\n";
            } else {
                echo "Đã xảy ra lỗi khi lưu email.\n";
            }

            // Gửi email xác nhận
            $emailBody = "Xin chào,\n\nCảm ơn bạn đã đăng ký dịch vụ thông báo công việc sắp đến hạn chót từ Task Manager! 🎉\n\n"
            . "Từ bây giờ, chúng tôi sẽ gửi thông báo đến email của bạn mỗi khi có công việc sắp đến hạn chót. "
            . "Hãy yên tâm rằng bạn sẽ không bỏ lỡ bất kỳ nhiệm vụ quan trọng nào.\n\n"
            . "Nếu bạn có bất kỳ câu hỏi hoặc cần hỗ trợ, vui lòng liên hệ với chúng tôi qua email support@taskmanager.local.\n\n"
            . "Trân trọng,\n"
            . "Đội ngũ Task Manager";
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
                $mail->Subject = 'Task Management Notification'; // Tiêu đề email
                $mail->Body = $emailBody;

                // Gửi email
                $mail->send();

                // Hiển thị thông báo thành công và chuyển hướng bằng JavaScript
                echo "<script>
                    alert('Thông báo đã được gửi thành công!');
                    window.location.href = 'index.php';
                </script>";
                exit(); // Dừng thực thi mã sau khi chuyển hướng
            } catch (Exception $e) {
                echo "Đã xảy ra lỗi khi gửi email: " . $mail->ErrorInfo;
            }
        } else {
            echo "Email không hợp lệ. Vui lòng nhập lại.";
        }
    } else {
        echo "Bạn đã chọn không nhận thông báo.";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo nhắc hẹn - Task Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        h1 {
            color: #333333;
            font-size: 24px;
            margin-bottom: 10px;
        }

        p.description {
            color: #555555;
            font-size: 16px;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        form {
            margin-top: 20px;
        }

        label {
            font-size: 16px;
            color: #333333;
            margin-right: 10px;
        }

        input[type="radio"] {
            margin-right: 5px;
        }

        #emailInput {
            margin-top: 15px;
        }

        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font-size: 14px;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Chào mừng đến với Task Management</h1>
        <p class="description">
            Dịch vụ thông báo nhắc hẹn của chúng tôi giúp bạn không bỏ lỡ bất kỳ công việc quan trọng nào. 
            Hãy đăng ký ngay để nhận thông báo qua email mỗi khi có công việc sắp đến hạn chót!
        </p>
        <form method="POST">
            <p>Bạn muốn nhận thông báo khi sắp đến hạn chót?</p>
            <label>
                <input type="radio" name="notify" value="yes" required> Có
            </label>
            <label>
                <input type="radio" name="notify" value="no" required> Không
            </label>
            <div id="emailInput" style="display: none;">
                <label for="email">Nhập email của bạn:</label>
                <input type="email" name="email" id="email" placeholder="example@domain.com" required>
            </div>
            <button type="submit">Xác nhận</button>
        </form>
    </div>

    <script>
        // Hiển thị ô nhập email nếu chọn "Có"
        document.querySelectorAll('input[name="notify"]').forEach(radio => {
            radio.addEventListener('change', function () {
                const emailInput = document.getElementById('emailInput');
                if (this.value === 'yes') {
                    emailInput.style.display = 'block';
                } else {
                    emailInput.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>