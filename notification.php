<?php
$save_path = __DIR__ . '/sessions';
if (!is_dir($save_path)) {
    mkdir($save_path, 0733, true);
}
session_save_path($save_path);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// ログイン中のユーザーID（セッションから取得）
$user_id = $_SESSION['user_id'] ?? null;

// 時間帯ごとのデフォルト値
$times = [
    '朝' => '',
    '昼' => '',
    '夕方' => '',
    '就寝前' => ''
];

if ($user_id) {
    $host = 'localhost';
    $dbname = 'medithink_db';
    $username = 'medithink_admin';
    $db_password = 'MediThink123!';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // データベースから朝・昼・夕方・就寝前の時間をすべて取得
        $stmt = $pdo->prepare("SELECT morning_time, lunch_time, evening_time, bedtime_time FROM users WHERE id = :id");
        $stmt->execute([':id' => $user_id]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_data) {
            $times['朝']   = $user_data['morning_time'] ?? '';
            $times['昼']   = $user_data['lunch_time'] ?? '';
            $times['夕方'] = $user_data['evening_time'] ?? '';
            $times['就寝前'] = $user_data['bedtime_time'] ?? '';
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
</head>
<body class="login-body">
    <main class="login-page" style="justify-content: center; align-items: center;">
        <section class="container flex-fill" style="text-align: center;">
            <h1 class="title-label" style="font-size: 24px; margin-bottom: 20px;">お薬通知システム稼働中</h1>
            
            <div style="margin-bottom: 20px; text-align: left; display: inline-block;">
                <p><strong>【登録されている通知時間】</strong></p>
                <ul style="list-style-type: none; padding: 0; margin-top: 5px;">
                    <li>朝：<?= htmlspecialchars($times['朝'] !== '' ? $times['朝'] : '未設定', ENT_QUOTES, 'UTF-8') ?></li>
                    <li>昼：<?= htmlspecialchars($times['昼'] !== '' ? $times['昼'] : '未設定', ENT_QUOTES, 'UTF-8') ?></li>
                    <li>夕方：<?= htmlspecialchars($times['夕方'] !== '' ? $times['夕方'] : '未設定', ENT_QUOTES, 'UTF-8') ?></li>
                    <li>就寝前：<?= htmlspecialchars($times['就寝前'] !== '' ? $times['就寝前'] : '未設定', ENT_QUOTES, 'UTF-8') ?></li>
                </ul>
            </div>

            <p style="color: #888; font-size: 14px; margin-bottom: 20px;">
                このページを開いている間、1秒ごとに現在時刻をチェックし、いずれかの設定時刻と一致するとアラートでお知らせします。
            </p>

            <div>
                <a class="link" href="home.php">ホーム画面に戻る</a>
            </div>
        </section>
    </main>

    <!-- ▼ 1秒ごとに現在時刻とDBの各時間を比較するJavaScriptスクリプト ▼ -->
    <script>
        // PHPから受け取った各時間帯のデータをJavaScriptのオブジェクトに格納
        const notificationTimes = {
            朝: "<?= htmlspecialchars($times['朝'], ENT_QUOTES, 'UTF-8') ?>",
            昼: "<?= htmlspecialchars($times['昼'], ENT_QUOTES, 'UTF-8') ?>",
            夕方: "<?= htmlspecialchars($times['夕方'], ENT_QUOTES, 'UTF-8') ?>",
            就寝前: "<?= htmlspecialchars($times['就寝前'], ENT_QUOTES, 'UTF-8') ?>"
        };

        function checkNotificationTime() {
            const now = new Date();
            
            // 現在の時・分・秒を2桁にフォーマット
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const currentTime = `${hours}:${minutes}:${seconds}`;

            // すべての時間帯をループしてチェック
            for (const [label, timeStr] of Object.entries(notificationTimes)) {
                // 値が空でなく、かつ現在時刻と一致した場合
                if (timeStr && currentTime === timeStr) {
                    alert(`⏰ 【${label}のお薬時間です！】設定された時間 (${timeStr}) になりました。`);
                    break; // 同じ秒での重複アラートを防ぐためループを抜ける
                }
            }
        }

        // 1秒ごと（1000ミリ秒）に関数を実行
        setInterval(checkNotificationTime, 1000);
    </script>
</body>
</html>