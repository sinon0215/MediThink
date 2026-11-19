<link rel="stylesheet" href="./css/header.css">

<header class="medithink-header">
    <div class="medithink-logo-area">
        <div class="medithink-logo">
            <img src="image/small_logo.png"  alt="ロゴ">
        </div>
        <a class="medithink-logout-btn" id="header-button" href="logout.php">戻る</a>
    </div>
</header>

<script>

    const Path = location.pathname;
    const getPath = Path.split('/MediThink/').slice(1).join('/');

    const headerButton = document.getElementById('header-button');

    console.log('現在のURL:', Path);
    console.log('取得したパス:', getPath);
    console.log('ボタン要素:', headerButton);

    if (getPath === 'home.php') {
        headerButton.textContent = 'ログアウト';
        headerButton.href = 'logout.php';
    } else {
        headerButton.textContent = '戻る';
        headerButton.href = 'javascript:history.back()';
    }

</script>