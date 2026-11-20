document.addEventListener('DOMContentLoaded', () => {
    const numberElements = document.querySelectorAll('.game-number');
    const gameContainer = document.querySelector('.game-container');
    const noticeBox = document.getElementById('game-notice');
    const timerBox = document.getElementById('game-timer');
    const commentBox = document.getElementById('character-comment');

    // 探す順番（お知らせに合わせて 1 から 9 まで）
    const targetOrder = [1, 2, 3, 4, 5, 6, 7, 8, 9];
    let currentIndex = 0;
    let missCount = 0; // お手つき回数
    let isFinished = false;

    // タイマー管理（1を押したときにスタート）
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
        const finalSec = formatTime(performance.now() - startTime);
        if (timerBox) {
            timerBox.textContent = `タイム：${finalSec}秒`;
        }
        return finalSec;
    }

    function getCurrentTarget() {
        return targetOrder[currentIndex];
    }

    function updateGuide() {
        if (isFinished) return;
        const nextNum = getCurrentTarget();
        if (noticeBox) {
            noticeBox.textContent = `「${nextNum}」を探そう！`;
        }
    }

    function showResultButton(clearSec, misses) {
        if (!gameContainer) return;
        if (gameContainer.querySelector('.game-result-link-btn')) return;

        const btn = document.createElement('a');
        btn.href = `game_result.php?time=${encodeURIComponent(clearSec)}&miss=${encodeURIComponent(misses)}`;
        btn.className = 'game-result-link-btn';
        btn.textContent = '結果画面に移動';
        gameContainer.appendChild(btn);
    }

    updateGuide();

    numberElements.forEach((el) => {
        el.addEventListener('click', () => {
            if (isFinished) return;
            if (el.classList.contains('is-cleared')) return;

            const clickedNum = Number(el.textContent.trim());
            const expectedNum = getCurrentTarget();

            if (clickedNum === expectedNum) {
                // 「1」を押したタイミングでタイマー開始
                if (expectedNum === 1) {
                    startTimer();
                }

                // 正解！
                el.classList.add('is-cleared');
                currentIndex++;

                if (currentIndex >= targetOrder.length) {
                    // 全部クリア！
                    isFinished = true;
                    const clearSec = stopTimer();

                    if (noticeBox) {
                        noticeBox.textContent = `クリアおめでとう！（${clearSec}秒）`;
                    }
                    if (commentBox) {
                        commentBox.textContent = `${clearSec}秒で完走〜！すごいやん！`;
                    }
                    showResultButton(clearSec, missCount);
                } else {
                    const nextNum = getCurrentTarget();
                    if (commentBox) {
                        commentBox.textContent = `ナイス！次は「${nextNum}」やで`;
                    }
                    updateGuide();
                }
            } else {
                // お手つき（違う数字を押したとき）
                missCount++;
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