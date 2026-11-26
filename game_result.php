<?php
date_default_timezone_set('Asia/Tokyo');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// URLパラメータからクリアタイムとお手つき回数を取得
$timeRaw = isset($_GET['time']) ? (float)$_GET['time'] : 0.0;
$missRaw = isset($_GET['miss']) ? (int)$_GET['miss'] : 0;

$clearTime = max(0.0, $timeRaw);
$missCount = max(0, $missRaw);
$isPazulResult = isset($_GET['game']) && $_GET['game'] === 'pazul';

if ($isPazulResult) {
    $fullTimeScoreSeconds = 120.0;
    $timeLimitSeconds = 180.0;
    $timeScore = (int)round(
        700 * max(0.0, 1.0 - max(0.0, $clearTime - $fullTimeScoreSeconds) / ($timeLimitSeconds - $fullTimeScoreSeconds))
    );
    $freeMisses = 2;
    $accuracyScore = max(0, 300 - (max(0, $missCount - $freeMisses) * 30));
    $score = min(1000, $timeScore + $accuracyScore);
} else {
    // 基準タイム（7.0秒以内ならタイム減点なし）
    $targetTime = 7.0;
    $overTime = max(0.0, $clearTime - $targetTime);

    // スコア計算：1000点 - (超過秒数 × 25) - (お手つき回数 × 80)、最低100点
    $rawScore = 1000 - ($overTime * 25) - ($missCount * 80);
    $score = max(100, (int)round($rawScore));
}

// 結果表示テキスト
$resultLabel = ($missCount === 0) ? 'クリア！（お手つきなし）' : 'クリア！';

// DB保存（game_records へ履歴保存・直近10件維持 + game_stats へ累計回数・最高スコア反映）
$db_host = 'localhost';
$db_name = 'medithink_db';
$db_user = 'medithink_admin';
$db_pass = 'MediThink123!';
$game_name = 'suuzisagashi';

$recordToken = md5(
    $_SESSION['user_id'] . '|' .
    $game_name . '|' .
    $score . '|' .
    $clearTime . '|' .
    $missCount . '|' .
    ($_SERVER['REQUEST_URI'] ?? '')
);

if (!isset($_SESSION['saved_game_tokens']) || !is_array($_SESSION['saved_game_tokens'])) {
    $_SESSION['saved_game_tokens'] = [];
}

if (!$isPazulResult && !in_array($recordToken, $_SESSION['saved_game_tokens'], true)) {
    try {
        $pdo = new PDO(
            "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
            $db_user,
            $db_pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $userId = (int)$_SESSION['user_id'];

        $pdo->beginTransaction();

        // 1. 今回のスコア履歴を保存
        $stmt = $pdo->prepare(
            "INSERT INTO game_records (user_id, game_name, score)
             VALUES (:user_id, :game_name, :score)"
        );
        $stmt->execute([
            ':user_id'   => $userId,
            ':game_name' => $game_name,
            ':score'     => $score,
        ]);

        // 2. 該当ユーザー・ゲームの履歴を直近10件までに絞る
        $stmtOld = $pdo->prepare(
            "SELECT game_record_id
             FROM game_records
             WHERE user_id = :user_id
               AND game_name = :game_name
             ORDER BY game_record_id DESC
             LIMIT 1000 OFFSET 10"
        );
        $stmtOld->execute([
            ':user_id'   => $userId,
            ':game_name' => $game_name,
        ]);
        $oldIds = $stmtOld->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($oldIds)) {
            $placeholders = implode(',', array_fill(0, count($oldIds), '?'));
            $stmtDelete = $pdo->prepare(
                "DELETE FROM game_records WHERE game_record_id IN ($placeholders)"
            );
            $stmtDelete->execute($oldIds);
        }

        // 3. 累計プレイ回数と最高スコアを game_stats に反映（自己ベストを超えた場合のみ更新）
        $stmtStats = $pdo->prepare(
            "INSERT INTO game_stats (user_id, game_name, best_score, play_count)
             VALUES (:user_id, :game_name, :score_insert, 1)
             ON DUPLICATE KEY UPDATE
                 best_score = CASE
                     WHEN best_score >= :score_update THEN best_score
                     ELSE :score_update_fallback
                 END,
                 play_count = play_count + 1"
        );
        $stmtStats->execute([
            ':user_id'               => $userId,
            ':game_name'             => $game_name,
            ':score_insert'          => $score,
            ':score_update'          => $score,
            ':score_update_fallback' => $score,
        ]);

        $pdo->commit();
        $_SESSION['saved_game_tokens'][] = $recordToken;
    } catch (PDOException $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('game_result DB save error: ' . $e->getMessage());
    }
}
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
                <?php if ($isPazulResult): ?>
                    <div style="font-size: 1.05rem; margin-top: 6px; font-weight: normal;">
                        時間点：<?= htmlspecialchars((string)$timeScore, ENT_QUOTES, 'UTF-8') ?>/700点 ／
                        正確さ：<?= htmlspecialchars((string)$accuracyScore, ENT_QUOTES, 'UTF-8') ?>/300点
                    </div>
                <?php endif; ?>
                <div style="font-size: 1.05rem; margin-top: 10px; font-weight: normal;">
                    プレイ時間：<?= htmlspecialchars(number_format($clearTime, 1), ENT_QUOTES, 'UTF-8') ?>秒
                </div>
                <div style="font-size: 1.05rem; margin-top: 6px; font-weight: normal;">
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

                actionBtn.addEventListener('click', async (e) => {
                    e.preventDefault();

                    sessionStorage.removeItem(STORAGE_RECORD_MODE);
                    const returnUrl = sessionStorage.getItem(STORAGE_RETURN_URL) || 'home.php';
                    sessionStorage.removeItem(STORAGE_RETURN_URL);

                    try {
                        await fetch('mark_medication_taken.php', {
                            method: 'POST',
                            credentials: 'same-origin',
                        });
                    } catch (err) {
                        console.error('Failed to mark medication taken:', err);
                    }

                    window.location.href = returnUrl;
                });
            }
        })();
        </script>

    </body>
    </html>