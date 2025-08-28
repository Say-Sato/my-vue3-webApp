<?php
//DB接続
include('./BlogPDO.php');

// リクエストURIからIDを取得
$requestUri = $_SERVER['REQUEST_URI'];
$id = null;

// URIが'/api/FollowGetAPI.php/id'形式の場合
if (preg_match('/php\/(\d+)$/', $requestUri, $matches)) {
    $id = urldecode($matches[1]);
}

try {
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($id) {
        // ログインしているユーザーIDに該当するフォローを昇順ですべて取得
        $query = "SELECT followed_id FROM follows WHERE follower_id = :u_id ORDER BY created_at DESC";
        $stmt = $dbh->prepare($query);
        // パラメータをバインド
        $stmt->bindParam(':u_id', $id, PDO::PARAM_INT);
        $stmt->execute();

        //followed_idカラムを配列として取得
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        echo json_encode(["followed_id" => $result], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } elseif ($id == 0) {
        // すべてのユーザーごとにフォロワー数、フォロー数を取得
        $query = "SELECT
            p.id AS profile_id,
            COALESCE(f1.follower_ids, '[]') AS follower_ids,
            COALESCE(f2.followed_ids, '[]') AS followed_ids
        FROM profile p
        LEFT JOIN (
            SELECT followed_id, JSON_ARRAYAGG(follower_id) AS follower_ids
            FROM follows
            GROUP BY followed_id
        ) f1 ON p.id = f1.followed_id
        LEFT JOIN (
            SELECT follower_id, JSON_ARRAYAGG(followed_id) AS followed_ids
            FROM follows
            GROUP BY follower_id
        ) f2 ON p.id = f2.follower_id;
        ";
        $stmt = $dbh->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'follows' => $result
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "データベースエラー: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    die();
}
// 接続を閉じる
$dbh = null;
