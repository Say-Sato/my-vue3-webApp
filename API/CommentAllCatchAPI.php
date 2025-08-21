<?php
header('Content-Type: application/json; charset=utf-8');

// DB接続
include('./BlogPDO.php');

// リクエストURIからIDを取得
$requestUri = $_SERVER['REQUEST_URI'];
$id = null;

try {
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'DB接続失敗', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}

// URIが'/api/CommentAllCatchAPI.php/id'形式の場合
if (preg_match('/php\/(\d+)$/', $requestUri, $matches)) {
    $id = urldecode($matches[1]);
}

if ($id) {
    try {
        $stmt = $dbh->prepare("SELECT * FROM comments WHERE post_id = :post_id");
        $stmt->execute([
            ':post_id' => $id,
        ]);
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'comment' => $result
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'コメント取得失敗', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
} else {
    try {
        $stmt = $dbh->prepare("SELECT * FROM comments");
        $stmt->execute([
        ]);
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'comment' => $result
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'コメント取得失敗', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
}
?>