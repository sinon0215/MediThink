<?php
$header_notice = $header_notice ?? 'お知らせ';
?>

<link rel="stylesheet" href="css/header.css">

<header class="medithink-header">
    <div class="medithink-logo-area">
        <div class="medithink-logo">
            <img src="image/small_logo.png"  alt="ロゴ">
        </div>
        <a class="medithink-logout-btn" href="home.php">ホームに戻る</a>
    </div>
    <p class="medithink-notice"><?= htmlspecialchars($header_notice, ENT_QUOTES, 'UTF-8') ?></p>
</header>
