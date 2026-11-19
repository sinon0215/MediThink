<?php
// ゲームで表示する数字（1〜9をシャッフル）
$numbers = range(1, 9);
shuffle($numbers);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>数字探しゲーム | MediThink</title>
    <link rel="stylesheet" href="css/style.css?v=2">
    <link rel="stylesheet" href="css/header.css?v=2">
    <link rel="stylesheet" href="css/game_suuzisagashi.css?v=2">
</head>

<body class="game-body">

    <!-- ヘッダー -->
    <?php
        include 'header.php';
    ?>

    <!-- お知らせ表示エリア -->
    <div class="game-notice-area">
        <div class="game-notice-box" id="game-notice">
            「1」から順番に数字を探そう
        </div>
    </div>

    <!-- ゲームエリア -->
    <main class="main-container game-main-container">

        <section class="game-container" id="game-container">
            <?php foreach ($numbers as $number): ?>
                <div class="game-number" data-number="<?= htmlspecialchars((string)$number, ENT_QUOTES, 'UTF-8') ?>">
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
            <img src="image/zilyou_zaiko.png" alt="キャラクター" class="game-character-image">
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const numberElements = document.querySelectorAll('.game-number');
            const noticeBox = document.getElementById('game-notice');
            const commentBox = document.getElementById('character-comment');

            // 1 から 9 まで順番に探す設定
            const targetOrder = [1, 2, 3, 4, 5, 6, 7, 8, 9];
            let currentIndex = 0;
            let isFinished = false;

            function getCurrentTarget() {
                return targetOrder[currentIndex];
            }

            function updateNotice() {
                if (isFinished || !noticeBox) return;
                noticeBox.textContent = `「${getCurrentTarget()}」を探そう！`;
            }

            numberElements.forEach((el) => {
                el.addEventListener('click', () => {
                    if (isFinished) return;
                    if (el.classList.contains('is-cleared')) return;

                    const clickedNum = Number(el.dataset.number);
                    const expectedNum = getCurrentTarget();

                    if (clickedNum === expectedNum) {
                        // 正解
                        el.classList.add('is-cleared');
                        currentIndex++;

                        if (currentIndex >= targetOrder.length) {
                            // 全クリア
                            isFinished = true;
                            if (noticeBox) {
                                noticeBox.textContent = 'クリアおめでとう！';
                            }
                            if (commentBox) {
                                commentBox.textContent = 'すごいやん！完走〜！';
                            }
                        } else {
                            const nextNum = getCurrentTarget();
                            if (commentBox) {
                                commentBox.textContent = `ナイス！次は「${nextNum}」やで`;
                            }
                            updateNotice();
                        }
                    } else {
                        // お手つき（違う数字を押したとき）
                        el.classList.add('is-wrong');
                        setTimeout(() => {
                            el.classList.remove('is-wrong');
                        }, 350);

                        if (commentBox) {
                            commentBox.textContent = `ちがうで〜！「${expectedNum}」を探してちょ`;
                        }
                    }
                });
            });
        });
    </script>

</body>
</html>