<?php
session_start();
require 'db.php';
require 'notify_line.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = intval($_SESSION['user_id']);
    $vehicle_id = intval($_POST['vehicle_id']);
    $start_date = $conn->real_escape_string($_POST['start_date']);
    $start_time = $conn->real_escape_string($_POST['start_time']);
    $end_date = $conn->real_escape_string($_POST['end_date']);
    $end_time = $conn->real_escape_string($_POST['end_time']);
    $reason = $conn->real_escape_string($_POST['reason']);

    if (strtotime($start_date . ' ' . $start_time) > strtotime($end_date . ' ' . $end_time)) {
        $msg = "<div class='alert alert-warning'>วันเวลาเริ่มต้องไม่มากกว่าสิ้นสุด</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, vehicle_id, start_date, start_time, end_date, end_time, reason, status)
                                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("iisssss", $user_id, $vehicle_id, $start_date, $start_time, $end_date, $end_time, $reason);

        if ($stmt->execute()) {
            // ดึงชื่อผู้ใช้
            $user_result = $conn->query("SELECT name FROM users WHERE id = $user_id LIMIT 1");
            $user_name = $user_result && $user_result->num_rows ? $user_result->fetch_assoc()['name'] : "ไม่ทราบชื่อ";

            // ดึงชื่อรถ
            $v = $conn->query("SELECT model, license_plate FROM vehicles WHERE id=$vehicle_id");
            $vehicle = '';
            if ($v && $row = $v->fetch_assoc()) {
                $vehicle = "รถ " . $row['model'] . " เลขทะเบียน " . $row['license_plate'];
            }

            function format_th_date($d) {
                $months = ['','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
                $ts = strtotime($d);
                return date('d', $ts) . ' ' . $months[intval(date('m', $ts))] . ' ' . (date('Y', $ts) + 543);
            }

            $start_fmt = format_th_date($start_date) . ' ' . substr($start_time, 0, 5);
            $end_fmt = format_th_date($end_date) . ' ' . substr($end_time, 0, 5);

            $notify_text = ":: จองยานพาหนะ\n"
                . "ชื่อผู้จอง: $user_name\n"
                . "รายละเอียดการขอใช้: $reason\n"
                . "วันที่: $start_fmt - $end_fmt\n"
                . "สถานะ: รอตรวจสอบ\n"
                . "URL: http://www.sirattanahospital.go.th/carbooking-master/";

            $notify_result = notify_moph($notify_text);
            $res = json_decode($notify_result, true);
            if (!empty($res['status']) && $res['status'] == 200) {
                $notify_msg = "<div class='alert alert-info'>ส่งแจ้งเตือนผ่าน MOPH Notify สำเร็จ</div>";
            } else {
                $notify_msg = "<div class='alert alert-warning'>แจ้งเตือนไม่สำเร็จ: " . htmlspecialchars($notify_result) . "</div>";
            }

            $msg = "<div class='alert alert-success'>จองรถสำเร็จ รอการอนุมัติ</div>" . $notify_msg;
        } else {
            $msg = "<div class='alert alert-danger'>เกิดข้อผิดพลาดในการจองรถ</div>";
        }
    }
}

$vehicles = $conn->query("SELECT id, model, license_plate FROM vehicles WHERE status='available'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>จองรถยนต์</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">🚗 จองรถยนต์</h4>
        </div>
        <div class="card-body">
            <?= $msg ?>
            <form method="post">
                <div class="form-group">
                    <label>เลือกรถยนต์</label>
                    <select name="vehicle_id" class="form-control" required>
                        <option value="">-- เลือกรถ --</option>
                        <?php if ($vehicles): while($car = $vehicles->fetch_assoc()): ?>
                            <option value="<?= $car['id'] ?>">
                                <?= htmlspecialchars($car['model']) ?> (<?= htmlspecialchars($car['license_plate']) ?>)
                            </option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>วันที่เริ่มต้น</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>เวลาเริ่มต้น</label>
                    <input type="time" name="start_time" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>วันที่สิ้นสุด</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>เวลาสิ้นสุด</label>
                    <input type="time" name="end_time" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>เหตุผลการจอง</label>
                    <textarea name="reason" class="form-control" rows="2" required></textarea>
                </div>
                <button class="btn btn-success btn-block">จองรถ</button>
            </form>
            <a href="index.php" class="btn btn-link mt-3">กลับหน้าหลัก</a>
        </div>
    </div>
</div>
</body>
</html>
