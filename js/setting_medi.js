const modal = document.getElementById("timeModal");
const hourWheel = document.getElementById("hourWheel");
const minuteWheel = document.getElementById("minuteWheel");
const hourList = document.getElementById("hourList");
const minuteList = document.getElementById("minuteList");
const modalTitle = document.getElementById("modalTitle");

const cells = document.querySelectorAll(".calendar tbody td");
const settingBtn = document.getElementById("settingBtn");

// 時刻選択の数字を作成
function createWheel(list, max) {
    for (let i = 0; i <= max; i++) {
        const item = document.createElement("div");

        item.className = "wheel-item";
        item.textContent = String(i).padStart(2, "0");

        list.appendChild(item);
    }
}

// 時と分の数字を作成
createWheel(hourList, 23);
createWheel(minuteList, 59);

// 選択中の時刻を取得
function getWheelValue(wheel) {
    const itemHeight = 44;
    const index = Math.round(wheel.scrollTop / itemHeight);

    return String(index).padStart(2, "0");
}

// 指定した時刻までスクロール
function setWheelValue(wheel, value) {
    const itemHeight = 44;

    wheel.scrollTop = Number(value) * itemHeight;
}

// スクロール位置に合わせて選択中の数字を変更
function updateWheelSelection(wheel) {
    const itemHeight = 44;
    const index = Math.round(wheel.scrollTop / itemHeight);

    const items = wheel.querySelectorAll(".wheel-item");

    items.forEach((item, i) => {
        item.classList.toggle("selected", i === index);
    });
}

// スクロールしたときに選択状態を更新
hourWheel.addEventListener("scroll", function() {
    updateWheelSelection(hourWheel);
});

minuteWheel.addEventListener("scroll", function() {
    updateWheelSelection(minuteWheel);
});

// カレンダーの列順（0:月 〜 6:日）
const days = ["月曜日", "火曜日", "水曜日", "木曜日",
              "金曜日", "土曜日", "日曜日"];
const timingLabels = ["朝", "昼", "夜", "寝る前"];
const timings = ["morning", "noon", "evening", "bedtime"];

// 列インデックス（0:月〜6:日） → save_schedule.php の day_of_week（0:日〜6:土）
const colToDayOfWeek = [1, 2, 3, 4, 5, 6, 0];
// DBの day_of_week（0:日〜6:土） → カレンダーの列インデックス（0:月〜6:日）
const dayOfWeekToCol = { 1: 0, 2: 1, 3: 2, 4: 3, 5: 4, 6: 5, 0: 6 };

let selectedCell = null;
let hasUnsavedChanges = false;


// 各マスをクリックしたとき
cells.forEach((cell, index) => {
    const row = Math.floor(index / 7);
    const column = index % 7;

    cell.dataset.day = colToDayOfWeek[column];
    cell.dataset.timing = timings[row];

    cell.addEventListener("click", function() {
        selectedCell = this;

        modalTitle.textContent =
            days[column] + "・" + timingLabels[row] + "の時刻設定";

        const time = this.dataset.time || "08:00";
        const [hour, minute] = time.split(":");

        setWheelValue(hourWheel, hour);
        setWheelValue(minuteWheel, minute);
        updateWheelSelection(hourWheel);
        updateWheelSelection(minuteWheel);

        modal.style.display = "flex";
    });
});

// 決定ボタン（画面だけ変更）
document.getElementById("saveBtn").addEventListener("click", function() {

    if (selectedCell) {
        const hour = getWheelValue(hourWheel);
        const minute = getWheelValue(minuteWheel);

        const selectedTime = hour + ":" + minute;

        selectedCell.dataset.time = selectedTime;
        selectedCell.textContent = selectedTime;

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

// 「設定する」ボタン（そのまま save_schedule.php に保存）
settingBtn.addEventListener("click", async function() {

    // 設定内容の確認
    if (!confirm("この内容で設定しますか？")) {
        return;
    }

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

    settingBtn.disabled = true;

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
        console.log("保存結果:", result);

        if (response.ok && result.success) {
            hasUnsavedChanges = false;

            if (result.is_first_login) {
                window.location.href = "home.php";
                return;
            }

        } else {
            alert(result.message || "保存に失敗しました。");
        }
    } catch (error) {
        console.error(error);
        alert("通信エラーが発生しました。");
    } finally {
        settingBtn.disabled = false;
    }
});

// ページを開いたときDBから読み込む
async function loadSchedules() {
    try {
        const response = await fetch("get_schedule.php");
        const result = await response.json();

        if (response.ok && result.success && Array.isArray(result.schedules)) {
            result.schedules.forEach(schedule => {
                const day = Number(schedule.day_of_week);
                const timing = schedule.medication_timing;

                const timingIndex = timings.indexOf(timing);
                const column = dayOfWeekToCol[day];

                if (timingIndex === -1 || column === undefined) {
                    return;
                }

                const index = timingIndex * 7 + column;
                const cell = cells[index];

                if (cell && schedule.scheduled_time) {
                    cell.dataset.time = String(schedule.scheduled_time).substring(0, 5);
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
const logoutBtn = document.querySelector(".medithink-logout-btn");
if (logoutBtn) {
    logoutBtn.addEventListener("click", function(event) {
        if (hasUnsavedChanges) {
            const answer = confirm(
                "変更した内容が保存されていません。\nホームに戻ってもよろしいですか？"
            );

            if (!answer) {
                event.preventDefault();
            }
        }
    });
}