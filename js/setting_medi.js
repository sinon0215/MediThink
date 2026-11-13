const modal = document.getElementById("timeModal");
const timeInput = document.getElementById("timeInput");
const modalTitle = document.getElementById("modalTitle");

const cells = document.querySelectorAll(".calendar tbody td");

const days = ["月曜日", "火曜日", "水曜日", "木曜日",
              "金曜日", "土曜日", "日曜日"];

const timings = ["朝", "昼", "夜", "寝る前"];

let selectedCell = null;

// 時刻入力欄のクリック
timeInput.addEventListener("click", function() {
    this.showPicker();
});

// 各マスをクリックしたとき
cells.forEach((cell, index) => {
    const row = Math.floor(index / 7);
    const column = index % 7;

    cell.textContent = "";

    cell.addEventListener("click", function() {
        selectedCell = this;

        modalTitle.textContent =
            days[column] + "・" + timings[row] + "の時刻設定";

        timeInput.value = this.dataset.time || "08:00";

        modal.style.display = "flex";
    });
});

// 決定ボタン
document.getElementById("saveBtn").addEventListener("click", function() {
    if (selectedCell && timeInput.value) {
        selectedCell.dataset.time = timeInput.value;
        selectedCell.textContent = timeInput.value;
    }

    modal.style.display = "none";
});

// 削除ボタン
document.getElementById("deleteBtn").addEventListener("click", function() {
    if (selectedCell) {
        selectedCell.textContent = "";
        delete selectedCell.dataset.time;
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