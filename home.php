<?php
// home.php

// セッションの保存先を指定（login.phpやlogout.phpと合わせる）
$save_path = __DIR__ . '/sessions';
if (is_dir($save_path)) {
    session_save_path($save_path);
}
session_start();

// ログインしていない場合はログイン画面へリダイレクト
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// --- データベース設定 ---
$db_host = '127.0.0.1';
$db_name = 'medithink_db';
$db_user = 'medithink_admin';
$db_pass = 'password123';

$user_times = [
    'morning_time' => '',
    'lunch_time' => '',
    'evening_time' => '',
    'bedtime_time' => ''
];

try {
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // 4つの時間をデータベースから取得
    $stmt = $pdo->prepare('SELECT morning_time, lunch_time, evening_time, bedtime_time FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    if ($result) {
        $user_times = $result;
    }
} catch (PDOException $e) {
    // エラー時の処理
}

// 画面に表示する「次の時間」を決定するロジック
// デフォルト値
$next_time = "-- : --";
$now_time_str = date('H:i:s');

// 登録されている時間を配列にする（時分秒）
$schedule = [
    '朝' => $user_times['morning_time'] ?? '',
    '昼' => $user_times['lunch_time'] ?? '',
    '夜' => $user_times['evening_time'] ?? '',
    '寝る前' => $user_times['bedtime_time'] ?? ''
];

// 現在時刻より後の最初の時間を「次の時間」として探す
foreach ($schedule as $label => $time) {
    if (!empty($time) && $time > $now_time_str) {
        $next_time = date('H : i', strtotime($time));
        break;
    }
}
// もし今日の分がすべて終わっていれば、一番最初の「朝」の時間を表示する
if ($next_time === "-- : --" && !empty($user_times['morning_time'])) {
    $next_time = date('H : i', strtotime($user_times['morning_time']));
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediThink | ホーム</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="home-body">

    <!-- ヘッダーエリア (.header) -->
    <header class="header">
        <a href="logout.php" class="logout-btn">ログアウト</a>

        <div class="head_logo" aria-label="MediThink">
            <div class="logo-pill" aria-hidden="true">
                <span class="logo-pill-green"></span>
                <span class="logo-pill-white"></span>
            </div>
            <span class="logo-text">MediThink</span>
        </div>

        <div class="header-right-space"></div>
    </header>

    <!-- お知らせ領域 (.head_text) -->
    <section class="head_text">
        <h2>お知らせ</h2>
    </section>

    <!-- メインコンテンツ (.main-container) -->
    <main class="main-container">
        <!-- 左側メインエリア (カレンダー + 時間 + キャラクター) -->
        <div class="main-content-left">
            <!-- カレンダーエリア (.calender) : 空白の枠 -->
            <div class="calender"></div>

            <!-- 時間表示とキャラクターのエリア -->
            <div class="bottom-time-character-row">
                <!-- 次の薬の時間 (.medi-time-box) -->
                <div class="medi-time-box">
                    <span class="medi-time-label">次の薬の時間は</span>
                    <div class="medi-time-display">
                        <span class="time-digits"><?= htmlspecialchars($next_time, ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="medi-time-suffix">です</span>
                    </div>
                </div>

                <!-- キャラクターエリア (.character) -->
                <div class="character" aria-label="キャラクター">
                    <div class="pill-character">
                        <div class="pill-head">
                            <span class="pill-green"></span>
                            <span class="pill-white"></span>
                        </div>
                        <div class="pill-glasses"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 右側メニューボタン群 (.menu-btn) -->
        <nav class="menu-btn" aria-label="メインメニュー">
            <a href="game.php" class="medium-btn">ゲームスタート</a>
            <a href="settings.php" class="medium-btn">設定</a>
            <a href="ranking.php" class="medium-btn">ランキング</a>
            <button type="button" class="medium-btn">もう薬飲んだ</button>
        </nav>
    </main>

    <!-- JavaScriptによる時間監視・通知機能 -->
    <script>
        // PHPから4つの時間をJavaScriptに安全に渡す
        const targetTimes = {
            morning: "<?= $user_times['morning_time'] ?? '' ?>",
            lunch:   "<?= $user_times['lunch_time'] ?? '' ?>",
            evening: "<?= $user_times['evening_time'] ?? '' ?>",
            bedtime: "<?= $user_times['bedtime_time'] ?? '' ?>"
        };

        // 同じ時間に何度もアラートが出ないようにするためのフラグ
        let notifiedStatus = {
            morning: false,
            lunch: false,
            evening: false,
            bedtime: false
        };

        function checkNotificationTimes() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const currentHM = `${hours}:${minutes}`;

            // 朝のチェック
            if (targetTimes.morning && targetTimes.morning.slice(0, 5) === currentHM && !notifiedStatus.morning) {
                notifiedStatus.morning = true;
                alert('【朝の通知】時間になりました！');
            }
            // 昼のチェック
            if (targetTimes.lunch && targetTimes.lunch.slice(0, 5) === currentHM && !notifiedStatus.lunch) {
                notifiedStatus.lunch = true;
                alert('【昼の通知】時間になりました！');
            }
            // 夜のチェック
            if (targetTimes.evening && targetTimes.evening.slice(0, 5) === currentHM && !notifiedStatus.evening) {
                notifiedStatus.evening = true;
                alert('【夜の通知】時間になりました！');
            }
            // 寝る前のチェック（先ほど22:00に変更したものもここで連動します）
            if (targetTimes.bedtime && targetTimes.bedtime.slice(0, 5) === currentHM && !notifiedStatus.bedtime) {
                notifiedStatus.bedtime = true;
                alert('【寝る前の通知】時間になりました！');
            }
        }

        // 1秒ごとに時間をチェックする
        setInterval(checkNotificationTimes, 1000);
    </script>
</body>
</html>