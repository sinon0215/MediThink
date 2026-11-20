
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
