<?php
session_start();
include("connect.php");

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm']);

    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $error = "Email không hợp lệ!";
    } elseif (strlen($password) < 8) {
        $error = "Mật khẩu phải ít nhất 8 ký tự!";
    } elseif ($password !== $confirm) {
        $error = "Mật khẩu nhập lại không khớp!";
    } else {
        // kiểm tra email đã tồn tại chưa
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $error = "Email đã tồn tại!";
        } else {
            $hash = md5($password); // Hoặc password_hash()
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hash);
            if ($stmt->execute()) {
                $success = "Đăng ký thành công! <a href='login.php'>Đăng nhập ngay</a>";
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng ký</title>
<link rel="stylesheet" href="css.css">
<style>
body {font-family: Arial, sans-serif;}
.register-container {
    width: 350px; margin: 100px auto; padding: 25px;
    border: 1px solid #ccc; border-radius: 10px;
    background: #fff; box-shadow: 0 0 12px rgba(0,0,0,0.1);
}
h2 {text-align:center; margin-bottom:20px;}
.form-group {margin-bottom:15px;}
.form-label {display:block; margin-bottom:5px; font-weight:bold;}
.form-input {width:100%; padding:8px; border:1px solid #aaa; border-radius:5px;}
.form-actions {text-align:center;}
.btn-primary {
    padding:10px 20px; border:none; background:#28a745;
    color:#fff; border-radius:5px; cursor:pointer;
}
.btn-primary:hover {background:#218838;}
.error {color:red; text-align:center; margin-bottom:10px;}
.success {color:green; text-align:center; margin-bottom:10px;}
</style>
</head>
<body>
<div class="register-container">
    <h2>Đăng ký tài khoản</h2>
    <?php if($error) echo "<div class='error'>$error</div>"; ?>
    <?php if($success) echo "<div class='success'>$success</div>"; ?>
    <form method="post" autocomplete="off">
        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="username" required class="form-input">
        </div>
        <div class="form-group">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" required minlength="8" class="form-input">
        </div>
        <div class="form-group">
            <label class="form-label">Nhập lại mật khẩu</label>
            <input type="password" name="confirm" required minlength="8" class="form-input">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Đăng ký</button>
        </div>
        <div class="form-actions" style="margin-top:10px;">
            <a href="login.php">Quay lại đăng nhập</a>
        </div>
    </form>
</div>
</body>
</html>
