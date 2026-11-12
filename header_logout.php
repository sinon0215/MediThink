<?php
$header_notice = $header_notice ?? 'お知らせ';

// ユーザーの通知時間をデータベースから安全に取得する処理
$notification_times = ['morning' => '', 'lunch' => '', 'evening' => '', 'bedtime' => ''];

if (isset($_SESSION['user_id'])) {
    try {
        $db_host = 'localhost';
        $db_name = 'medithink_db';
        $db_user = 'medithink_admin'; 
        $db_pass = 'MediThink123!';     
        $pdo_h = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
        $pdo_h->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt_h = $pdo_h->prepare("SELECT morning_time, lunch_time, evening_time, bedtime_time FROM users WHERE id = ?");
        $stmt_h->execute([$_SESSION['user_id']]);
        $user_times = $stmt_h->fetch(PDO::FETCH_ASSOC);
        
        if ($user_times) {
            $notification_times = [
                'morning' => !empty($user_times['morning_time']) ? substr($user_times['morning_time'], 0, 5) : '',
                'lunch'   => !empty($user_times['lunch_time'])   ? substr($user_times['lunch_time'], 0, 5)   : '',
                'evening' => !empty($user_times['evening_time']) ? substr($user_times['evening_time'], 0, 5) : '',
                'bedtime' => !empty($user_times['bedtime_time']) ? substr($user_times['bedtime_time'], 0, 5) : '',
            ];
        }
    } catch (PDOException $e) {
        // エラー時は空のまま
    }
}
?>

<link rel="stylesheet" href="./css/header.css">

<header class="medithink-header">
    <div class="medithink-logo-area">
        <div class="medithink-logo">
            <img src="image/small_logo.png"  alt="ロゴ">
        </div>
        <a class="medithink-logout-btn" href="logout.php">ログアウト</a>
    </div>
    <p class="medithink-notice"><?= htmlspecialchars($header_notice, ENT_QUOTES, 'UTF-8') ?></p>
</header>

<script>
window.medTimesData = {
    morning: "<?= htmlspecialchars($notification_times['morning'] ?? '', ENT_QUOTES, 'UTF-8') ?>",
    lunch:   "<?= htmlspecialchars($notification_times['lunch'] ?? '', ENT_QUOTES, 'UTF-8') ?>" ,
    evening: "<?= htmlspecialchars($notification_times['evening'] ?? '', ENT_QUOTES, 'UTF-8') ?>",
    bedtime: "<?= htmlspecialchars($notification_times['bedtime'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
};
</script>

<!-- 2. 分割した外部JSファイル（notification.js）の読み込み -->
<script src="js/notification.js"></script>