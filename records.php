<?php
date_default_timezone_set('Asia/Tokyo');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db_host = 'localhost';
$db_name = 'medithink_db';
$db_user = 'medithink_admin';
$db_pass = 'MediThink123!';

$games = ['suuzisagashi', 'pazul'];
$recordsData = [];

foreach ($games as $g) {
    $recordsData[$g] = [
        'scores'    => [],
        'bestScore' => null,
        'lastScore' => null,
        'playCount' => 0,
    ];
}

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $userId = (int)$_SESSION['user_id'];

    foreach ($games as $g) {
        $recordTable = $g === 'pazul' ? 'pazul_game_records' : 'game_records';
        $statsTable = $g === 'pazul' ? 'pazul_game_stats' : 'game_stats';

        // 直近10件のスコアを取得（新しい順に10件取得してから古い順に並べ替え）
        $stmtRecent = $pdo->prepare(
            "SELECT score
             FROM $recordTable
             WHERE user_id = :user_id
               AND game_name = :game_name
             ORDER BY game_record_id DESC
             LIMIT 10"
        );

        // 累計プレイ回数・最高スコアを取得
        $stmtStats = $pdo->prepare(
            "SELECT best_score, play_count
             FROM $statsTable
             WHERE user_id = :user_id
               AND game_name = :game_name
             LIMIT 1"
        );

        $stmtRecent->execute([
            ':user_id'   => $userId,
            ':game_name' => $g,
        ]);
        $recentDesc = array_map('intval', $stmtRecent->fetchAll(PDO::FETCH_COLUMN));

        if (!empty($recentDesc)) {
            $recordsData[$g]['lastScore'] = $recentDesc[0];
            $recordsData[$g]['scores'] = array_reverse($recentDesc);
        }

        $stmtStats->execute([
            ':user_id'   => $userId,
            ':game_name' => $g,
        ]);
        $statRow = $stmtStats->fetch(PDO::FETCH_ASSOC);

        if ($statRow) {
            $recordsData[$g]['bestScore'] = (int)$statRow['best_score'];
            $recordsData[$g]['playCount'] = (int)$statRow['play_count'];
        } elseif (!empty($recentDesc)) {
            $recordsData[$g]['bestScore'] = max($recentDesc);
            $recordsData[$g]['playCount'] = count($recentDesc);
        }
    }
} catch (PDOException $e) {
    error_log('records.php DB error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediThink | ゲーム履歴</title>
    <link rel="stylesheet" href="./css/records.css">
</head>
<body>
    <?php include 'header_home.php'; ?>
    <main class="records-container">

        <h1>ゲーム履歴</h1>
        <!-- <p class="description">これまでのゲームの記録を確認できます。</p> -->

        <!-- ゲーム切り替え -->
        <div class="game-tabs">
            <button type="button" class="game-tab active" data-game="suuzisagashi">
                数字探し
            </button>

            <button type="button" class="game-tab" data-game="pazul">
                パズル
            </button>
        </div>

        <!-- スコアグラフ -->
        <section class="graph-section">
            <h2 id="graphTitle">数字探しのスコア推移</h2>

            <p class="graph-description">
                直近10回のスコアを表示しています。
            </p>

            <div class="chart-container">
                <canvas id="scoreChart"></canvas>
            </div>
        </section>

        <!-- スコア情報 -->
        <section class="score-info">

            <div class="score-card">
                <p>最高スコア</p>
                <strong id="bestScore">-- 点</strong>
            </div>

            <div class="score-card">
                <p>前回のスコア</p>
                <strong id="lastScore">-- 点</strong>
            </div>

            <div class="score-card">
                <p>プレイ回数</p>
                <strong id="playCount">0 回</strong>
            </div>

        </section>

    </main>

    <script>
        window.RECORDS_DATA = <?= json_encode($recordsData, JSON_UNESCAPED_UNICODE) ?>;
    </script>

    <!-- グラフライブラリ -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="./js/records.js"></script>

</body>
</html>