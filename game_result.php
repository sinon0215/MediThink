<?php
// URLパラメータからクリアタイムとお手つき回数を取得
$timeRaw = isset($_GET['time']) ? (float)$_GET['time'] : 0.0;
$missRaw = isset($_GET['miss']) ? (int)$_GET['miss'] : 0;

$clearTime = max(0.0, $timeRaw);
$missCount = max(0, $missRaw);

// スコア計算：基準1000点 - (秒数 × 25) - (お手つき回数 × 80)、最低100点
$rawScore = 1000 - ($clearTime * 25) - ($missCount * 80);
$score = max(100, (int)round($rawScore));

// 結果表示テキスト
$resultLabel = ($missCount === 0) ? 'クリア！（お手つきなし）' : 'クリア！';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ゲーム結果 - MediThink</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/game_result.css">
</head>
<body class="result-body">

    <!-- 既存のヘッダーを読み込み -->
    <?php include 'header.php'; ?>

    <!-- メインエリア -->
    <main class="result-main">
        <div class="result-banner">ゲーム結果</div>

        <div class="result-status-box">
            <?= htmlspecialchars($resultLabel, ENT_QUOTES, 'UTF-8') ?>
        </div>

        <div class="result-score-box">
            <div>
                <div>スコア：<?= htmlspecialchars((string)$score, ENT_QUOTES, 'UTF-8') ?>点</div>
                <div style="font-size: 1.05rem; margin-top: 10px; font-weight: normal;">
                    クリアタイム：<?= htmlspecialchars(number_format($clearTime, 1), ENT_QUOTES, 'UTF-8') ?>秒 ／
                    お手つき：<?= htmlspecialchars((string)$missCount, ENT_QUOTES, 'UTF-8') ?>回
                </div>
            </div>
        </div>

        <a href="game_select.php" id="result-action-btn" class="result-back-btn">選択画面に戻る</a>
    </main>

    <!-- 右下の吹き出し＆キャラクター -->
    <div class="result-character-area">
        <div class="result-comment">お疲れ様でした。</div>
        <div class="result-character">
            <img src="image/zilyou_zaiko.png" alt="キャラクター" class="result-character-image">
        </div>
    </div>

    <script>
    (() => {
        const STORAGE_RECORD_MODE = 'medi_record_mode';
        const STORAGE_RETURN_URL = 'medi_notice_return_url';

        const actionBtn = document.getElementById('result-action-btn');
        if (!actionBtn) return;

        const isRecordMode = sessionStorage.getItem(STORAGE_RECORD_MODE) === '1';

        if (isRecordMode) {
            // 服薬記録モードのときだけ「薬を飲みました！」に変更し、リンク先を home.php にする
            actionBtn.textContent = '薬を飲みました！';
            actionBtn.href = 'home.php';

            actionBtn.addEventListener('click', (e) => {
                e.preventDefault();

                // TODO: DB保存処理が決まったらここに追加

                sessionStorage.removeItem(STORAGE_RECORD_MODE);
                const returnUrl = sessionStorage.getItem(STORAGE_RETURN_URL) || 'home.php';
                sessionStorage.removeItem(STORAGE_RETURN_URL);

                window.location.href = returnUrl;
            });
        }
    })();
    </script>

</body>
</html>