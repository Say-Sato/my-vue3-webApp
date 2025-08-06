<?php
// DB接続
include('./BlogPDO.php');
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$token = $input['token'] ?? '';

$response = ['valid' => false];

if ($token !== '') {
    try {
        $dbh = new PDO($dsn, $user, $password);
        
        // SQL準備（ここ！$dbh を使うよう修正）
        $query = "SELECT * FROM temporary_users WHERE token = ? AND expire > NOW()";
        $stmt = $dbh->prepare($query);
        $stmt->execute([$token]);

        if ($stmt->fetch()) {
            $response['valid'] = true;
        }

    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "データベースの接続に失敗しました: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// レスポンス返却
echo json_encode($response, JSON_UNESCAPED_UNICODE);
