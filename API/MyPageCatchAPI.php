<?php
include('./BlogPDO.php');

// リクエストURIからIDを取得
$requestUri = $_SERVER['REQUEST_URI'];
$id = null;

// URIが'/api/MyPageCatchAPI.php/id'形式の場合
if (preg_match('/\/(\d+)$/', $requestUri, $matches)) {
    $id = intval($matches[1]);
}

$data = []; // 配列の初期化

if ($id !== null) {
    try {
        $dbh = new PDO($dsn, $user, $password);
        
        // 指定されたIDのレコードを選択
        $query = "SELECT * FROM illust WHERE p_id = :id ORDER BY created DESC;";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        // クエリを実行
        $stmt->execute();
        
        // 結果をすべて取得して$data配列に格納
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($rows) {
            foreach ($rows as $row) {
                $data[] = [
                    "id" => $row['id'],
                    "p_id" => $row['p_id'],
                    "title" => $row['title'],
                    "tag" => $row['tag'],
                    "url" => $row['url'],
                    "body" => $row['body'],
                    "R18" => $row['R18'],
                    "public" => $row['public'],
                    "s_url" => $row['s_url'],
                ];
            }
        } else {
            // IDが見つからない場合
            http_response_code(404);
            echo json_encode(["error" => "ユーザーID:{$id}のデータが見つかりません。"], JSON_UNESCAPED_UNICODE);
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
    echo json_encode(["error" => "ユーザーIDが入っていません。"], JSON_UNESCAPED_UNICODE);
    die();
}
?>
