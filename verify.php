<?php
session_start();

ini_set('display_errors', 0);
header('Content-Type: application/json');

// Composer の自動読み込みチェック
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'vendor/autoload.php が見つかりません。cd /var/www/html && composer require kreait/firebase-php を実行してください。']);
    exit;
}

require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

// 秘密鍵 JSON の存在チェック
$serviceAccountPath = '/etc/firebase/service-account.json';
if (!file_exists($serviceAccountPath)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => '/etc/firebase/service-account.json が見つかりません。Firebase Consoleからダウンロードした秘密鍵JSONを配置してください。']);
    exit;
}

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'トークンが送信されていません']);
    exit;
}

$idTokenString = $matches[1];

try {
    $factory = (new Factory())->withServiceAccount($serviceAccountPath);
    $auth = $factory->createAuth();

    $verifiedIdToken = $auth->verifyIdToken($idTokenString);

    $uid = $verifiedIdToken->claims()->get('sub');
    $email = $verifiedIdToken->claims()->get('email');

    $_SESSION['user_id'] = $uid;
    $_SESSION['user_email'] = $email;

    echo json_encode([
        'status' => 'success',
        'message' => 'PHPバックエンドでのIDトークン検証およびセッション作成に成功しました！',
        'user' => [
            'uid' => $uid,
            'email' => $email
        ]
    ]);

} catch (FailedToVerifyToken $e) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => '無効なトークンです: ' . $e->getMessage()]);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'サーバーエラー: ' . $e->getMessage()]);
}
