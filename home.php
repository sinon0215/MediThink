<?php
// home.php

// セッションの保存先を指定（login.phpやlogout.phpと合わせる）
$save_path = __DIR__ . '/sessions';
if (is_dir($save_path)) {
    session_save_path($save_path);
}
if(session_status() !== PHP_SESSION_ACTIVE){
    session_start();
}

// ログインしていない場合はログイン画面へリダイレクト
if (!isset($_SESSION['user_id'])) {
    error_log("failed to session.");
    header('Location: login.php');
    exit;
}

// ==========================================
// データベースから次の薬の時間を取得する処理
// ==========================================
$next_time = "未設定"; // デフォルト値

$db_host = 'localhost';
$db_name = 'medithink_db';
$db_user = 'medithink_admin'; // 必要に応じて変更してください
$db_pass = 'MediThink123!';     // 必要に応じてパスワードを設定してください

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ログイン中のユーザーIDをもとに users テーブルから morning_time を取得
    $stmt = $pdo->prepare("SELECT morning_time FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && !empty($user['morning_time'])) {
        $next_time = date('H:i', strtotime($user['morning_time']));
    }
} catch (PDOException $e) {
    // どのようなエラーが起きているかを画面に詳細表示する
    $next_time = "DBエラー: " . $e->getMessage();
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

</body>
</html>