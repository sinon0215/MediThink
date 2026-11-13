
<?php
// save_schedule.php

// セッション設定
$save_path = __DIR__ . '/sessions';
if (is_dir($save_path)) {
    session_save_path($save_path);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// JSON形式で結果を返す
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

// POST以外は受け付けない
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
$db_pass = 'MediThink123!';

// フロントからJSONデータを受け取る
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['schedules']) || !is_array($data['schedules'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => '設定データが正しくありません。'
    ]);
    exit;
}

$allowed_timings = [
    'morning',
    'noon',
    'evening',
    'bedtime'
];

$valid_schedules = [];
$seen = [];

foreach ($data['schedules'] as $schedule) {

    if (
        !isset($schedule['day_of_week']) ||
        !isset($schedule['medication_timing'])||
        !isset($schedule['scheduled_time'])
    ) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => '設定内容が不足しています。'
        ]);
        exit;
    }

    $day = $schedule['day_of_week'];
    $timing = $schedule['medication_timing'];
    $time = $schedule['scheduled_time'];
    
    if (
    !is_int($day) ||
    $day < 0 ||
    $day > 6 ||
    !in_array($timing, $allowed_timings, true) ||
    !is_string($time) ||
    !preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $time)
    ) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => '曜日または時間帯が不正です。'
        ]);
        exit;
    }

    // 重複チェック
    $key = $day . '_' . $timing;

    if (isset($seen[$key])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => '同じ設定が重複しています。'
        ]);
        exit;
    }

    $seen[$key] = true;
    $valid_schedules[] = [
        'day' => $day,
        'timing' => $timing,
        'time' => $time
    ];
}

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

    $user_id = $_SESSION['user_id'];

    // トランザクション開始
    $pdo->beginTransaction();

    // 既存の設定を削除
    $delete = $pdo->prepare(
        "DELETE FROM medication_schedules WHERE user_id = ?"
    );
    $delete->execute([$user_id]);

    // 新しい設定を登録
    $insert = $pdo->prepare(
    "INSERT INTO medication_schedules
    (user_id, day_of_week, medication_timing, scheduled_time)
    VALUES (?, ?, ?, ?)"
    );

    foreach ($valid_schedules as $schedule) {
        $insert->execute([
            $user_id,
            $schedule['day'],
            $schedule['timing'],
            $schedule['time']
        ]);
    }

    // 保存確定
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'カレンダー設定を保存しました。'
    ]);

} catch (PDOException $e) {

    // エラー時は元に戻す
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log($e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '保存に失敗しました。'
    ]);
}
