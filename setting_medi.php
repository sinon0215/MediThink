<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediThink | 時間編集</title>
    <link rel="stylesheet" href="./css/setting_medi.css">
</head>
<body class="">
     <?php include 'header.php'; ?>
    <div class="container">
       

            <!-- カレンダー -->
            <main class="main-container-section">
                 <div class="calendar-area">
                    <table class="calendar">

                        <thead>
                            <tr>
                                <th></th>
                                <th>月</th>
                                <th>火</th>
                                <th>水</th>
                                <th>木</th>
                                <th>金</th>
                                <th>土</th>
                                <th>日</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <th>朝</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <th>昼</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td ></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <th>夜</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td ></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <th>寝る前</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td ></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>

                    </table>
                </div>

                <!-- 右側の説明エリア -->
                <div class="calendar-guide">

                    <!-- 吹き出し -->
                    <div class="guide-bubble">
                        カレンダーのマスをクリックして、
                        お薬を飲む時間を設定してね！
                    </div>

                    <!-- キャラクター -->
                    <div class="character">
                        <img src="./image/zilyou_zaiko.png">
                    </div>
                    
                </div>
            </main>

            <!-- 下部ボタン -->
            <footer class="footer">

                <button id="settingBtn" class="setting-btn" type="button">
                    設定する
                </button>
                <button id="resetMedicationBtn" class="setting-btn" type="button" style="background-color: #d9534f; margin-left: 12px;">
                    服薬判定リセット
                </button>

            </footer>
        
    </div>

    
    <!-- 時刻設定ポップアップ -->
<div id="timeModal" class="modal">
    <div class="modal-content">
        <h3 id="modalTitle">時刻設定</h3>

        <p>服薬する時刻を選択してください。</p>

        <div class="time-select">

            <div class="time-wheel" id="hourWheel">
                <div class="wheel-list" id="hourList"></div>
            </div>

            <span class="time-colon">：</span>

            <div class="time-wheel" id="minuteWheel">
                <div class="wheel-list" id="minuteList"></div>
            </div>

        </div>

        <div class="modal-buttons">
            <button type="button" id="deleteBtn">削除</button>
            <button type="button" id="cancelBtn">キャンセル</button>
            <button type="button" id="saveBtn">決定</button>
        </div>
    </div>
</div>

    <script src="js/setting_medi.js"></script>
    <script>
    (() => {
        const resetBtn = document.getElementById('resetMedicationBtn');
        if (!resetBtn) return;

        resetBtn.addEventListener('click', async () => {
            try {
                const res = await fetch('reset_medication_logs.php', {
                    method: 'POST',
                    credentials: 'same-origin'
                });
                const data = await res.json();
                if (!res.ok || !data.ok) {
                    alert('リセットに失敗しました。');
                    return;
                }
                sessionStorage.removeItem('medi_notice_triggered_key');
                sessionStorage.removeItem('medi_record_mode');
                alert('今日の服薬・通知判定をリセットしました！');
            } catch (err) {
                console.error(err);
                alert('通信エラーが発生しました。');
            }
        });
    })();
    </script>
</body>
</html>