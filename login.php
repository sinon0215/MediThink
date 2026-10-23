<?php
// login.php (データベース連携版・平文認証)

// --- データベース設定 ---
$db_host = '127.0.0.1';
$db_name = 'medithink_db';
$db_user = 'medithink_admin';     // 必要に応じて変更してください
$db_pass = 'password123';         // 必要に応じてパスワードを設定してください

$save_path = __DIR__ . '/sessions';
if (!is_dir($save_path)) {
    mkdir($save_path, 0733, true);
}
session_save_path($save_path);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit;
}

$email = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'メールアドレスとパスワードを入力してください。';
    } else {
        try {
            // PDOを使ったデータベース接続
            $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
            $pdo = new PDO($dsn, $db_user, $db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            // データベースからメールアドレスでユーザーを検索
            $stmt = $pdo->prepare('SELECT id, email, password FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // ユーザーが存在し、パスワードが一致するか確認（平文で直接比較）
            if ($user && $password === $user['password']) {
                // セッションハイジャック対策
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];

                header('Location: home.php');
                exit;
            } else {
                $error = 'メールアドレスまたはパスワードが間違っています。';
            }

        } catch (PDOException $e) {
            $error = 'データベース接続エラーが発生しました。';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediThink | ログイン</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
    <main class="login-page">
        <!-- 左側：ブランド・タイトルエリア -->
        <section class="login-brand" aria-label="MediThink ログイン画面">
            <div class="logo" aria-label="MediThink">
                <div class="logo-pill" aria-hidden="true">
                    <span class="logo-pill-green"></span>
                    <span class="logo-pill-white"></span>
                </div>
                <div class="logo-text">MediThink</div>
            </div>

            <h1 class="login-title">ログイン画面</h1>
        </section>

        <!-- 右側：フォーム入力エリア -->
        <section class="login-form-area">
            <form class="login-form" action="login.php" method="post">
                <div class="form-group">
                    <label class="form-input-label" for="email">メールアドレス</label>
                    <input
                        class="form-input"
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                        autocomplete="email"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-input-label" for="password">パスワード</label>
                    <input
                        class="form-input"
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <label class="checkbox-row">
                    <input class="checkbox-input" type="checkbox" id="show-password">
                    <span class="checkbox-label">パスワードを表示する</span>
                </label>

                <?php if ($error !== ''): ?>
                    <p class="error-message" role="alert">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

                <button class="primary-btn" type="submit">ログイン</button>

                <nav class="login-links" aria-label="ログイン関連リンク">
                    <a class="link" href="signup.php">新規登録はこちら</a>
                </nav>
            </form>
        </section>
    </main>

    <script>
        const showPassword = document.getElementById('show-password');
        const passwordInput = document.getElementById('password');

        showPassword.addEventListener('change', () => {
            passwordInput.type = showPassword.checked ? 'text' : 'password';
        });
    </script>
</body>
</html>