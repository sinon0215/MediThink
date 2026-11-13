
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediThink | 時間編集</title>
    <link rel="stylesheet" href="./css/setting_medi.css">
</head>
<body class="">
     <?php include 'header_home.php'; ?>
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
                    <img src="./image/zilyou_zaiko.png" height="150px">

                </div>
            </main>

            <!-- 下部ボタン -->
            <footer class="footer">

                <button class="help-btn" type="button">
                    ?
                </button>

                <button id="settingBtn" class="setting-btn" type="button">
                    設定する
                </button>

            </footer>
        
    </div>

    
    <!-- 時刻設定ポップアップ -->
    <div id="timeModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle">時刻設定</h3>

            <p>服薬する時刻を選択してください。</p>

            <input type="time" id="timeInput" value="08:00">

            <div class="modal-buttons">
                <button type="button" id="deleteBtn">削除</button>
                <button type="button" id="cancelBtn">キャンセル</button>
                <button type="button" id="saveBtn">決定</button>
            </div>
        </div>
    </div>

    


  
    <script src="js/setting_medi.js"></script>
</body>
</html>

