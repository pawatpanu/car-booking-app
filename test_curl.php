<?php
require_once 'notify_config.php';
require_once 'notify_line.php'; // ไฟล์ที่มีฟังก์ชัน notify_moph

$response = notify_moph("🚗 มีรายการจองรถใหม่จากระบบ Car Booking");
echo "<pre>";
print_r($response);
echo "</pre>";
?>
