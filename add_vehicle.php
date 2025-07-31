<?php
session_start();
require 'db.php';

// ตรวจสอบสิทธิ์ (อนุญาตเฉพาะ admin user_id = 1 หรือ role = 'admin')
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: login.php");
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $model = $conn->real_escape_string(trim($_POST['model']));
    $status = $conn->real_escape_string(trim($_POST['status']));

    if ($model && $status) {
        $sql = "INSERT INTO vehicles (model, status) VALUES ('$model', '$status')";
        if ($conn->query($sql)) {
            $msg = "<div class='alert alert-success'>เพิ่มรถยนต์สำเร็จ</div>";
        } else {
            $msg = "<div class='alert alert-danger'>เกิดข้อผิดพลาด: {$conn->error}</div>";
        }
    } else {
        $msg = "<div class='alert alert-warning'>กรุณากรอกข้อมูลให้ครบถ้วน</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>เพิ่มรถยนต์ | Car Booking App</title>
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
            <h4 class="mb-0">เพิ่มรถยนต์</h4>
        </div>
        <div class="card-body">
            <?= $msg ?>
            <form method="post" autocomplete="off">
                <div class="form-group">
                    <label>ชื่อ/รุ่นรถยนต์</label>
                    <input name="model" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>สถานะ</label>
                    <select name="status" class="form-control" required>
                        <option value="available">พร้อมใช้งาน</option>
                        <option value="unavailable">ไม่พร้อมใช้งาน</option>
                    </select>
                </div>
                <button class="btn btn-info btn-block">เพิ่มรถยนต์</button>
            </form>
            <a href="index.php" class="btn btn-link mt-3">กลับหน้าหลัก</a>
        </div>
    </div>
</div>
</body>
</html>