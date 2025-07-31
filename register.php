<?php
require 'db.php';

$name = $email = $password = $msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    // ตรวจสอบว่ามี email ซ้ำหรือไม่
    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $msg = "<div class='alert alert-warning'>อีเมลนี้ถูกใช้งานแล้ว</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 0)");
        $stmt->bind_param("sss", $name, $email, $password);
        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success'>สมัครสมาชิกสำเร็จ กรุณาเข้าสู่ระบบ</div>";
        } else {
            $msg = "<div class='alert alert-danger'>เกิดข้อผิดพลาดในการสมัคร</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สมัครสมาชิก</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">📋 สมัครสมาชิก</h4>
        </div>
        <div class="card-body">
            <?= $msg ?>
            <form method="POST">
                <div class="form-group">
                    <label>ชื่อ-นามสกุล</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>อีเมล</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>รหัสผ่าน</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button class="btn btn-primary btn-block">สมัครสมาชิก</button>
            </form>
            <a href="login.php" class="btn btn-link mt-3">เข้าสู่ระบบ</a>
        </div>
    </div>
</div>
</body>
</html>
