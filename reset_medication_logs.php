<?php
date_default_timezone_set('Asia/Tokyo');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method Not Allowed']);
    exit;
}

$db_host = 'localhost';
$db_name = 'medithink_db';
$db_user = 'medithink_admin';
$db_pass = 'MediThink123!';

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $user_id = (int)$_SESSION['user_id'];
    $today_str = date('Y-m-d');

    $stmt = $pdo->prepare(
        "DELETE FROM medication_logs WHERE user_id = ? AND taken_date = ?"
    );
    $stmt->execute([$user_id, $today_str]);

    unset($_SESSION['target_medication_timing']);

    echo json_encode(['ok' => true, 'reset_date' => $today_str]);
} catch (PDOException $e) {
    error_log('reset_medication_logs error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'DB error']);
}
