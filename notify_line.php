<?php
function notify_line($msg) {
  $token = 'YOUR_LINE_NOTIFY_TOKEN';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'https://notify-api.line.me/api/notify');
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['message' => $msg]));
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
  curl_exec($ch);
  curl_close($ch);
}
?>