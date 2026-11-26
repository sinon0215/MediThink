// DBから渡された記録データ（直近10件の推移 + 累計の最高スコア・プレイ回数）
const recordsData = window.RECORDS_DATA || {};

// 現在のグラフ
let scoreChart = null;

// グラフを表示する関数
function showGraph(gameName) {
    const gameRecord = recordsData[gameName] || {
        scores: [],
        bestScore: null,
        lastScore: null,
        playCount: 0
    };

    const scores = Array.isArray(gameRecord.scores) ? gameRecord.scores : [];

    // 最高スコア・前回のスコア・プレイ回数（10件制限前の累計も含む）
    const bestScore = gameRecord.bestScore !== null && gameRecord.bestScore !== undefined
        ? gameRecord.bestScore
        : (scores.length > 0 ? Math.max(...scores) : null);

    const lastScore = gameRecord.lastScore !== null && gameRecord.lastScore !== undefined
        ? gameRecord.lastScore
        : (scores.length > 0 ? scores[scores.length - 1] : null);

    const playCount = gameRecord.playCount !== null && gameRecord.playCount !== undefined
        ? gameRecord.playCount
        : scores.length;

    // 画面に表示
    document.getElementById("bestScore").textContent =
        bestScore !== null ? bestScore + " 点" : "-- 点";
    document.getElementById("lastScore").textContent =
        lastScore !== null ? lastScore + " 点" : "-- 点";
    document.getElementById("playCount").textContent =
        playCount + " 回";

    // グラフのタイトル
    const gameTitle = gameName === "suuzisagashi"
        ? "数字探し"
        : "パズル";

    document.getElementById("graphTitle").textContent =
        gameTitle + "のスコア推移";

    // グラフのデータ
    const labels = scores.map((_, index) => (index + 1) + "回目");

    // 既存のグラフを削除
    if (scoreChart) {
        scoreChart.destroy();
    }

    // グラフを作成
    const ctx = document.getElementById("scoreChart").getContext("2d");

    scoreChart = new Chart(ctx, {
        type: "line",

        data: {
            labels: labels,

            datasets: [{
                label: "スコア",
                data: scores,

                borderColor: "#3999bd",
                backgroundColor: "rgba(57, 153, 189, 0.15)",

                borderWidth: 4,
                pointRadius: 6,
                pointHoverRadius: 9,
                pointBackgroundColor: "#3999bd",

                tension: 0.3,
                fill: false
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false
                }
            },

            scales: {
                x: {
                    title: {
                        display: true,
                        text: "プレイ回数",
                        font: {
                            size: 16
                        }
                    },
                    ticks: {
                        font: {
                            size: 14
                        }
                    }
                },

                y: {
                    beginAtZero: true,

                    title: {
                        display: true,
                        text: "スコア",
                        font: {
                            size: 16
                        }
                    },

                    ticks: {
                        font: {
                            size: 14
                        }
                    }
                }
            }
        }
    });
}


// ページ読み込み後の処理
document.addEventListener("DOMContentLoaded", function () {

    // 最初は数字探しを表示
    showGraph("suuzisagashi");

    // ゲーム切り替えボタン
    const tabs = document.querySelectorAll(".game-tab");

    tabs.forEach(tab => {

        tab.addEventListener("click", function () {

            // 選択状態を切り替える
            tabs.forEach(button => {
                button.classList.remove("active");
            });

            this.classList.add("active");

            // 選択したゲームのグラフを表示
            const gameName = this.dataset.game;
            showGraph(gameName);
        });
    });

});