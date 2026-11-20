
// ゲームごとの仮スコア（直近10回分）
const gameScores = {
    suuzisagashi: [40, 55, 48, 65, 72, 60, 80, 75, 90, 100],
    pazul: [30, 45, 50, 42, 60, 70, 65, 80, 85, 95]
};

// 現在のグラフ
let scoreChart = null;

// グラフを表示する関数
function showGraph(gameName) {

    const scores = gameScores[gameName];

    // スコア情報を計算
    const bestScore = Math.max(...scores);
    const lastScore = scores[scores.length - 1];
    const playCount = scores.length;

    // 画面に表示
    document.getElementById("bestScore").textContent = bestScore + " 点";
    document.getElementById("lastScore").textContent = lastScore + " 点";
    document.getElementById("playCount").textContent = playCount + " 回";

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
