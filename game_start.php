
<?php
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

    <main class="main-container flex-fill">

        <!-- 左側：ゲーム説明 -->
        <section class="game-rule-container">

            <h2 class="head-text">
                ゲーム説明
            </h2>

            <button class="small-btn" type="button">
                次へ
            </button>

        </section>

        <!-- 左下：スタートボタン -->
        <div class="start-area">

            <button class="medium-btn" type="button">
                スタート
            </button>

        </div>

        <!-- 右側：ゲームメニュー -->
        <section class="menu-btn flex-fill">

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

        <!-- キャラクターとコメント -->
        <div class="character-area">

            <div class="character-comment flex-fill">
                ゲームを選択してください
            </div>

            <img
                class="character flex-fill"
                src="image/zilyou_zaiko.png"
                alt="MediThinkのキャラクター"
            >

        </div>

    </main>

</body>
</html>
