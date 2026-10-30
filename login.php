
<?php
$save_path = __DIR__ . '/sessions';

if (!is_dir($save_path)) {
    mkdir($save_path, 0733, true);
}

session_save_path($save_path);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$email = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'メールアドレスとパスワードを入力してください。';
    } else {
        $host = 'localhost';
        $dbname = 'medithink_db';
        $username = 'medithink_admin';
        $password_db = 'MediThink123!';

        try {
            $pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password_db
            );

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare(
                "SELECT * FROM users WHERE email = :email"
            );

            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            

            
            // ユーザーが存在し、パスワードがハッシュ値と一致するか検証する
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];

                // 初回ログインか確認
                if ($user['is_first_login'] == 1) {

                    // 初回ログイン済みに更新
                    $stmt = $pdo->prepare(
                        "UPDATE users SET is_first_login = 0 WHERE id = :id"
                    );
                    $stmt->execute([':id' => $user['id']]);

                    // 設定画面へ移動
                    header('Location: setting_medi.php');
                    exit;

                } else {
                    // 2回目以降はホーム画面へ
                    header('Location: home.php');
                    exit;
                }

            } else {
                $error = 'メールアドレスまたはパスワードが間違っています。';
            }


        } catch (PDOException $e) {
            $error = 'データベースエラーが発生しました。';
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
    <link rel="stylesheet" href="./css/style.css">
</head>

<body class="login-body">

    <main class="login-page">

        <section class="info flex-fill" aria-label="MediThink ログイン画面">

            <div class="logo" aria-label="MediThink">
                <img src="./image/logo.png" height="350px" alt="MediThink">
            </div>

            <h1 class="title-label">ログイン画面</h1>

        </section>

        <section class="container flex-fill">

            <form class="login-form" action="login.php" method="post">

                <div class="form-group">
                    <label class="form-input-label" for="email">
                        メールアドレス
                    </label>

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
                    <label class="form-input-label" for="password">
                        パスワード
                    </label>

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
                    <input
                        class="checkbox-input"
                        type="checkbox"
                        id="show-password"
                    >

                    <span class="checkbox-label">
                        パスワードを表示する
                    </span>
                </label>

                <?php if ($error !== ''): ?>
                    <p class="error-message" role="alert">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

<<<<<<< HEAD
                <!-- <button class="primary-btn btn-outline-primary" type="submit">ログイン</button> -->
                <button type="submit" class="primary-btn">ログイン</button>
=======
                <button class="primary-btn" type="submit">
                    ログイン
                </button>
>>>>>>> 52c06a1 (ガクトセーブ)

                <nav class="login-links" aria-label="ログイン関連リンク">
                    <a class="link" href="signup.php">
                        新規登録はこちら
                    </a>
                </nav>

                <nav class="login-links" aria-label="ログイン関連リンク">
                    <a class="link" href="pass_reset.php">
                        パスワードを忘れた場合
                    </a>
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
