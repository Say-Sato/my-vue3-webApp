<?php
//DB接続
include('./BlogPDO.php');

// リクエストURIから削除する記事のIDを取得
$requestUri = $_SERVER['REQUEST_URI'];
$id = null;

// URIが'/api/ArticleDeleteAPI.php/id'形式の場合
if (preg_match('/\/(\d+)$/', $requestUri, $matches)) {
    $id = intval($matches[1]);
}

if ($id !== null) {
    try {
        $dbh = new PDO($dsn, $user, $password);
        
        // 指定されたIDのレコードを選択
        $query = // 指定されたIDのレコードを選択
        $query = "DELETE illust, illust_tags, images, favorite, comments
                FROM illust
                LEFT JOIN illust_tags ON illust.id = illust_tags.i_id
                LEFT JOIN images ON illust.id = images.post_id
                LEFT JOIN favorite ON illust.id = favorite.i_id
                LEFT JOIN comments ON illust.id = comments.post_id
                WHERE illust.id = :id";

        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        // クエリを実行
        if ($stmt->execute()) {
            echo json_encode(["true" => "データの削除が完了しました。"], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["error" => "データの削除に失敗しました。"], JSON_UNESCAPED_UNICODE);
        }
    } catch(PDOException $e) {
        // エラーメッセージもJSON形式で返す
        header('Content-Type: application/json', true, 500);
        echo json_encode(["error" => "データベースの接続に失敗しました: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        die();
    } finally {
        // DB接続を閉じる
        $dbh = null; // PDOオブジェクトをnullにすることで接続を閉じる
    }
} else {
    echo json_encode(["error" => "IDが渡されていません。"], JSON_UNESCAPED_UNICODE);
    die();
}
?>

