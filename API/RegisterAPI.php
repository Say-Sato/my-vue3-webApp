<?php
// CORS（Vueで呼び出す前提）とJSONヘッダ
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

//DB接続
include('./BlogPDO.php');

// DB接続情報（環境に応じて変更してね）
define('SECRET_KEY', 'are0421'); // AESの鍵（セキュアに保管すべき！）

// POSTデータ取得（JSON形式を処理するよう修正）
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

$loginId = $data['loginId'] ?? null;
$userPassword = $data['password'] ?? null;

// 必須チェック
if (!$loginId || !$userPassword) {
    http_response_code(400);
    echo json_encode([
        'error' => 'loginIdとpasswordは必須です',
        'login_id' => $loginId,
        'password' => $userPassword,
    ]);
    exit;
}

try {
    // DB接続
    $dbh = new PDO($dsn, $user, $password);

    // SQL準備
    $stmt = $dbh->prepare("
    INSERT INTO profile (login_id, password, name)
    VALUES (:login_id, HEX(AES_ENCRYPT(:password, :secret_key)), :name)
    ");

    // バインド＆実行
    $stmt->execute([
        ':login_id' => $loginId,
        ':password' => $userPassword,
        ':secret_key' => SECRET_KEY,
        ':name' => $loginId
    ]);

    // 成功時のレスポンス
    echo json_encode([
        'success' => 'true',
        'login_id' => $loginId,
        'password' => '[ENCRYPTED]',
        'name' => $loginId
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'データベースエラー: ' . $e->getMessage()
    ]);
    exit;
}
