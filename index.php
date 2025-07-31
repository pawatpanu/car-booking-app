<?php
session_start();
require 'db.php';

// ตรวจสอบว่าเข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ดึงชื่อและ role ของผู้ใช้
$user_id = intval($_SESSION['user_id']);
$stmt = $conn->prepare("SELECT name, role FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

$user_name = $user_data['name'] ?? '';
$user_role = intval($user_data['role'] ?? 0);

// สร้างเมนูตามสิทธิ์ผู้ใช้
$menu = [
    ['href' => 'reserve.php', 'label' => '🚗 จองรถยนต์'],
    ['href' => 'booking_list.php', 'label' => '📋 รายการจองของฉัน'],
];

if ($user_role === 1) { // 1 = admin
    $menu[] = ['href' => 'admin_approval.php', 'label' => '🛡️ อนุมัติการจอง (Admin)'];
    $menu[] = ['href' => 'add_user.php', 'label' => '➕ เพิ่มผู้ใช้ใหม่'];
    $menu[] = ['href' => 'add_vehicle.php', 'label' => '🚙 เพิ่มรถยนต์'];
}

$menu[] = ['href' => 'logout.php', 'label' => '🚪 ออกจากระบบ'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Car Booking App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f8fafc; }
        .card { border-radius: 1rem; }
        .list-group-item { font-size: 1.1rem; }
        .welcome { font-weight: 500; }
    </style>
</head>
<body>
<div class="container" style="max-width:500px; margin-top:60px;">
    <div class="card shadow">
        <div class="card-header bg-success text-white text-center">
            <h3 class="mb-0">🚙 ระบบจองยานพาหนะ</h3>
        </div>
        <div class="card-body text-center">
            <h4 class="welcome mb-4">ยินดีต้อนรับ, <?= htmlspecialchars($user_name) ?></h4>
            <div class="list-group">
                <?php foreach ($menu as $item): ?>
                    <a href="<?= htmlspecialchars($item['href']) ?>" class="list-group-item list-group-item-action">
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
