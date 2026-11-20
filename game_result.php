<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ゲーム結果 - MediThink</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/game_result.css">
</head>
<body class="result-body">

    <!-- 既存のヘッダーを読み込み -->
    <?php include 'header.php'; ?>

    <!-- メインエリア -->
    <main class="result-main">
        <div class="result-banner">ゲーム結果</div>

        <div class="result-status-box">結果</div>

        <div class="result-score-box">スコア</div>

        <a href="game_select.php" class="result-back-btn">選択画面に戻る</a>
    </main>

    <!-- 右下の吹き出し＆キャラクター -->
    <div class="result-character-area">
        <div class="result-comment">お疲れ様でした。</div>
        <div class="result-character">
            <img src="image/zilyou_zaiko.png" alt="キャラクター" class="result-character-image">
        </div>
    </div>

</body>
</html>