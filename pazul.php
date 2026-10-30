<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediThink | パズル</title>
    <link rel="stylesheet" href="css/pazul.css">
</head>
<body class="pazul-body">
    <?php
    $header_notice = 'パズルを並べよう！';
    include 'header_home.php';
    ?>

    <main class="pazul-main">
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

        <div class="pazul-bottom">
            <a class="game-screen-link" href="home.php">ゲーム画面</a>

            <section class="piece-tray" aria-label="パズルのピース">
                <?php
                $piece_images = [
                    '透明背景の青緑赤白パズルピース-1.png',
                    '上段2列目のカラフルなパズルピース-2.png',
                    '上段3列目のジグソーピース-3.png',
                    '右上のカラフルなパズルピース-4.png',
                    '透明背景の2行1列ジグソーピース-5.png',
                    '透明背景の青緑赤白パズルピース-6.png',
                    '青緑赤白のパズルピース7-7.png',
                    '青緑赤白のジグソーピース-8.png',
                    '青緑赤白のジグソーパズル9番ピース-9.png',
                    '透明背景のパズルピース10-10.png',
                    '透明背景の青緑赤白ジグソーピース-11.png',
                    '右下のカラフルなジグソーピース-12.png',
                ];
                foreach ($piece_images as $piece => $piece_image):
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
        <p class="puzzle-help" aria-live="polite">ピースを選んで、盤面のマスをクリックしてください。</p>
    </main>
    <script>
        const pieces = document.querySelectorAll('.piece');
        const cells = document.querySelectorAll('.puzzle-cell');
        const help = document.querySelector('.puzzle-help');
        let selectedPiece = null;
        let placedCount = 0;

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
                    help.textContent = 'クリア！すべてのピースを正しく並べました。';
                    return;
                }

                help.textContent = 'ピースを選んで、盤面のマスをクリックしてください。';
            });
        });
    </script>
</body>
</html>