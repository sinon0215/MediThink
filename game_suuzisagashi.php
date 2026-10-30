
<?php
// ゲームで表示する数字
$numbers = range(0, 9);
shuffle($numbers);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>数字探しゲーム | MediThink</title>

    <!-- CSS読み込み -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
</head>

<body class="game-body">

    <!-- ヘッダー -->
    <?php
        $header_notice = '「1」から順番に数字を探そう';
        include 'header.php';
    ?>

    <!-- ゲームエリア -->
    <main class="main-container game-main-container">

        <section class="game-container" id="game-container">
            <?php foreach ($numbers as $number): ?>
                <div class="game-number">
                    <?= htmlspecialchars((string)$number, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endforeach; ?>
        </section>

    </main>

    <!-- キャラクターと応援コメント -->
    <div class="character-area">

        <div class="character-comment" id="character-comment">
            数字探してちょ
        </div>

        <div class="character">
            <img
                src="image/zilyou_zaiko.png"
                alt="キャラクター"
                class="game-character-image"
            >
        </div>

    </div>

</body>
</html>
