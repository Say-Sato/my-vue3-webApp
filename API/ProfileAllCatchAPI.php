<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

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
        $query = "SELECT * FROM profile WHERE id = :id;";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        // クエリを実行
        $stmt->execute();
        
        // 結果を取得
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $data = [
                "id" => $row['id'],
                "login_id" => $row['login_id'],
                "password" => $row['password'],
                "name" => $row['name'],
                "body" => $row['body'],
                "profile_photo" => $row['profile_photo'],
                "admin" => $row['admin']
            ]; // オブジェクトとして格納
        } else {
            // IDが見つからない場合
        http_response_code(404);
        echo json_encode(["error" => "ID:{$id}のデータが見つかりません。"], JSON_UNESCAPED_UNICODE);
        die();

        }

        // JSON形式で返す
        header('Content-Type: application/json');
        echo json_encode($data);

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
    // IDが指定されていない場合すべてのデータを返す
    try {
    $dbh = new PDO($dsn, $user, $password);
    
    // クエリ: id, login_id, bodyの3つのカラムを選択
    $query = "SELECT * FROM profile;";
    $stmt = $dbh->prepare($query);
    
    // クエリを実行
    $stmt->execute();
    
    // すべての行をループしてカラムの値をオブジェクトとして配列に格納
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            "id" => $row['id'],
            "login_id" => $row['login_id'],
            "password" => $row['password'],
            "name" => $row['name'],
            "body" => $row['body'],
            "profile_photo" => $row['profile_photo'],
            "admin" => $row['admin']
        ]; // オブジェクトとして格納
    }

    // JSON形式で返す
    header('Content-Type: application/json');
    echo json_encode($data);

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
