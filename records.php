
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
        <p class="description">これまでのゲームの記録を確認できます。</p>

        <!-- ゲーム切り替え -->
        <div class="game-tabs">
            <button class="game-tab active" data-game="suuzisagashi">
                数字探し
            </button>

            <button class="game-tab" data-game="pazul">
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

    <!-- グラフライブラリ -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="./js/records.js"></script>

</body>
</html>
