<?php
// medication_header_notice.php
date_default_timezone_set('Asia/Tokyo');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$notice_user_id = $_SESSION['user_id'] ?? null;
$notice_today_dow = (int)date('w'); // 0:日, 1:月, 2:火, 3:水, 4:木, 5:金, 6:土
$notice_today_str = date('Y-m-d');
$notice_schedules = [];

$notice_timing_labels = [
    'morning' => '朝',
    'noon'    => '昼',
    'evening' => '夕方',
    'bedtime' => '就寝前'
];

if ($notice_user_id) {
    $notice_db_host = 'localhost';
    $notice_db_name = 'medithink_db';
    $notice_db_user = 'medithink_admin';
    $notice_db_pass = 'MediThink123!';

    try {
        $notice_pdo = new PDO(
            "mysql:host=$notice_db_host;dbname=$notice_db_name;charset=utf8mb4",
            $notice_db_user,
            $notice_db_pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $stmt = $notice_pdo->prepare(
            "SELECT medication_timing, scheduled_time
             FROM medication_schedules
             WHERE user_id = :user_id
               AND day_of_week = :dow
               AND medication_timing NOT IN (
                   SELECT medication_timing
                   FROM medication_logs
                   WHERE user_id = :log_user_id
                     AND taken_date = :taken_date
               )
             ORDER BY scheduled_time ASC"
        );
        $stmt->execute([
            ':user_id'     => $notice_user_id,
            ':dow'         => $notice_today_dow,
            ':log_user_id' => $notice_user_id,
            ':taken_date'  => $notice_today_str
        ]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $timing = $row['medication_timing'];
            $notice_schedules[] = [
                'timing' => $timing,
                'label'  => $notice_timing_labels[$timing] ?? $timing,
                'time'   => substr($row['scheduled_time'], 0, 5)
            ];
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}
?>

<style>
.medi-header-notice {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 9999;
    background-color: #ffeb3b;
    color: #000000;
    padding: 12px 16px;
    justify-content: center;
    align-items: center;
    font-weight: bold;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}
</style>

<div id="medi-header-notice" class="medi-header-notice" style="display: none;" role="alert">
    <span id="medi-header-notice-text"></span>
</div>

<script>
(() => {
    if (window.__mediHeaderNoticeInitialized) return;
    window.__mediHeaderNoticeInitialized = true;

    const schedules = <?= json_encode($notice_schedules, JSON_UNESCAPED_UNICODE) ?>;
    const noticeEl = document.getElementById('medi-header-notice');
    const textEl = document.getElementById('medi-header-notice-text');

    const GAME_TARGET_URL = 'medi_game_start.php';

    const STORAGE_TRIGGERED_KEY = 'medi_notice_triggered_key';
    const STORAGE_RETURN_URL = 'medi_notice_return_url';
    const STORAGE_RECORD_MODE = 'medi_record_mode';

    if (!noticeEl || !textEl || !Array.isArray(schedules) || schedules.length === 0) {
        return;
    }

    let isTransitioning = false;

    function getJstDateAndTime() {
        const now = new Date();
        const jst = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Tokyo' }));
        const yyyy = jst.getFullYear();
        const mm = String(jst.getMonth() + 1).padStart(2, '0');
        const dd = String(jst.getDate()).padStart(2, '0');
        const hh = String(jst.getHours()).padStart(2, '0');
        const min = String(jst.getMinutes()).padStart(2, '0');
        return {
            dateStr: `${yyyy}-${mm}-${dd}`,
            hhmm: `${hh}:${min}`
        };
    }

    function startMedFlow(triggerKey) {
        if (isTransitioning) return;
        isTransitioning = true;

        sessionStorage.setItem(STORAGE_TRIGGERED_KEY, triggerKey);
        sessionStorage.setItem(STORAGE_RECORD_MODE, '1');

        if (!sessionStorage.getItem(STORAGE_RETURN_URL)) {
            sessionStorage.setItem(STORAGE_RETURN_URL, window.location.href || 'home.php');
        }

        setTimeout(() => {
            window.location.href = GAME_TARGET_URL;
        }, 5000);
    }

    function checkNotice() {
        const { dateStr, hhmm } = getJstDateAndTime();
        const matched = schedules.find(item => item.time === hhmm);
        if (!matched) return;

        const key = `${dateStr}_${matched.timing}`;
        if (sessionStorage.getItem(STORAGE_TRIGGERED_KEY) === key) {
            return;
        }

        textEl.textContent = `⏰ 【${matched.label}のお薬時間です！】設定時刻（${matched.time}）になりました。`;
        noticeEl.style.display = 'flex';
        startMedFlow(key);
    }

    checkNotice();
    setInterval(checkNotice, 1000);
})();
</script>