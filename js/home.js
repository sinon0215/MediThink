
document.addEventListener("DOMContentLoaded", function() {

    // 服薬スケジュールを取得
    fetch("get_schedule.php")
        .then(response => response.json())
        .then(data => {

            if (!data.success) {
                console.error("スケジュールを取得できませんでした");
                return;
            }

            data.schedules.forEach(schedule => {

                const cell = document.querySelector(
                    `.home-calendar td[data-day="${schedule.day_of_week}"][data-timing="${schedule.medication_timing}"]`
                );

                if (cell) {
                    const img = document.createElement("img");

                    img.src = "./image/tablet.png";
                    img.alt = "服薬予定";
                    img.className = "medicine-icon";

                    cell.appendChild(img);
                }

            });

        })
        .catch(error => {
            console.error("取得エラー:", error);
        });

});


// 日付表示と今日の曜日の色付け
document.addEventListener("DOMContentLoaded", function() {

    const calendar = document.querySelector(".home-calendar");
    if (!calendar) return;

    const thead = calendar.querySelector("thead");
    const dateRow = document.createElement("tr");

    dateRow.className = "date-row";

    // 左端は空欄
    const emptyCell = document.createElement("th");
    dateRow.appendChild(emptyCell);

    const today = new Date();

    // 今週の月曜日の日付を取得
    const monday = new Date(today);
    const day = today.getDay();
    monday.setDate(today.getDate() - ((day + 6) % 7));

    // 月曜日から日曜日の日付を表示
    for (let i = 0; i < 7; i++) {
        const date = new Date(monday);
        date.setDate(monday.getDate() + i);

        const cell = document.createElement("th");
        cell.textContent = date.getDate() + "日";

        dateRow.appendChild(cell);
    }

    // 曜日の行の下に日付を追加
    thead.appendChild(dateRow);

    // 今日の曜日を取得（月曜=1、日曜=0）
    const todayDay = today.getDay();

    // 今日の列をクリーム色にする
    const todayColumn = todayDay === 0 ? 6 : todayDay - 1;

    calendar.querySelectorAll("tr").forEach(row => {
        const cell = row.children[todayColumn + 1];

        if (cell) {
            cell.classList.add("today-column");
        }
    });

});

