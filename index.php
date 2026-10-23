<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MediThink | Firebaseログイン</title>
  <style>
    body { font-family: sans-serif; text-align: center; padding: 3rem; background: #f9f9f9; }
    .card { background: white; padding: 2rem; border-radius: 8px; max-width: 500px; margin: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    button { padding: 12px 24px; font-size: 16px; cursor: pointer; border: none; border-radius: 4px; background: #4285f4; color: white; font-weight: bold; }
    button:hover { background: #3367d6; }
    #status { margin-top: 15px; font-weight: bold; color: #333; }
    #result { margin-top: 20px; text-align: left; background: #eee; padding: 10px; border-radius: 4px; font-size: 14px; white-space: pre-wrap; word-break: break-all; display: none; }
  </style>
</head>
<body>
  <div class="card">
    <h1>MediThink ログイン</h1>
    <p style="color: #666; margin-bottom: 20px;">Firebase (Google) 認証</p>
    
    <button id="login-btn">Googleでログイン</button>
    <div id="status"></div>
    <div id="result"></div>

    <div style="margin-top: 25px; border-top: 1px solid #ddd; padding-top: 15px;">
      <a href="login.php" style="color: #4285f4; text-decoration: none; font-size: 14px;">通常ログイン（メール/パスワード）はこちら</a>
    </div>
  </div>

  <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.0/firebase-app.js";
    import { getAuth, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/11.0.0/firebase-auth.js";

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
        statusDiv.innerText = `Google認証成功: ${user.displayName}\nサーバーと通信中...`;

        const idToken = await user.getIdToken();

        const res = await fetch('verify.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${idToken}`
          }
        });

        const data = await res.json();

        if (res.ok && data.status === 'success') {
          statusDiv.innerText = "ログイン成功！ホーム画面へ移動します...";
          
          // 0.8秒後にホーム画面（home.php）へリダイレクト
          setTimeout(() => {
            window.location.href = 'home.php';
          }, 800);
        } else {
          throw new Error(data.message || '認証に失敗しました');
        }

      } catch (error) {
        console.error("エラー:", error);
        statusDiv.innerText = "エラーが発生しました: " + error.message;
      }
    });
  </script>
</body>
</html>