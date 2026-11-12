<?php
$save_path = __DIR__ . '/sessions';
if (!is_dir($save_path)) {
    mkdir($save_path, 0733, true);
}
session_save_path($save_path);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$error = '';
$step = 1; // 1: メールアドレス入力, 2: 秘密の質問＆新しいパスワード入力
$email = '';
$secret_question = '';

// POST処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    $host = 'localhost';
    $dbname = 'medithink_db';
    $username = 'medithink_admin';
    $db_password = 'MediThink123!';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ステップ1：メールアドレスの確認と秘密の質問の取得
        if ($action === 'check_email') {
            $email = trim($_POST['email'] ?? '');

            if ($email === '') {
                $error = 'メールアドレスを入力してください。';
            } else {
                $stmt = $pdo->prepare("SELECT id, secret_question FROM users WHERE email = :email");
                $stmt->execute([':email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    if (empty($user['secret_question'])) {
                        $error = 'このアカウントには秘密の質問が設定されていません。管理者にお問い合わせください。';
                    } else {
                        $_SESSION['reset_email'] = $email;
                        $_SESSION['reset_user_id'] = $user['id'];
                        $secret_question = $user['secret_question'];
                        $step = 2;
                    }
                } else {
                    $error = '入力されたメールアドレスは登録されていません。';
                }
            }
        }
        // ステップ2：秘密の質問の答え合わせとパスワード再設定
        elseif ($action === 'reset_password') {
            $email = $_SESSION['reset_email'] ?? '';
            $user_id = $_SESSION['reset_user_id'] ?? '';
            $secret_answer = trim($_POST['secret_answer'] ?? '');
            $new_password = $_POST['password'] ?? '';

            if ($secret_answer === '' || $new_password === '') {
                $error = '秘密の質問の答えと新しいパスワードを入力してください。';
                $step = 2;
                $stmt = $pdo->prepare("SELECT secret_question FROM users WHERE id = :id");
                $stmt->execute([':id' => $user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $secret_question = $user['secret_question'] ?? '';
            } else {
                $stmt = $pdo->prepare("SELECT secret_answer FROM users WHERE id = :id");
                $stmt->execute([':id' => $user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && $secret_answer === $user['secret_answer']) {
                    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
                    $update_stmt->execute([
                        ':password' => $new_hashed_password,
                        ':id' => $user_id
                    ]);

                    unset($_SESSION['reset_email'], $_SESSION['reset_user_id']);
                    $_SESSION['flash_message'] = 'パスワードを再設定しました。新しいパスワードでログインしてください。';
                    header('Location: login.php');
                    exit;
                } else {
                    $error = '秘密の質問の答えが間違っています。';
                    $step = 2;
                    $stmt = $pdo->prepare("SELECT secret_question FROM users WHERE id = :id");
                    $stmt->execute([':id' => $user_id]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    $secret_question = $user['secret_question'] ?? '';
                }
            }
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $error = 'データベースエラーが発生しました。';
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
            <form class="login-form" action="pass_reset.php" method="post">
                
                <?php if ($step === 1): ?>
                    <!-- ステップ1：メールアドレス入力 -->
                    <input type="hidden" name="action" value="check_email">
                    <div class="form-group">
                        <label class="form-input-label" for="email">登録メールアドレス</label>
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
                    <button class="primary-btn" type="submit">次へ（秘密の質問を表示）</button>

                <?php else: ?>
                    <!-- ステップ2：秘密の質問への回答 ＆ 新しいパスワード入力 -->
                    <input type="hidden" name="action" value="reset_password">
                    
                    <div class="form-group">
                        <p class="form-input-label"><strong>秘密の質問：</strong></p>
                        <p style="margin-bottom: 15px; color: #333; font-weight: bold;">
                            <?= htmlspecialchars($secret_question, ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-input-label" for="secret_answer">秘密の質問の答え</label>
                        <input
                            class="form-input"
                            type="text"
                            id="secret_answer"
                            name="secret_answer"
                            required
                        >
                    </div>

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

                    <label class="checkbox-row">
                        <input class="checkbox-input" type="checkbox" id="show-password">
                        <span class="checkbox-label">パスワードを表示する</span>
                    </label>

                    <button class="primary-btn" type="submit">パスワードを再設定</button>
                <?php endif; ?>

                <?php if ($error !== ''): ?>
                    <p class="error-message" role="alert" style="margin-top: 15px;">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

                <nav class="login-links" aria-label="ログイン関連リンク" style="margin-top: 20px;">
                    <a class="link" href="login.php">ログインはこちら</a>
                </nav>

                <nav class="signup-links" aria-label="ログイン関連リンク">
                    <a class="link" href="signup.php">新規登録はこちら</a>
                </nav>
            </form>
        </section>
    </main>

    <script>
        const showPassword = document.getElementById('show-password');
        const passwordInput = document.getElementById('password');

        if (showPassword && passwordInput) {
            showPassword.addEventListener('change', () => {
                passwordInput.type = showPassword.checked ? 'text' : 'password';
            });
        }
    </script>
</body>
</html>