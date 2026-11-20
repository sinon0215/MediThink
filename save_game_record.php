
<?php
session_start();

header('Content-Type: application/json; charset=UTF-8');

// ログイン確認
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'ログインしてください。'
    ]);
    exit;
}

// POST確認
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => '不正なアクセスです。'
    ]);
    exit;
}

// JSONデータ取得
$data = json_decode(file_get_contents('php://input'), true);

$game_name = $data['game_name'] ?? '';
$score = $data['score'] ?? null;

// ゲーム名確認
$allowed_games = ['suuzisagashi', 'pazul'];

if (!in_array($game_name, $allowed_games, true)) {
    echo json_encode([
        'success' => false,
        'message' => 'ゲーム名が正しくありません。'
    ]);
    exit;
}

// スコア確認
if (!is_int($score) || $score < 0) {
    echo json_encode([
        'success' => false,
        'message' => 'スコアが正しくありません。'
    ]);
    exit;
}

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=medithink_db;charset=utf8mb4',
        'medithink_admin',
        'MediThink123!',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );

    // スコア保存
    $sql = "INSERT INTO game_records
            (user_id, game_name, score)
            VALUES (?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_SESSION['user_id'],
        $game_name,
        $score
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'スコアを保存しました。'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '保存に失敗しました。'
    ]);
}
