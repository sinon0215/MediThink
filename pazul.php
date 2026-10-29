<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediThink | パズル</title>
    <link rel="stylesheet" href="pazul.css">
</head>
<body class="pazul-body">
    <?php
    $header_notice = 'パズルを並べよう！';
    include 'header.php';
    ?>

    <main class="pazul-main">
        <section class="puzzle-board" aria-label="パズル盤">
            <?php for ($row = 0; $row < 3; $row++): ?>
                <?php for ($column = 0; $column < 4; $column++): ?>
                    <div class="puzzle-cell puzzle-cell-<?= $row ?>-<?= $column ?>">
                        <span class="puzzle-tab" aria-hidden="true"></span>
                    </div>
                <?php endfor; ?>
            <?php endfor; ?>
        </section>

        <div class="pazul-bottom">
            <a class="game-screen-link" href="home.php">ゲーム画面</a>

            <section class="piece-tray" aria-label="パズルのピース">
                <?php for ($piece = 0; $piece < 7; $piece++): ?>
                    <button class="piece piece-<?= $piece ?>" type="button" aria-label="パズルピース<?= $piece + 1 ?>">
                        <span></span>
                    </button>
                <?php endfor; ?>
            </section>

            <div class="pazul-character" aria-label="キャラクター">
                <div class="character-body"></div>
                <div class="character-face"></div>
                <div class="character-book">服薬手帳</div>
            </div>
        </div>
    </main>
</body>
</html>