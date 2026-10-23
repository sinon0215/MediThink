<?php
// logout.php

// セッションの保存先を指定（login.php等と合わせるため）
$save_path = __DIR__ . '/sessions';
if (is_dir($save_path)) {
    session_save_path($save_path);
}

session_start();

// セッション変数をすべて解除
$_SESSION = [];

// セッションを破壊
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// ログイン画面へリダイレクト
header('Location: login.php');
exit;