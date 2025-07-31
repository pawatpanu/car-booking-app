<?php
session_start();
require 'db.php';

// ตรวจสอบสิทธิ์ (อนุญาตเฉพาะ admin user_id = 1)
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: login.php");
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $conn->real_escape_string(trim($_POST['password']));

    if ($name && $email && $password) {
        // ตรวจสอบอีเมลซ้ำ
        $chk = $conn->query("SELECT id FROM users WHERE email='$email' LIMIT 1");
        if ($chk && $chk->num_rows > 0) {
            $msg = "<div class='alert alert-danger'>อีเมลนี้ถูกใช้แล้ว</div>";
        } else {
            $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
            if ($conn->query($sql)) {
                $msg = "<div class='alert alert-success'>เพิ่มผู้ใช้สำเร็จ</div>";
            } else {
                $msg = "<div class='alert alert-danger'>เกิดข้อผิดพลาด: {$conn->error}</div>";
            }
        }
    } else {
        $msg = "<div class='alert alert-warning'>กรุณากรอกข้อมูลให้ครบถ้วน</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>เพิ่มผู้ใช้ใหม่ | Car Booking App</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f8fafc; }
        .card { border-radius: 1rem; }
    </style>
</head>
<body class="bg-light">
<div class="container" style="max-width:400px; margin-top:60px;">
    <div class="card shadow">
        <div class="card-header bg-info text-white text-center">
            <h4 class="mb-0">เพิ่มผู้ใช้ใหม่</h4>
        </div>
        <div class="card-body">
            <?= $msg ?>
            <form method="post" autocomplete="off">
                <div class="form-group">
                    <label>ชื่อผู้ใช้</label>
                    <input name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>อีเมล</label>
                    <input name="email" type="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>รหัสผ่าน</label>
                    <input name="password" type="text" class="form-control" required>
                </div>
                <button class="btn btn-info btn-block">เพิ่มผู้ใช้</button>
            </form>
            <a href="index.php" class="btn btn-link mt-3">กลับหน้าหลัก</a>
        </div>
    </div>
</div>
</body>
</html>