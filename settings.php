<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediThink | 設定</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="home-body">

    <!-- ヘッダーエリア (.header) -->
    <header class="header">
        <a href="logout.php" class="home-btn">ホームに戻る</a>

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
        <h2>変更したい設定を選択してください</h2>
    </section>

    <!-- メインコンテンツ (.main-container) -->
    <main class="main-container-section">
        

        <!-- 設定ボタン群 (.setting-btn) -->
        <div class="setting-btn">
            <a href="setting_pass.php" class="large-btn password-btn">パスワード変更</a>
            <div class="setting-btns">
                <a href="setting_medi.php" class="large-btn">薬服用時間</a>
                <a href="setting_time.php" class="large-btn">通知時間</a>
            </div>
        </div>
    </main>
    

</body>
</html>