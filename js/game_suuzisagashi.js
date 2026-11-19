document.addEventListener('DOMContentLoaded', () => {
    const numberElements = document.querySelectorAll('.game-number');
    const noticeBox = document.getElementById('game-notice');
    const commentBox = document.getElementById('character-comment');

    // 探す順番（お知らせに合わせて 1 から 9 まで）
    const targetOrder = [1, 2, 3, 4, 5, 6, 7, 8, 9];
    let currentIndex = 0;
    let isFinished = false;

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

    updateGuide();

    numberElements.forEach((el) => {
        el.addEventListener('click', () => {
            if (isFinished) return;
            if (el.classList.contains('is-cleared')) return;

            const clickedNum = Number(el.textContent.trim());
            const expectedNum = getCurrentTarget();

            if (clickedNum === expectedNum) {
                // 正解！
                el.classList.add('is-cleared');
                currentIndex++;

                if (currentIndex >= targetOrder.length) {
                    // 全部クリア！
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
                    updateGuide();
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