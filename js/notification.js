// 全画面共通のバックグラウンド通知監視用 JavaScript
const medTimes = {
    morning: window.medTimesData ? window.medTimesData.morning : "",
    lunch:   window.medTimesData ? window.medTimesData.lunch : "",
    evening: window.medTimesData ? window.medTimesData.evening : "",
    bedtime: window.medTimesData ? window.medTimesData.bedtime : ""
};

let lastNotifiedTime = "";

function checkMedicineTimeGlobal() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const currentTime = `${hours}:${minutes}`;

    if (currentTime === lastNotifiedTime) return;

    if (
        (medTimes.morning && currentTime === medTimes.morning) ||
        (medTimes.lunch && currentTime === medTimes.lunch) ||
        (medTimes.evening && currentTime === medTimes.evening) ||
        (medTimes.bedtime && currentTime === medTimes.bedtime)
    ) {
        lastNotifiedTime = currentTime;
        alert("お薬の時間です！お薬を服用してください!");
    }
}

setInterval(checkMedicineTimeGlobal, 10000);