const modal = document.getElementById("timeModal");
const timeInput = document.getElementById("timeInput");
const modalTitle = document.getElementById("modalTitle");

const cells = document.querySelectorAll(".calendar tbody td");
const settingBtn = document.getElementById("settingBtn");

const days = ["月曜日", "火曜日", "水曜日", "木曜日",
              "金曜日", "土曜日", "日曜日"];

const timings = ["morning", "noon", "evening", "bedtime"];

let selectedCell = null;
// 未保存の変更があるかどうか
let hasUnsavedChanges = false;

// 時刻入力欄のクリック
timeInput.addEventListener("click", function() {
    this.showPicker();
});

// 各マスをクリックしたとき
cells.forEach((cell, index) => {
    const row = Math.floor(index / 7);
    const column = index % 7;

    cell.dataset.day = column;
    cell.dataset.timing = timings[row];

    cell.addEventListener("click", function() {
        selectedCell = this;

        modalTitle.textContent =
            days[column] + "・" + ["朝", "昼", "夜", "寝る前"][row] + "の時刻設定";

        timeInput.value = this.dataset.time || "08:00";

        modal.style.display = "flex";
    });
});

// 決定ボタン（画面だけ変更）
document.getElementById("saveBtn").addEventListener("click", function() {
    if (selectedCell && timeInput.value) {
        selectedCell.dataset.time = timeInput.value;
        selectedCell.textContent = timeInput.value;

        hasUnsavedChanges = true;
    }

    modal.style.display = "none";
});

// 削除ボタン（画面だけ変更）
document.getElementById("deleteBtn").addEventListener("click", function() {
    if (selectedCell) {
        selectedCell.textContent = "";
        delete selectedCell.dataset.time;

        hasUnsavedChanges = true;
    }

    modal.style.display = "none";
});

// キャンセルボタン
document.getElementById("cancelBtn").addEventListener("click", function() {
    modal.style.display = "none";
});

// モーダル外をクリックしたとき
modal.addEventListener("click", function(event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
});

// 設定するボタン（DBに保存）
settingBtn.addEventListener("click", async function() {

    const schedules = [];

    cells.forEach(cell => {
        if (cell.dataset.time) {
            schedules.push({
                day_of_week: Number(cell.dataset.day),
                medication_timing: cell.dataset.timing,
                scheduled_time: cell.dataset.time
            });
        }
    });

    try {
        const response = await fetch("save_schedule.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                schedules: schedules
            })
        });

        const result = await response.json();

        if (result.success) {
            hasUnsavedChanges = false;
            alert("設定を保存しました！");
        } else {
            alert(result.message || "保存に失敗しました。");
        }

    } catch (error) {
        console.error(error);
        alert("通信エラーが発生しました。");
    }
});

// ページを開いたときDBから読み込む
async function loadSchedules() {

    try {
        const response = await fetch("get_schedule.php");
        const result = await response.json();

        if (result.success) {
            result.schedules.forEach(schedule => {

                const day = Number(schedule.day_of_week);
                const timing = schedule.medication_timing;

                const timingIndex = timings.indexOf(timing);
                const index = timingIndex * 7 + day;

                const cell = cells[index];

                if (cell && schedule.scheduled_time) {
                    cell.dataset.time = schedule.scheduled_time.substring(0, 5);
                    cell.textContent = cell.dataset.time;
                }
            });
        }

    } catch (error) {
        console.error(error);
        alert("設定の読み込みに失敗しました。");
    }
}

loadSchedules();

// ホームに戻るときの未保存確認
document.querySelector(".medithink-logout-btn")
    .addEventListener("click", function(event) {

        if (hasUnsavedChanges) {
            const answer = confirm(
                "変更した内容が保存されていません。\nホームに戻ってもよろしいですか？"
            );

            if (!answer) {
                event.preventDefault();
            }
        }
    });