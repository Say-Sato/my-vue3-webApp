<?php
//DB接続
include('./BlogPDO.php');

// リクエストURIからIDを取得
$requestUri = $_SERVER['REQUEST_URI'];
$id = null;

// URIが'/api/favoriteGetAPI.php/id'形式の場合
if (preg_match('/php\/(\d+)$/', $requestUri, $matches)) {
    $id = urldecode($matches[1]);
}

try {
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if($id){
        // ログインしているユーザーIDに該当するいいねをすべて取得
        $query = "SELECT i_id FROM favorite WHERE u_id = :u_id ORDER BY liked_times DESC";
        $stmt = $dbh->prepare($query);
        // パラメータをバインド
        $stmt->bindParam(':u_id', $id, PDO::PARAM_INT);
        $stmt->execute();

        //i_idカラムを配列として取得
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN,0);

        echo json_encode(["i_id" => $result], JSON_UNESCAPED_UNICODE);

    } else {
        // すべてのイラストごとにいいね数を取得
        $query = "SELECT i_id, COUNT(u_id) AS liked_count
                FROM favorite
                GROUP BY i_id";
        $stmt = $dbh->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

} catch (PDOException $e) {
    echo json_encode(["error" => "データベースエラー: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    die();
}
// 接続を閉じる
$dbh = null;
?>