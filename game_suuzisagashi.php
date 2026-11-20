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

    <!-- グレーにするためのコードです -->
    <link rel="stylesheet" href="css/gure-ninaru.css">
</head>

<body class="game-body">

    <!-- グレーにするためのコードです -->
    <div class="gray-bg" id="gray-bg"></div>
    <div class="gray-box" id="gray-box">
        <h2>遊び方</h2>
        <p>1から9までの数字を順番に押そう！</p>
        <button type="button" class="gray-close-btn" id="gray-close-btn">戻る</button>
    </div>

    <!-- ヘッダー -->
    <?php
        include 'header.php';
    ?>

    <!-- 右上タイマー（ヘッダーはいじらず上に重ねる） -->
    <div class="game-top-timer" id="game-timer">
        タイム：0.0秒
    </div>

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
            数字を探してね
        </div>

        <div class="character">
            <img src="image/zilyou_zaiko.png" alt="キャラクター" class="game-character-image">
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // グレーにするためのコードです
            const grayBg = document.getElementById('gray-bg');
            const grayBox = document.getElementById('gray-box');
            const grayCloseBtn = document.getElementById('gray-close-btn');

            if (grayCloseBtn) {
                grayCloseBtn.addEventListener('click', () => {
                    if (grayBg) grayBg.style.display = 'none';
                    if (grayBox) grayBox.style.display = 'none';
                });
            }

            const numberElements = document.querySelectorAll('.game-number');
            const gameContainer = document.getElementById('game-container');
            const noticeBox = document.getElementById('game-notice');
            const timerBox = document.getElementById('game-timer');
            const commentBox = document.getElementById('character-comment');

            // 1 から 9 まで順番に探す設定
            const targetOrder = [1, 2, 3, 4, 5, 6, 7, 8, 9];
            let currentIndex = 0;
            let isFinished = false;

            // タイマー管理（最初は未スタート）
            let startTime = null;
            let timerInterval = null;
            let isTimerStarted = false;

            function formatTime(ms) {
                return (ms / 1000).toFixed(1);
            }

            function startTimer() {
                if (isTimerStarted) return;
                isTimerStarted = true;
                startTime = performance.now();

                timerInterval = setInterval(() => {
                    if (isFinished) return;
                    const elapsed = performance.now() - startTime;
                    if (timerBox) {
                        timerBox.textContent = `タイム：${formatTime(elapsed)}秒`;
                    }
                }, 100);
            }

            function stopTimer() {
                if (timerInterval) {
                    clearInterval(timerInterval);
                    timerInterval = null;
                }
                if (!isTimerStarted || startTime === null) {
                    return '0.0';
                }
                const finalElapsed = performance.now() - startTime;
                const finalSec = formatTime(finalElapsed);
                if (timerBox) {
                    timerBox.textContent = `タイム：${finalSec}秒`;
                }
                return finalSec;
            }

            function getCurrentTarget() {
                return targetOrder[currentIndex];
            }

            function updateNotice() {
                if (isFinished || !noticeBox) return;
                noticeBox.textContent = `「${getCurrentTarget()}」を探そう！`;
            }

            function showResultButton(clearSec) {
                if (!gameContainer) return;
                if (gameContainer.querySelector('.game-result-link-btn')) return;

                const btn = document.createElement('a');
                btn.href = `game_result.php?time=${encodeURIComponent(clearSec)}`;
                btn.className = 'game-result-link-btn';
                btn.textContent = '結果画面に移動';
                gameContainer.appendChild(btn);
            }

            numberElements.forEach((el) => {
                el.addEventListener('click', () => {
                    if (isFinished) return;
                    if (el.classList.contains('is-cleared')) return;

                    const clickedNum = Number(el.dataset.number);
                    const expectedNum = getCurrentTarget();

                    if (clickedNum === expectedNum) {
                        // 「1」を押したタイミングでタイマー開始
                        if (expectedNum === 1) {
                            startTimer();
                        }

                        // 正解
                        el.classList.add('is-cleared');
                        currentIndex++;

                        if (currentIndex >= targetOrder.length) {
                            // 全クリア
                            isFinished = true;
                            const clearSec = stopTimer();

                            if (noticeBox) {
                                noticeBox.textContent = `ゲームクリアおめでとうございます！（記録：${clearSec}秒）`;
                            }
                            if (commentBox) {
                                commentBox.textContent = `${clearSec}秒で完走です！とても良い結果です。`;
                            }
                            showResultButton(clearSec);
                        } else {
                            const nextNum = getCurrentTarget();
                            if (commentBox) {
                                commentBox.textContent = `いいですね！次は「${nextNum}」です。`;
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
                            commentBox.textContent = `失礼ですが、次の数字は「${expectedNum}」です。`;
                        }
                    }
                });
            });
        });
    </script>

</body>
</html>