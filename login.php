<?php
session_start();
require 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $conn->real_escape_string(trim($_POST['user']));
    $pass = trim($_POST['pass']);

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE name = ? OR email = ? LIMIT 1");
    $stmt->bind_param("ss", $user, $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        if (password_verify($pass, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            header("Location: index.php");
            exit;
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $error = "ไม่พบผู้ใช้งานนี้";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>เข้าสู่ระบบ | Car Booking App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #e9f5ff; }
        .card { border-radius: 1rem; }
        .login-title { font-weight: 600; letter-spacing: 1px; }
        .login-icon { font-size: 2.5rem; }
    </style>
</head>
<body class="bg-light">
<div class="container" style="max-width:400px; margin-top:80px;">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <div class="login-icon mb-2">🔑</div>
            <h4 class="mb-0 login-title">เข้าสู่ระบบ</h4>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" autocomplete="off">
                <div class="form-group">
                    <label>ชื่อผู้ใช้หรืออีเมล</label>
                    <input name="user" class="form-control" required autofocus>
                </div>
                <div class="form-group">
                    <label>รหัสผ่าน</label>
                    <input name="pass" type="password" class="form-control" required>
                </div>
                <button class="btn btn-primary btn-block">เข้าสู่ระบบ</button>
            </form>
            <a href="register.php" class="btn btn-link mt-3">ยังไม่มีบัญชี? สมัครสมาชิก</a>
        </div>
    </div>
</div
