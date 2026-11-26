<?php
// home.php
// タイムゾーンを日本時間に明示的に設定
date_default_timezone_set('Asia/Tokyo');

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

$next_time = "今日飲む薬はありません";
$has_today_medication = false;
$already_taken_today = false;

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $user_id = $_SESSION['user_id'];
    $today_dow = (int)date('w'); // 0:日, 1:月, 2:火, 3:水, 4:木, 5:金, 6:土
    $now = date('H:i:s');
    $today_str = date('Y-m-d');

    $already_taken_today = (
        isset($_SESSION['taken_medication_date']) &&
        $_SESSION['taken_medication_date'] === $today_str
    );

    if ($already_taken_today) {
        $has_today_medication = false;
        $next_time = "今日飲む薬はありません";
    } else {
        // 今日の曜日のうち、現在時刻以降に飲む予定があるか確認
        $stmtCount = $pdo->prepare(
            "SELECT COUNT(*)
             FROM medication_schedules
             WHERE user_id = ?
               AND day_of_week = ?
               AND scheduled_time IS NOT NULL
               AND scheduled_time <> ''
               AND scheduled_time >= ?"
        );
        $stmtCount->execute([$user_id, $today_dow, $now]);
        $has_today_medication = ((int)$stmtCount->fetchColumn() > 0);

        // 今日の曜日のうち、現在時刻以降の最も近い予定を取得
        $stmt = $pdo->prepare(
            "SELECT scheduled_time
             FROM medication_schedules
             WHERE user_id = ?
               AND day_of_week = ?
               AND scheduled_time >= ?
             ORDER BY scheduled_time ASC
             LIMIT 1"
        );
        $stmt->execute([$user_id, $today_dow, $now]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && !empty($row['scheduled_time'])) {
            $next_time = date('H:i', strtotime($row['scheduled_time']));
        } else {
            $next_time = "今日飲む薬はありません";
        }
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    $next_time = "DBエラー";
    $has_today_medication = false;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediThink | ホーム</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body class="home-body">

    <?php include 'header_logout.php'; ?>

    <!-- メインコンテンツ (.main-container) -->
    <main class="main-container">
        <!-- 左側メインエリア (カレンダー + 時間 + キャラクター) -->
        <div class="main-content-left">
            <!-- カレンダーエリア (.calender) : 空布の枠 -->
            
        <div class="calender">
            <table class="home-calendar">
                <thead>
                    <tr>
                        <th></th>
                        <th>月</th>
                        <th>火</th>
                        <th>水</th>
                        <th>木</th>
                        <th>金</th>
                        <th>土</th>
                        <th>日</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>朝</th>
                        <td data-day="1" data-timing="morning"></td>
                        <td data-day="2" data-timing="morning"></td>
                        <td data-day="3" data-timing="morning"></td>
                        <td data-day="4" data-timing="morning"></td>
                        <td data-day="5" data-timing="morning"></td>
                        <td data-day="6" data-timing="morning"></td>
                        <td data-day="0" data-timing="morning"></td>
                    </tr>
                    <tr>
                        <th>昼</th>
                        <td data-day="1" data-timing="noon"></td>
                        <td data-day="2" data-timing="noon"></td>
                        <td data-day="3" data-timing="noon"></td>
                        <td data-day="4" data-timing="noon"></td>
                        <td data-day="5" data-timing="noon"></td>
                        <td data-day="6" data-timing="noon"></td>
                        <td data-day="0" data-timing="noon"></td>
                    </tr>
                    <tr>
                        <th>夜</th>
                        <td data-day="1" data-timing="evening"></td>
                        <td data-day="2" data-timing="evening"></td>
                        <td data-day="3" data-timing="evening"></td>
                        <td data-day="4" data-timing="evening"></td>
                        <td data-day="5" data-timing="evening"></td>
                        <td data-day="6" data-timing="evening"></td>
                        <td data-day="0" data-timing="evening"></td>
                    </tr>
                    <tr>
                        <th>寝る前</th>
                        <td data-day="1" data-timing="bedtime"></td>
                        <td data-day="2" data-timing="bedtime"></td>
                        <td data-day="3" data-timing="bedtime"></td>
                        <td data-day="4" data-timing="bedtime"></td>
                        <td data-day="5" data-timing="bedtime"></td>
                        <td data-day="6" data-timing="bedtime"></td>
                        <td data-day="0" data-timing="bedtime"></td>
                    </tr>
                </tbody>
            </table>
        </div>


            <!-- 時間表示とキャラクターのエリア -->
            <div class="bottom-time-character-row">
                <!-- 次の薬の時間 (.medi-time-box) -->
                <div class="medi-time-box">
                    <span class="medi-time-label">次の薬の時間は</span>
                    <div class="medi-time-display">
                        <span class="time-digits"><?= htmlspecialchars($next_time, ENT_QUOTES, 'UTF-8') ?></span>
                        <?php if ($next_time !== "今日飲む薬はありません" && $next_time !== "DBエラー"): ?>
                            <span class="medi-time-suffix">です</span>
                        <?php endif; ?>
                    </div>
                </div>


                <!-- キャラクターエリア (.character) -->
                <div class="character">
                    <img src="./image/zilyou_zaiko.png" height="150px">
                </div>
            </div>
        </div>

        <!-- 右側メニューボタン群 (.menu-btn) -->
        <nav class="menu-btn" aria-label="メインメニュー">
            <a href="game_select.php" class="medium-btn">ゲームスタート</a>
            <a href="settings.php" class="medium-btn">設定</a>
            <a href="records.php" class="medium-btn">ゲーム記録</a>
            <a href="home_medi_start.php" id="already-taken-btn" class="medium-btn">もう薬飲んだ</a>
            <p id="no-medi-message" class="no-medi-message">
                今日飲む薬はないよ
            </p>
        </nav>
    </main>
<script src="./js/home.js"></script>
<script>
(() => {
    const btn = document.getElementById('already-taken-btn');
    const msg = document.getElementById('no-medi-message');
    const hasTodayMedication = <?= json_encode((bool)$has_today_medication, JSON_UNESCAPED_UNICODE) ?>;
    const alreadyTakenToday = <?= json_encode((bool)$already_taken_today, JSON_UNESCAPED_UNICODE) ?>;

    if (!btn) return;

    if (!hasTodayMedication) {
        sessionStorage.removeItem('medi_record_mode');
    }

    btn.addEventListener('click', (e) => {
        e.preventDefault();

        if (!hasTodayMedication) {
            sessionStorage.removeItem('medi_record_mode');
            if (msg) {
                msg.textContent = alreadyTakenToday
                    ? '今日はもう服薬記録済みだよ'
                    : '今日飲む薬はないよ';
                msg.style.display = 'block';
            }
            return;
        }

        if (msg) {
            msg.style.display = 'none';
        }

        sessionStorage.setItem('medi_record_mode', '1');
        if (!sessionStorage.getItem('medi_notice_return_url')) {
            sessionStorage.setItem('medi_notice_return_url', window.location.href || 'home.php');
        }
        window.location.href = btn.href || 'medi_game_start.php';
    });
})();
</script>
</body>
</html>