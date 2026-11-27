cat << 'EOF' > /var/www/html/MediThink/mark_medication_taken.php
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
    $timing = $_SESSION['target_medication_timing'] ?? null;

    if (!$timing) {
        $today_dow = (int)date('w');
        $now = date('H:i:s');
        $stmtFind = $pdo->prepare(
            "SELECT medication_timing
             FROM medication_schedules
             WHERE user_id = ?
               AND day_of_week = ?
               AND scheduled_time >= ?
               AND medication_timing NOT IN (
                   SELECT medication_timing
                   FROM medication_logs
                   WHERE user_id = ? AND taken_date = ?
               )
             ORDER BY scheduled_time ASC
             LIMIT 1"
        );
        $stmtFind->execute([$user_id, $today_dow, $now, $user_id, $today_str]);
        $timing = $stmtFind->fetchColumn();
    }

    if (!$timing) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => '記録対象のスケジュールがありません']);
        exit;
    }

    $stmt = $pdo->prepare(
        "INSERT IGNORE INTO medication_logs (user_id, taken_date, medication_timing)
         VALUES (?, ?, ?)"
    );
    $stmt->execute([$user_id, $today_str, $timing]);

    unset($_SESSION['target_medication_timing']);

    echo json_encode([
        'ok' => true,
        'taken_date' => $today_str,
        'medication_timing' => $timing,
    ]);
} catch (PDOException $e) {
    error_log('mark_medication_taken error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'DB error']);
}
