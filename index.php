<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Firebase Auth + PHP (CDN)</title>
  <style>
    body { font-family: sans-serif; text-align: center; padding: 3rem; background: #f9f9f9; }
    .card { background: white; padding: 2rem; border-radius: 8px; max-width: 500px; margin: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    button { padding: 12px 24px; font-size: 16px; cursor: pointer; border: none; border-radius: 4px; background: #4285f4; color: white; font-weight: bold; }
    button:hover { background: #3367d6; }
    #result { margin-top: 20px; text-align: left; background: #eee; padding: 10px; border-radius: 4px; font-size: 14px; white-space: pre-wrap; word-break: break-all; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Firebase + PHP 認証 (CDN)</h1>
    <button id="login-btn">Googleでログイン</button>
    <div id="status" style="margin-top: 15px;"></div>
    <div id="result" style="display: none;"></div>
  </div>

  <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.0/firebase-app.js";
    import { getAuth, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/11.0.0/firebase-auth.js";

    // スクリーンショットから取得した設定値
    const firebaseConfig = {
      apiKey: "AIzaSyBcLXgC9uTzuHslvsSHCU-cIE6hPB8FdUs",
      authDomain: "medthink-521e0.firebaseapp.com",
      projectId: "medthink-521e0",
      storageBucket: "medthink-521e0.firebasestorage.app",
      messagingSenderId: "1606499031961",
      appId: "1:606499031961:web:e32662d42354d748bda108"
    };

    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);
    const provider = new GoogleAuthProvider();

    const loginBtn = document.getElementById('login-btn');
    const statusDiv = document.getElementById('status');
    const resultDiv = document.getElementById('result');

    loginBtn.addEventListener('click', async () => {
      statusDiv.innerText = "ログイン処理中...";
      try {
        const result = await signInWithPopup(auth, provider);
        const user = result.user;
        statusDiv.innerText = `Googleログイン成功: ${user.displayName}`;

        const idToken = await user.getIdToken();

        const res = await fetch('verify.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${idToken}`
          }
        });

        const data = await res.json();
        resultDiv.style.display = 'block';
        resultDiv.innerText = "PHPバックエンドからの応答:\n" + JSON.stringify(data, null, 2);

      } catch (error) {
        console.error("エラー:", error);
        statusDiv.innerText = "エラーが発生しました: " + error.message;
      }
    });
  </script>
</body>
</html>
