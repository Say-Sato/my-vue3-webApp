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
        $query = "SELECT
        -- illust テーブルの全カラム（エイリアス付き）
        illust.id AS illust_id,
        illust.title AS illust_title,
        illust.body AS illust_body,
        illust.public AS public,
        illust.R18 AS R18,

        -- profile テーブルの必要カラム（エイリアス付き）
        profile.id AS profile_id,
        profile.login_id AS profile_login_id,
        profile.name AS profile_name,
        profile.profile_photo AS profile_photo,

        -- images テーブルのURLのみ（エイリアス付き）
        images.url AS thumbnail_url

        FROM
        illust
        -- profileテーブルとの内部結合
        INNER JOIN profile ON illust.p_id = profile.id
        -- imagesテーブルとの内部結合（サムネイル1枚分）
        INNER JOIN images ON illust.id = images.post_id

        WHERE
        -- sort_orderが0の画像（メイン画像想定）
        images.sort_order = 0
        AND illust.p_id = :id
        ORDER BY illust.created_at DESC;";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // クエリを実行
        $stmt->execute();

        // 結果をすべて取得して$data配列に格納
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($rows) {
            foreach ($rows as $row) {
                $data[] = [
                    "illust_id"         => $row['illust_id'],
                    "illust_title"      => $row['illust_title'],
                    "illust_body"       => $row['illust_body'],
                    "public"            => $row['public'],
                    "R18"               => $row['R18'],
                    "profile_id"        => $row['profile_id'],
                    "profile_login_id"  => $row['profile_login_id'],
                    "profile_name"      => $row['profile_name'],
                    "profile_photo"     => $row['profile_photo'],
                    "thumbnail_url"     => $row['thumbnail_url'],
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
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        // エラーメッセージもJSON形式で返す
        header('Content-Type: application/json', true, 500);
        echo json_encode(["error" => "データベースの接続に失敗しました: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        die();
    } finally {
        // DB接続を閉じる
        $dbh = null; // PDOオブジェクトをnullにすることで接続を閉じる
    }
} else {
    echo json_encode(["error" => "ユーザーIDが入っていません。"], JSON_UNESCAPED_UNICODE);
    die();
}
