<?php
// home.php
// タイムゾーンを日本時間に明示的に設定
date_default_timezone_set('Asia/Tokyo');

// $save_path = __DIR__ . '/sessions';
// if (is_dir($save_path)) {
//     session_save_path($save_path);
// }
if(session_status() !== PHP_SESSION_ACTIVE){
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
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediThink | ゲーム選択</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body class="home-body">

    <?php include 'header_home.php'; ?>

    <!-- メインコンテンツ (.main-container) -->
    <main class="main-container">

            <nav class="menu-btn" aria-label="メインメニュー">
                <a href="pazul.php" class="medium-btn">パズル</a>
                <a href="game_suuzisagashi.php" class="medium-btn">数字探し</a>
            </nav>

        <!-- キャラクターとコメント -->
        <div class="bottom-time-character-row">
            <!-- コメント欄 -->
            <div class="character-comment">
                <span class="character-comment">ゲームを選択してください</span>
            </div>

            <!-- キャラクターエリア (.character) -->
                <img src="./image/zilyou_zaiko.png" height="150px">
            </div>
        </div>

        </nav>
    </main>

</body>
</html>