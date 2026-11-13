
<?php
// get_schedule.php

// セッション設定
$save_path = __DIR__ . '/sessions';
if (is_dir($save_path)) {
    session_save_path($save_path);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

// ログイン確認
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'ログインしてください。'
    ]);
    exit;
}

// GETメソッドのみ許可
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => '不正なリクエストです。'
    ]);
    exit;
}

// DB接続情報
$db_host = 'localhost';
$db_name = 'medithink_db';
$db_user = 'medithink_admin';
$db_pass = 'MediThink123!'; // 必要に応じてパスワードを設定してください

try {
    // DB接続
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // ログイン中のユーザーの設定を取得
    $stmt = $pdo->prepare(
        "SELECT day_of_week, medication_timing, scheduled_time
         FROM medication_schedules
         WHERE user_id = ?
         ORDER BY day_of_week, medication_timing"
    );

    $stmt->execute([$_SESSION['user_id']]);

    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 結果を返す
    echo json_encode([
        'success' => true,
        'schedules' => $schedules
    ]);

} catch (PDOException $e) {

    error_log($e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '設定の取得に失敗しました。'
    ]);
}
