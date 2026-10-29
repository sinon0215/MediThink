<?php
$header_notice = $header_notice ?? 'お知らせ';
?>

<link rel="stylesheet" href="header.css">

<header class="medithink-header">
    <div class="medithink-logo-area">
        <div class="medithink-logo">
            <img src="image/small_logo.png"  alt="ロゴ">
        </div>
        <button class="medithink-logout-btn">ログアウト</button>
    </div>
    <p class="medithink-notice"><?= htmlspecialchars($header_notice, ENT_QUOTES, 'UTF-8') ?></p>
</header>
