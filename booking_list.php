<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = intval($_SESSION['user_id']);

// ดึงรายการจองของผู้ใช้
$sql = "SELECT b.id, v.model AS vehicle, b.start_date, b.end_date, b.reason, b.status
        FROM bookings b
        JOIN vehicles v ON b.vehicle_id = v.id
        WHERE b.user_id = $user_id
        ORDER BY b.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>รายการจองของฉัน | Car Booking App</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f8fafc; }
        .card { border-radius: 1rem; }
        .table th, .table td { vertical-align: middle; }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">📋 รายการจองของฉัน</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>รถยนต์</th>
                        <th>วันที่เริ่ม</th>
                        <th>วันที่สิ้นสุด</th>
                        <th>เหตุผล</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['vehicle']) ?></td>
                            <td><?= htmlspecialchars($row['start_date']) ?></td>
                            <td><?= htmlspecialchars($row['end_date']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['reason'])) ?></td>
                            <td>
                                <?php
                                    if ($row['status'] == 'pending') {
                                        echo '<span class="badge badge-warning">รออนุมัติ</span>';
                                    } elseif ($row['status'] == 'approved') {
                                        echo '<span class="badge badge-success">อนุมัติ</span>';
                                    } else {
                                        echo '<span class="badge badge-danger">ไม่อนุมัติ</span>';
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">ไม่มีรายการจอง</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            <a href="index.php" class="btn btn-secondary mt-2">กลับหน้าหลัก</a>
        </div>
    </div>
</div>