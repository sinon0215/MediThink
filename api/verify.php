<?php
require __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

header('Content-Type: application/json');

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['error' => 'トークンが送信されていません']);
    exit;
}

$idTokenString = $matches[1];

try {
    $factory = (new Factory())->withServiceAccount('/etc/firebase/service-account.json');
    $auth = $factory->createAuth();

    $verifiedIdToken = $auth->verifyIdToken($idTokenString);

    $uid = $verifiedIdToken->claims()->get('sub');
    $email = $verifiedIdToken->claims()->get('email');

    echo json_encode([
        'status' => 'success',
        'message' => 'PHPバックエンドでのIDトークン検証に成功しました！',
        'user' => [
            'uid' => $uid,
            'email' => $email
        ]
    ]);

} catch (FailedToVerifyToken $e) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => '無効なトークンです: ' . $e->getMessage()
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'サーバーエラー: ' . $e->getMessage()
    ]);
}
