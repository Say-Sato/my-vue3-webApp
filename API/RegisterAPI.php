<?php
// CORS（Vueで呼び出す前提）とJSONヘッダ
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

//DB接続
include('./BlogPDO.php');

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

    $SearchQuery = "SELECT COUNT(*) FROM profile WHERE login_id = :login_id";

    $SearchStmt = $dbh->prepare($SearchQuery);
    $SearchStmt->execute([
        ':login_id' => $loginId
    ]);

    // カウントを取得
    $count = $SearchStmt->fetchColumn();
    if ($count >= 1) {
        echo json_encode(['error' => 'このIDは既に使用されています。']);
        exit;
    }

    $InsertQuery = "
    INSERT INTO profile (login_id, password, name)
    VALUES (:login_id, HEX(AES_ENCRYPT(:password, :secret_key)), :name)
    ";

    $InsertStmt = $dbh->prepare($InsertQuery);

    // バインド＆実行
    $stInsertStmt->execute([
        ':login_id' => $loginId,
        ':password' => $userPassword,
        ':secret_key' => $secretKey,
        ':name' => $loginId
    ]);

    // 最後にINSERTされたIDを取得
    $lastId = (int)$dbh->lastInsertId();

    // notificationSettings.php を呼び出す
    include('./notificationSettings.php');

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
