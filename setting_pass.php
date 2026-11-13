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
                        <label class="form-input-label" for="password">確認用パスワード</label>
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
                    <button class="primary-btn" type="submit" onclick="location.href='./settings.php'">戻る</button>
                    
                <?php endif; ?>

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

        if (showPassword && passwordInput) {
            showPassword.addEventListener('change', () => {
                passwordInput.type = showPassword.checked ? 'text' : 'password';
            });
        }
    </script>
</body>
</html>