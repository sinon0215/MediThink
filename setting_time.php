<?php
$save_path = __DIR__ . '/sessions';
if (!is_dir($save_path)) {
    mkdir($save_path, 0733, true);
}
session_save_path($save_path);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error = '';

$host = 'localhost';
$dbname = 'medithink_db';
$username = 'medithink_admin';
$db_password = 'MediThink123!';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // POST送信時の処理（時間の保存：セレクトボックスの時と分を結合）
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $morning_time = (!empty($_POST['morning_h']) && !empty($_POST['morning_m'])) ? $_POST['morning_h'] . ':' . $_POST['morning_m'] . ':00' : null;
        $lunch_time   = (!empty($_POST['lunch_h'])   && !empty($_POST['lunch_m']))   ? $_POST['lunch_h']   . ':' . $_POST['lunch_m']   . ':00' : null;
        $evening_time = (!empty($_POST['evening_h']) && !empty($_POST['evening_m'])) ? $_POST['evening_h'] . ':' . $_POST['evening_m'] . ':00' : null;
        $bedtime_time = (!empty($_POST['bedtime_h']) && !empty($_POST['bedtime_m'])) ? $_POST['bedtime_h'] . ':' . $_POST['bedtime_m'] . ':00' : null;

        $update_stmt = $pdo->prepare("
            UPDATE users 
            SET morning_time = :morning, 
                lunch_time = :lunch, 
                evening_time = :evening, 
                bedtime_time = :bedtime 
            WHERE id = :id
        ");
        $update_stmt->execute([
            ':morning' => $morning_time,
            ':lunch'   => $lunch_time,
            ':evening' => $evening_time,
            ':bedtime' => $bedtime_time,
            ':id'      => $user_id
        ]);

        $success_message = '通知時間を更新しました！';
    }

    // 現在の登録時間をデータベースから取得して分解
    $stmt = $pdo->prepare("SELECT morning_time, lunch_time, evening_time, bedtime_time FROM users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    $morning_h = ''; $morning_m = '';
    if (!empty($user_data['morning_time'])) {
        list($morning_h, $morning_m) = explode(':', $user_data['morning_time']);
    }
    
    $lunch_h = ''; $lunch_m = '';
    if (!empty($user_data['lunch_time'])) {
        list($lunch_h, $lunch_m) = explode(':', $user_data['lunch_time']);
    }
    
    $evening_h = ''; $evening_m = '';
    if (!empty($user_data['evening_time'])) {
        list($evening_h, $evening_m) = explode(':', $user_data['evening_time']);
    }
    
    $bedtime_h = ''; $bedtime_m = '';
    if (!empty($user_data['bedtime_time'])) {
        list($bedtime_h, $bedtime_m) = explode(':', $user_data['bedtime_time']);
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = 'データベースエラーが発生しました。';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediThink | 通知時間編集画面</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/setting.css">
    <style>
        body.login-body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            background-color: #e6f0fa;
        }
        .app-main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 20px;
        }
        .time-card-container {
            background: #fff;
            border: 3px solid #b0c4de;
            border-radius: 15px;
            width: 100%;
            max-width: 600px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .time-row {
            display: flex;
            align-items: center;
            background: #e0f2fe;
            border-radius: 8px;
            padding: 12px 20px;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .time-select {
            font-size: 20px;
            padding: 6px 12px;
            border: 2px solid #b0c4de;
            border-radius: 6px;
            background: #fff;
            color: #1e3a8a;
            font-weight: bold;
            outline: none;
        }
    </style>
</head>
<body>
    <!-- 共通ヘッダーの読み込み（正しく `<?php` に修正） -->
    <?php 
        if (file_exists('header_home.php')) {
        include('header_home.php');
            }
    ?>

    <!-- メインコンテンツエリア -->
    <main class="app-main">
        <div class="time-card-container">
            
            <h2 style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 25px; font-size: 18px; color: #333;">
                時間編集画面
            </h2>

            <?php if ($success_message !== ''): ?>
                <p style="color: green; text-align: center; margin-bottom: 15px; font-weight: bold;">
                    <?= htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <p class="error-message" role="alert" style="margin-bottom: 15px; text-align: center;">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <form action="setting_time.php" method="post">
                <!-- 時間設定リスト（朝・昼・夜・寝る前） -->
                <div>
                    <!-- 朝 -->
                    <div class="time-row">
                        <span style="font-size: 20px; font-weight: bold; color: #333;">朝</span>
                        <div>
                            <select name="morning_h" class="time-select">
                                <option value="">--</option>
                                <?php for($i=0; $i<=23; $i++): $h = sprintf('%02d', $i); ?>
                                    <option value="<?= $h ?>" <?= ($morning_h === $h) ? 'selected' : '' ?>><?= $h ?></option>
                                <?php endfor; ?>
                            </select> 時 
                            <select name="morning_m" class="time-select">
                                <option value="">--</option>
                                <?php for($i=0; $i<=59; $i++): $m = sprintf('%02d', $i); ?>
                                    <option value="<?= $m ?>" <?= ($morning_m === $m) ? 'selected' : '' ?>><?= $m ?></option>
                                <?php endfor; ?>
                            </select> 分
                        </div>
                    </div>

                    <!-- 昼 -->
                    <div class="time-row">
                        <span style="font-size: 20px; font-weight: bold; color: #333;">昼</span>
                        <div>
                            <select name="lunch_h" class="time-select">
                                <option value="">--</option>
                                <?php for($i=0; $i<=23; $i++): $h = sprintf('%02d', $i); ?>
                                    <option value="<?= $h ?>" <?= ($lunch_h === $h) ? 'selected' : '' ?>><?= $h ?></option>
                                <?php endfor; ?>
                            </select> 時 
                            <select name="lunch_m" class="time-select">
                                <option value="">--</option>
                                <?php for($i=0; $i<=59; $i++): $m = sprintf('%02d', $i); ?>
                                    <option value="<?= $m ?>" <?= ($lunch_m === $m) ? 'selected' : '' ?>><?= $m ?></option>
                                <?php endfor; ?>
                            </select> 分
                        </div>
                    </div>

                    <!-- 夜 -->
                    <div class="time-row">
                        <span style="font-size: 20px; font-weight: bold; color: #333;">夜</span>
                        <div>
                            <select name="evening_h" class="time-select">
                                <option value="">--</option>
                                <?php for($i=0; $i<=23; $i++): $h = sprintf('%02d', $i); ?>
                                    <option value="<?= $h ?>" <?= ($evening_h === $h) ? 'selected' : '' ?>><?= $h ?></option>
                                <?php endfor; ?>
                            </select> 時 
                            <select name="evening_m" class="time-select">
                                <option value="">--</option>
                                <?php for($i=0; $i<=59; $i++): $m = sprintf('%02d', $i); ?>
                                    <option value="<?= $m ?>" <?= ($evening_m === $m) ? 'selected' : '' ?>><?= $m ?></option>
                                <?php endfor; ?>
                            </select> 分
                        </div>
                    </div>

                    <!-- 寝る前 -->
                    <div class="time-row" style="margin-bottom: 25px;">
                        <span style="font-size: 20px; font-weight: bold; color: #333;">寝る前</span>
                        <div>
                            <select name="bedtime_h" class="time-select">
                                <option value="">--</option>
                                <?php for($i=0; $i<=23; $i++): $h = sprintf('%02d', $i); ?>
                                    <option value="<?= $h ?>" <?= ($bedtime_h === $h) ? 'selected' : '' ?>><?= $h ?></option>
                                <?php endfor; ?>
                            </select> 時 
                            <select name="bedtime_m" class="time-select">
                                <option value="">--</option>
                                <?php for($i=0; $i<=59; $i++): $m = sprintf('%02d', $i); ?>
                                    <option value="<?= $m ?>" <?= ($bedtime_m === $m) ? 'selected' : '' ?>><?= $m ?></option>
                                <?php endfor; ?>
                            </select> 分
                        </div>
                    </div>
                </div>

                <!-- ボタンエリア -->
                <div style="display: flex; justify-content: center;">
                    <button class="primary-btn" type="submit" style="width: 100%; padding: 15px; font-size: 18px; font-weight: bold; border-radius: 8px;">変更を決定する</button>
                </div>
            </form>

        </div>
    </main>
</body>
</html>