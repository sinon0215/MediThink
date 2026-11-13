<?php
// エラー表示（開発用）
ini_set('display_errors', 1);
error_reporting(E_ALL);

$email = '';
$secret_answer = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $secret_answer = trim($_POST['secret_answer'] ?? '');

    // バリデーション
    if ($email === '' || $password === '' || $secret_answer === '') {
        $error = 'すべての項目を入力してください。';
    } elseif (strlen($password) < 6) {
        $error = 'パスワードは6文字以上で入力してください。';
    } elseif (strlen($secret_answer) !== 8 || !is_numeric($secret_answer)) {
        $error = '秘密の質問（生年月日）は半角数字8桁で入力してください（例: 19950401）。';
    } else {
        $host = 'localhost';
        $dbname = 'medithink_db';
        $username = 'medithink_admin'; 
        $password_db = 'MediThink123!'; 

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password_db);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // すでに登録されているメールアドレスか確認
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $checkStmt->execute([':email' => $email]);
            
            if ($checkStmt->fetch()) {
                $error = 'このメールアドレスはすでに登録されています。';
            } else {
                // パスワードをハッシュ化する
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // データベースに登録（秘密の質問と答えも一緒に保存）
                $sql = "INSERT INTO users (email, password, secret_question, secret_answer) VALUES (:email, :password, :secret_question, :secret_answer)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':email' => $email,
                    ':password' => $hashed_password,
                    ':secret_question' => '生年月日8桁',
                    ':secret_answer' => $secret_answer
                ]);

                header('Location: login.php');
                exit;
            }

        } catch (PDOException $e) {
            $error = 'データベースエラー: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediThink | 新規登録</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body class="login-body">
    <main class="login-page">
        <section class="info flex-fill" aria-label="MediThink 新規登録画面">
            <div class="logo" aria-label="MediThink">
                <<img src="./image/logo.png" height="350px">
            </div>
            <h1 class="title-label">新規登録画面</h1>
        </section>

        <section class="container flex-fill">
            <form class="login-form" action="signup.php" method="post">
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
                    <label class="form-input-label" for="password">パスワード（6文字以上）</label>
                    <input
                        class="form-input"
                        type="password"
                        id="password"
                        name="password"
                        required
                    >
                </div>

                <!-- 秘密の質問（生年月日8桁）の入力欄を追加 -->
                <div class="form-group">
                    <label class="form-input-label" for="secret_answer">秘密の質問：生年月日（半角数字8桁）</label>
                    <input
                        class="form-input"
                        type="text"
                        id="secret_answer"
                        name="secret_answer"
                        placeholder="例: 19950401"
                        maxlength="8"
                        inputmode="numeric"
                        pattern="\d{8}"
                        value="<?= htmlspecialchars($secret_answer, ENT_QUOTES, 'UTF-8') ?>"
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

                <button class="primary-btn" type="submit">登録</button>

                <nav class="login-links" aria-label="ログイン関連リンク">
                    <a class="link" href="login.php">アカウントをお持ちの方はこちら</a>
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