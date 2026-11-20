<?php
// notification.php
date_default_timezone_set('Asia/Tokyo');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// ログイン中のユーザーID（セッションから取得）
$user_id = $_SESSION['user_id'] ?? null;
$today_dow = (int)date('w'); // 0:日, 1:月, 2:火, 3:水, 4:木, 5:金, 6:土
$today_schedules = [];

$timing_labels = [
    'morning' => '朝',
    'noon'    => '昼',
    'evening' => '夕方',
    'bedtime' => '就寝前'
];

if ($user_id) {
    $host = 'localhost';
    $dbname = 'medithink_db';
    $username = 'medithink_admin';
    $db_password = 'MediThink123!';

    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $db_password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        // medication_schedules から今日の曜日の予定を取得
        $stmt = $pdo->prepare(
            "SELECT medication_timing, scheduled_time
             FROM medication_schedules
             WHERE user_id = :user_id
               AND day_of_week = :dow
             ORDER BY scheduled_time ASC"
        );
        $stmt->execute([
            ':user_id' => $user_id,
            ':dow'     => $today_dow
        ]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $timing = $row['medication_timing'];
            $today_schedules[] = [
                'timing' => $timing,
                'label'  => $timing_labels[$timing] ?? $timing,
                'time'   => substr($row['scheduled_time'], 0, 5) // "08:00:00" -> "08:00"
            ];
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediThink | お薬通知システム</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .header-notify-bar {
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 16px;
            background-color: #fff3cd;
            color: #664d03;
            border-bottom: 1px solid #ffecb5;
            font-weight: bold;
        }
        .header-notify-bar button {
            background: transparent;
            border: none;
            color: inherit;
            font-size: 18px;
            cursor: pointer;
            line-height: 1;
        }
    </style>
</head>
<body class="login-body">

    <!-- ヘッダー通知エリア -->
    <header>
        <div id="header-medication-notice" class="header-notify-bar" role="alert">
            <span id="header-medication-notice-text"></span>
            <button type="button" id="header-medication-notice-close" aria-label="閉じる">×</button>
        </div>
    </header>

    <main class="login-page" style="justify-content: center; align-items: center;">
        <section class="container flex-fill" style="text-align: center;">
            <h1 class="title-label" style="font-size: 24px; margin-bottom: 20px;">お薬通知システム稼働中</h1>

            <div style="margin-bottom: 20px; text-align: left; display: inline-block;">
                <p><strong>【今日の登録通知時間】</strong></p>
                <?php if (!$user_id): ?>
                    <p style="margin-top: 8px; color: #666;">ログイン情報が確認できません。</p>
                <?php elseif (empty($today_schedules)): ?>
                    <p style="margin-top: 8px; color: #666;">今日の通知時間は登録されていません。</p>
                <?php else: ?>
                    <ul style="list-style-type: none; padding: 0; margin-top: 5px;">
                        <?php foreach ($today_schedules as $item): ?>
                            <li>
                                <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>：
                                <?= htmlspecialchars($item['time'], ENT_QUOTES, 'UTF-8') ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <p style="color: #888; font-size: 14px; margin-bottom: 20px;">
                このページを開いている間、現在時刻（日本時間・HH:MM）が今日の登録時刻と一致するとヘッダーに通知を表示します。
            </p>

            <div>
                <a class="link" href="home.php">ホーム画面に戻る</a>
            </div>
        </section>
    </main>

    <script>
        const todaySchedules = <?= json_encode($today_schedules, JSON_UNESCAPED_UNICODE) ?>;
        const noticeBar = document.getElementById('header-medication-notice');
        const noticeText = document.getElementById('header-medication-notice-text');
        const closeBtn = document.getElementById('header-medication-notice-close');

        let dismissedKey = null;

        function getJstDateAndTime() {
            const now = new Date();
            const jst = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Tokyo' }));
            const yyyy = jst.getFullYear();
            const mm = String(jst.getMonth() + 1).padStart(2, '0');
            const dd = String(jst.getDate()).padStart(2, '0');
            const hh = String(jst.getHours()).padStart(2, '0');
            const min = String(jst.getMinutes()).padStart(2, '0');
            return {
                dateStr: `${yyyy}-${mm}-${dd}`,
                hhmm: `${hh}:${min}`
            };
        }

        function checkNotificationTime() {
            if (!noticeBar || !noticeText || !Array.isArray(todaySchedules)) return;

            const { dateStr, hhmm } = getJstDateAndTime();
            const matched = todaySchedules.find(item => item.time === hhmm);

            if (!matched) return;

            const key = `${dateStr}_${matched.time}`;
            if (dismissedKey === key) return;

            noticeText.textContent = `⏰ 【${matched.label}のお薬時間です！】設定された時間 (${matched.time}) になりました。`;
            noticeBar.style.display = 'flex';
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                const { dateStr, hhmm } = getJstDateAndTime();
                dismissedKey = `${dateStr}_${hhmm}`;
                noticeBar.style.display = 'none';
            });
        }

        checkNotificationTime();
        setInterval(checkNotificationTime, 1000);
    </script>
</body>
</html>