
<?php
// get_schedule.php

date_default_timezone_set('Asia/Aichi');


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

    // SQLを実行して、ログイン中のユーザーIDを渡す
    $stmt->execute([
        (int)$_SESSION['user_id']
    ]);

    // 取得した予定を配列にする
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 今週の月曜日の日付を取得
        $today = new DateTime('today');
        $weekStart = clone $today;

        // 月曜日を週の始まりにする
        $dayOfWeek = (int)$today->format('w');
        $daysFromMonday = ($dayOfWeek + 6) % 7;

        $weekStart->modify("-{$daysFromMonday} days");

        // 今週の月曜日～日曜日の日付
        $startDate = $weekStart->format('Y-m-d');

        $weekEnd = clone $weekStart;
        $weekEnd->modify('+6 days');

        $endDate = $weekEnd->format('Y-m-d');

        // 今週の服薬記録を取得
        $stmtLog = $pdo->prepare(
            "SELECT medication_timing, taken_date
            FROM medication_logs
            WHERE user_id = ?
            AND taken_date BETWEEN ? AND ?"
        );

        $stmtLog->execute([
            (int)$_SESSION['user_id'],
            $startDate,
            $endDate
        ]);

        $logs = $stmtLog->fetchAll(PDO::FETCH_ASSOC);

        // 服薬記録を「日付＋時間帯」で検索できるようにする
        $takenList = [];

        foreach ($logs as $log) {
            $key = $log['taken_date'] . '_' . $log['medication_timing'];
            $takenList[$key] = true;
        }

        // 各予定に服薬済み判定を追加
        foreach ($schedules as &$schedule) {

            // DBの曜日：0=日曜日、1=月曜日...
            // カレンダーは月曜日始まりなので日付を調整
            $scheduleDay = (int)$schedule['day_of_week'];

            $daysFromMonday = ($scheduleDay + 6) % 7;

            $scheduledDate = clone $weekStart;
            $scheduledDate->modify("+{$daysFromMonday} days");

            $date = $scheduledDate->format('Y-m-d');

            // 日付と時間帯が一致する記録があるか確認
            $key = $date . '_' . $schedule['medication_timing'];

            $schedule['taken'] = isset($takenList[$key]);
        }

        unset($schedule);

        // 結果を返す
        echo json_encode([
            'success' => true,
            'schedules' => $schedules
        ]);

    // $stmt->execute([$_SESSION['user_id']]);

    // $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // // 結果を返す
    // echo json_encode([
    //     'success' => true,
    //     'schedules' => $schedules
    // ]);

} catch (PDOException $e) {

    error_log($e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '設定の取得に失敗しました。'
    ]);
}
