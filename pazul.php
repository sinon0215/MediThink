<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediThink | パズル</title>
    <link rel="stylesheet" href="css/pazul.css">
    <link rel="stylesheet" href="css/gure-ninaru.css">
</head>
<body class="pazul-body">
    <?php
    $header_notice = 'パズルを並べよう！';
    include 'header_home.php';
    ?>

    <div class="puzzle-notice-area">
        <div class="puzzle-help" aria-live="polite">ピースを選んで、盤面のマスをクリックしてください。</div>
    </div>

    <main class="pazul-main">
        <div class="puzzle-timer" id="puzzle-timer" data-duration="180" role="timer" aria-label="残り時間" aria-live="off">
            残り時間 03:00
        </div>
        <div class="puzzle-stage">
            <section class="puzzle-board" aria-label="パズル盤">
                <?php for ($row = 0; $row < 3; $row++): ?>
                    <?php for ($column = 0; $column < 4; $column++): ?>
                        <?php $cell = $row * 4 + $column; ?>
                        <button class="puzzle-cell puzzle-cell-<?= $row ?>-<?= $column ?>" type="button" aria-label="空のパズルマス" data-cell="<?= $cell ?>">
                            <span class="puzzle-tab" aria-hidden="true"></span>
                        </button>
                    <?php endfor; ?>
                <?php endfor; ?>
            </section>
            <figure class="puzzle-reference">
                <figcaption>完成イメージ</figcaption>
                <img src="image/neko.webp" alt="パズルの完成イメージ">
            </figure>
        </div>

        <div class="pazul-bottom">

            <section class="piece-tray" aria-label="パズルのピース">
                <?php
                $piece_images = [
                    'piece-01.png',
                    'piece-02.png',
                    'piece-03.png',
                    'piece-04.png',
                    'piece-05.png',
                    'piece-06.png',
                    'piece-07.png',
                    'piece-08.png',
                    'piece-09.png',
                    'piece-10.png',
                    'piece-11.png',
                    'piece-12.png',
                ];
                $piece_order = range(0, count($piece_images) - 1);
                shuffle($piece_order);
                foreach ($piece_order as $piece):
                    $piece_image = $piece_images[$piece];
                ?>
                    <button class="piece" type="button" aria-label="パズルピース<?= $piece + 1 ?>" data-piece="<?= $piece ?>">
                        <img src="image/<?= htmlspecialchars($piece_image, ENT_QUOTES, 'UTF-8') ?>" alt="">
                    </button>
                <?php endforeach; ?>
            </section>

            <div class="pazul-character" aria-label="キャラクター">
                <img src="image/zilyou_zaiko.png" alt="服薬手帳を持つキャラクター">
            </div>
        </div>
    </main>
    <div class="gray-bg" id="puzzle-instructions-bg" aria-hidden="true"></div>
    <section class="gray-box" id="puzzle-instructions" role="dialog" aria-modal="true" aria-labelledby="puzzle-instructions-title">
        <h2 id="puzzle-instructions-title">遊び方</h2>
        <p>下のピースを選び、完成イメージを見ながら正しいマスをクリックしてください。</p>
        <p>すべてのピースを3分以内に並べるとクリアです。間違えて置いた回数も記録されます。</p>
        <button type="button" class="gray-close-btn" id="puzzle-start">ゲーム開始</button>
    </section>
    <div class="clear-overlay" role="status" aria-live="assertive" aria-hidden="true">
        <div class="clear-message">クリア！</div>
    </div>
    <script>
        const backButton = document.querySelector('.medithink-header .medithink-logout-btn');
        if (backButton) {
            backButton.textContent = '戻る';
            backButton.addEventListener('click', (event) => {
                event.preventDefault();
                window.history.back();
            });
        }

        const pieces = document.querySelectorAll('.piece');
        const cells = document.querySelectorAll('.puzzle-cell');
        const help = document.querySelector('.puzzle-help');
        const timerButton = document.getElementById('puzzle-timer');
        const instructionsBg = document.getElementById('puzzle-instructions-bg');
        const instructions = document.getElementById('puzzle-instructions');
        const startButton = document.getElementById('puzzle-start');
        let selectedPiece = null;
        let placedCount = 0;
        let missCount = 0;
        let timerId = null;
        let startTime = null;
        let remainingSeconds = Number(timerButton?.dataset.duration || 180);
        let timerStarted = false;

        const formatTime = (seconds) => {
            const minutes = Math.floor(seconds / 60);
            const rest = seconds % 60;
            return `${String(minutes).padStart(2, '0')}:${String(rest).padStart(2, '0')}`;
        };

        const updateTimer = () => {
            if (!timerButton) {
                return;
            }

            timerButton.textContent = `残り時間 ${formatTime(remainingSeconds)}`;
            timerButton.classList.toggle('is-warning', remainingSeconds <= 30);
        };

        const finishTimer = () => {
            window.clearInterval(timerId);
            timerId = null;
            document.body.classList.add('puzzle-timeout');
            selectedPiece = null;
            pieces.forEach((piece) => {
                piece.disabled = true;
                piece.classList.remove('is-selected');
            });
            cells.forEach((cell) => {
                cell.disabled = true;
            });
            if (timerButton) {
                timerButton.textContent = '時間切れ';
            }
            help.textContent = '時間切れです。ページを再読み込みしてもう一度挑戦してください。';
        };

        const startTimer = () => {
            if (timerStarted || !timerButton) {
                return;
            }

            timerStarted = true;
            startTime = performance.now();
            updateTimer();
            timerId = window.setInterval(() => {
                remainingSeconds -= 1;
                updateTimer();
                if (remainingSeconds <= 0) {
                    finishTimer();
                }
            }, 1000);
        };

        updateTimer();
        startButton.addEventListener('click', () => {
            instructions.setAttribute('aria-hidden', 'true');
            instructions.style.display = 'none';
            instructionsBg.style.display = 'none';
            startTimer();
        });

        pieces.forEach((piece) => {
            piece.addEventListener('click', () => {
                if (piece.disabled) {
                    return;
                }

                pieces.forEach((item) => item.classList.remove('is-selected'));
                selectedPiece = piece;
                piece.classList.add('is-selected');
                help.textContent = '配置する盤面のマスをクリックしてください。';
            });
        });

        cells.forEach((cell) => {
            cell.addEventListener('click', () => {
                if (!selectedPiece || cell.classList.contains('is-filled')) {
                    help.textContent = selectedPiece
                        ? 'そのマスにはすでにピースがあります。別のマスを選んでください。'
                        : '先に下のピースを選んでください。';
                    return;
                }

                if (selectedPiece.dataset.piece !== cell.dataset.cell) {
                    missCount += 1;
                    help.textContent = 'そのピースの置き場所が違います。別のマスを選んでください。';
                    cell.classList.add('is-wrong');
                    window.setTimeout(() => cell.classList.remove('is-wrong'), 350);
                    return;
                }

                const placedPiece = selectedPiece.querySelector('img').cloneNode(true);
                placedPiece.classList.add('placed-piece');
                cell.appendChild(placedPiece);
                cell.classList.add('is-filled');
                cell.setAttribute('aria-label', '配置済みのパズルマス');
                selectedPiece.disabled = true;
                selectedPiece.classList.remove('is-selected');
                selectedPiece = null;
                placedCount += 1;

                if (placedCount === cells.length) {
                    document.body.classList.add('puzzle-cleared');
                    document.querySelector('.clear-overlay').setAttribute('aria-hidden', 'false');
                    window.clearInterval(timerId);
                    timerId = null;
                    if (timerButton) {
                        timerButton.textContent = 'クリア！';
                    }
                    help.textContent = 'クリア！すべてのピースを正しく並べました。';
                    const clearTime = ((performance.now() - startTime) / 1000).toFixed(1);
                    window.setTimeout(() => {
                        window.location.href = `game_result.php?time=${encodeURIComponent(clearTime)}&miss=${missCount}&game=pazul`;
                    }, 1000);
                    return;
                }

                help.textContent = 'ピースを選んで、盤面のマスをクリックしてください。';
            });
        });
    </script>
</body>
</html>