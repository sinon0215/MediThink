<?php
// ヘッダーのお知らせ表示
$header_notice = 'ゲームスタート画面';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ゲームスタート | MediThink</title>

    <!-- CSS読み込み -->
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/game_start.css">
</head>

<body>

    <!-- 共通ヘッダー -->
    <?php include 'header.php'; ?>

    <main class="main-container">

        <!-- ゲーム説明エリア -->
        <section class="game-rule-container">
            <h2 class="head-text">ゲーム説明</h2>

            <button class="small-btn" type="button">
                次へ
            </button>
        </section>

        <!-- スタートボタン -->
        <div class="start-area">
            <button class="medium-btn" type="button">
                スタート
            </button>
        </div>

        <!-- ゲーム選択ボタン -->
        <section class="menu-btn">
            <button class="medium-btn" type="button">
                計算
            </button>

            <button class="medium-btn" type="button">
                順番タップ
            </button>

            <button class="medium-btn" type="button">
                数字記憶
            </button>
        </section>

        <!-- キャラクターエリア -->
        <div class="character-area">

            <div class="character-comment">
                ゲームを選択してください
            </div>

            <img
                class="character"
                src="image/zilyou_zaiko.png"
                alt="MediThinkのキャラクター"
            >

        </div>

    </main>

</body>
</html>