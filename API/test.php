<?php
header('Content-Type: application/json');
//DB接続
include('./BlogPDO.php');

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // 1. illust テーブルから id, url を取得
    $stmt = $pdo->query("SELECT id, url FROM illust");
    $illusts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$illusts) {
        echo json_encode(['message' => 'illustテーブルが空っぽよ！？']);
        exit;
    }

    // 2. images テーブルに post_id, url を挿入
    $insert = $pdo->prepare("INSERT INTO images (post_id, url) VALUES (:post_id, :url)");

    $count = 0;
    foreach ($illusts as $illust) {
        $insert->execute([
            ':post_id' => $illust['id'],
            ':url'     => $illust['url']
        ]);
        $count++;
    }

    echo json_encode([
        'message' => "ふふん♪ imagesに {$count} 件ぶち込んでやったわよ♡",
        'inserted_count' => $count
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DBエラーよ！' . $e->getMessage()]);
}
?>