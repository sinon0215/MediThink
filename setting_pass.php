<?php
date_default_timezone_set('Asia/Tokyo');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db_host = 'localhost';
$db_name = 'medithink_db';
$db_user = 'medithink_admin';
$db_pass = 'MediThink123!';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if ($password === '' || $passwordConfirm === '') {
        $error = '新しいパスワードと確認用パスワードを入力してください。';
    } elseif ($password !== $passwordConfirm) {
        $error = '新しいパスワードと確認用パスワードが一致しません。';
    } else {
        try {
            $pdo = new PDO(
                "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
                $db_user,
                $db_pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, (int)$_SESSION['user_id']]);

            header('Location: home.php');
            exit;
        } catch (PDOException $e) {
            error_log('setting_pass error: ' . $e->getMessage());
            $error = 'パスワードの更新に失敗しました。';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediThink | パスワード再設定</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body class="login-body">
    <main class="login-page">
        <!-- 左側：ブランド・タイトルエリア（ログイン画面と完全に統一） -->
        <section class="info flex-fill" aria-label="MediThink パスワード再設定画面">
            <div class="logo" aria-label="MediThink">
                <img src="./image/logo.png" height="350px">
            </div>
            <h1 class="title-label">パスワード再設定画面</h1>
        </section>

        <!-- 右側：フォーム入力エリア -->
        <section class="container flex-fill">
            <form class="login-form" action="setting_pass.php" method="post">
                <div class="form-group">
                    <label class="form-input-label" for="password">新しいパスワード</label>
                    <input
                        class="form-input"
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-input-label" for="password_confirm">確認用パスワード</label>
                    <input
                        class="form-input"
                        type="password"
                        id="password_confirm"
                        name="password_confirm"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <label class="checkbox-row">
                    <input class="checkbox-input" type="checkbox" id="show-password">
                    <span class="checkbox-label">パスワードを表示する</span>
                </label>

                <button class="primary-btn" type="submit">パスワードを再設定</button>
                <button class="primary-btn" type="button" onclick="location.href='./settings.php'">戻る</button>

                <?php if ($error !== ''): ?>
                    <p class="error-message" role="alert" style="margin-top: 15px;">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>
            </form>
        </section>
    </main>

    <script>
        const showPassword = document.getElementById('show-password');
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirm');

        if (showPassword && passwordInput && passwordConfirmInput) {
            showPassword.addEventListener('change', () => {
                const type = showPassword.checked ? 'text' : 'password';
                passwordInput.type = type;
                passwordConfirmInput.type = type;
            });
        }
    </script>
</body>
</html>