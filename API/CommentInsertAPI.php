<?php
header('Content-Type: application/json; charset=utf-8');

// DB接続
include('./BlogPDO.php');

try {
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'DB接続失敗', 'message' => $e->getMessage()]);
    exit;
}

// POSTデータ取得（フォーム形式）
if (!isset($_POST['post_id'], $_POST['user_id'], $_POST['body'])) {
    echo json_encode(['error' => '必要なデータが不足しています']);
    exit;
}

$post_id = (int)$_POST['post_id'];
$user_id = (int)$_POST['user_id'];
$body = trim($_POST['body']);

try {
    $stmt = $dbh->prepare("INSERT INTO comments (post_id, user_id, body) VALUES (:post_id, :user_id, :body)");
    $stmt->execute([
        ':post_id' => $post_id,
        ':user_id' => $user_id,
        ':body' => $body,
    ]);

    // 登録結果を取得
    $lastId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE id = :id");
    $stmt->execute([':id' => $lastId]);
    $comment = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'comment' => $comment
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'コメント登録失敗', 'message' => $e->getMessage()]);
}
