<?php
// medi_game_start.php
// タイムゾーンを日本時間に明示的に設定
date_default_timezone_set('Asia/Tokyo');

// $save_path = __DIR__ . '/sessions';
// if (is_dir($save_path)) {
//     session_save_path($save_path);
// }
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db_host = 'localhost';
$db_name = 'medithink_db';
$db_user = 'medithink_admin';
$db_pass = 'MediThink123!';

$next_time = "未設定";

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT morning_time, lunch_time, evening_time, bedtime_time FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $morning = $user['morning_time'] ?? null;
        $lunch   = $user['lunch_time'] ?? null;
        $evening = $user['evening_time'] ?? null;
        $bedtime = $user['bedtime_time'] ?? null;

        // 日本時間の現在時刻
        $now = date('H:i:s');

        $slots = [$morning, $lunch, $evening, $bedtime];
        $next_time_raw = null;

        // 現在時刻より後の時間を探す
        foreach ($slots as $slot) {
            if (!empty($slot) && $now <= $slot) {
                $next_time_raw = $slot;
                break;
            }
        }

        // 今日もう残りの時間がない場合は最初の時間（翌朝など）
        if (!$next_time_raw) {
            foreach ($slots as $slot) {
                if (!empty($slot)) {
                    $next_time_raw = $slot;
                    break;
                }
            }
        }

        if ($next_time_raw) {
            $next_time = date('H:i', strtotime($next_time_raw));
        } else {
            $next_time = "未設定";
        }
    }
} catch (PDOException $e) {
    $next_time = "DBエラー";
}

$games = ['game_suuzisagashi.php', 'pazul.php'];
$random_game = $games[random_int(0, count($games) - 1)];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediThink | ゲームスタート</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/game_start.css">
</head>
<body class="home-body">

    <?php include 'header_nobutton.php'; ?>

    <!-- メインコンテンツ (.main-container) -->
    <main class="main-container">
        <a href="<?= htmlspecialchars($random_game, ENT_QUOTES, 'UTF-8') ?>" class="medium-btn start-button">スタート</a>
    </main>

</body>
</html>