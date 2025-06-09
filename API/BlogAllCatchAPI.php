<?php
include('./BlogPDO.php');

// リクエストURIからIDを取得
$requestUri = $_SERVER['REQUEST_URI'];
$id = null;

// URIが'/api/BlogAllCatchAPI.php/id'形式の場合
if (preg_match('/\/(\d+)$/', $requestUri, $matches)) {
    $id = intval($matches[1]);
}

$data = []; // 配列の初期化

if ($id !== null) {
    try {
        $dbh = new PDO($dsn, $user, $password);
        
        // 指定されたIDのレコードを選択
        $query = "SELECT illust.*, 
                profile.name AS p_name,
                profile.profile_photo AS p_photo,
                GROUP_CONCAT(illust_tags.t_id) AS tag_ids
                FROM illust
                JOIN profile ON illust.p_id = profile.id
                LEFT JOIN illust_tags ON illust.id = illust_tags.i_id
                WHERE illust.id = :id
                GROUP BY illust.id
                ORDER BY illust.created DESC";

        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        // クエリを実行
        $stmt->execute();
        
        // 結果を取得
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $data = [
                "id" => $row['id'],
                "p_id" => $row['p_id'],
                "title" => $row['title'],
                "tags" => array_map('intval', explode(',', $row['tag_ids'])),
                "url" => $row['url'],
                "body" => $row['body'],
                "R18" => $row['R18'],
                "public" => $row['public'],
                "s_url" => $row['s_url'],
                "p_name" => $row['p_name'],
                "p_photo" => $row['p_photo'],
            ]; // データを連想配列として格納
        } else {
            // IDが見つからない場合
        http_response_code(404);
        echo json_encode(["error" => "ID:{$id}のデータが見つかりません。"], JSON_UNESCAPED_UNICODE);
        die();
        }

    // JSON形式で返す
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    } catch(PDOException $e) {
        // エラーメッセージもJSON形式で返す
        header('Content-Type: application/json', true, 500);
        echo json_encode(["error" => "データベースの接続に失敗しました: " . $e->getMessage()]);
        die();
    } finally {
        // DB接続を閉じる
        $dbh = null; // PDOオブジェクトをnullにすることで接続を閉じる
    }
} else {
    // IDが指定されていない場合公開に設定されているすべてのデータを返す
    try {
    $dbh = new PDO($dsn, $user, $password);
    
    // クエリ
    $query = "SELECT illust.*, profile.name AS p_name,
    profile.profile_photo AS p_photo,
    GROUP_CONCAT(illust_tags.t_id) AS tag_ids 
    FROM illust 
    JOIN profile ON illust.p_id = profile.id 
    LEFT JOIN illust_tags ON illust.id = illust_tags.i_id 
    WHERE public = 0 
    GROUP BY illust.id
    ORDER BY created DESC;";

    $stmt = $dbh->prepare($query);
    
    // クエリを実行
    $stmt->execute();
    
    // すべての行をループしてカラムの値をオブジェクトとして配列に格納
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            "id" => $row['id'],
            "p_id" => $row['p_id'],
            "title" => $row['title'],
            "tags" => $row['tag_ids'],
            "url" => $row['url'],
            "body" => $row['body'],
            "R18" => $row['R18'],
            "public" => $row['public'],
            "s_url" => $row['s_url'],
            "p_name" => $row['p_name'],
            "p_photo" => $row['p_photo'],
        ]; // オブジェクトとして格納
    }

    // JSON形式で返す
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);


} catch(PDOException $e) {
    // エラーメッセージもJSON形式で返す
    header('Content-Type: application/json', true, 500);
    echo json_encode(["error" => "データベースの接続に失敗しました: " . $e->getMessage()]);
    die();
} finally {
    // DB接続を閉じる
    $dbh = null; // PDOオブジェクトをnullにすることで接続を閉じる
}
}
?>
