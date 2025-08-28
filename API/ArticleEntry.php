<?php
// DB接続
header('Content-Type: application/json; charset=utf-8');
include('./BlogPDO.php');

try {
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "INSERT INTO illust (p_id, title, url, body, R18, public, s_url) 
                VALUES (:p_id, :title, :url, :body, :R18, :public, :s_url)";
    $stmt = $dbh->prepare($query);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // 動的パスを指定し画像をURLへ変換
        $subFolder = 'Blog/mypage/portfolio/photo';
        include('./PhotoEntry.php');
        // 入力内容を取得
        $userid = isset($_POST['user_id']) ? htmlspecialchars($_POST['user_id']) : '未指定';
        $title = isset($_POST['illust_title']) ? htmlspecialchars($_POST['illust_title']) : '未指定';
        $tags = isset($_POST['tags']) ? htmlspecialchars($_POST['tags']) : '未指定';
        $body = isset($_POST['ilust_body']) ? htmlspecialchars($_POST['ilust_body']) : '';
        $publish = isset($_POST['public']) ? htmlspecialchars($_POST['public']) : '未指定';
        $adultsOnly = isset($_POST['R18']) ? htmlspecialchars($_POST['R18']) : '未指定';

        //Int型としてDBへ格納
        if ($userid) {
            $p_id = intval($userid);
        } else {
            echo json_encode(["error" => "投稿するにはログインしてください。"], JSON_UNESCAPED_UNICODE);
            die();
        }

        $R18 = (int) $adultsOnly;
        $public = (int) $publish;

        // illustテーブルにINSERT（$savedFiles[0] を使用）
        $data = [
            ':p_id' => $p_id,
            ':title' => $title,
            ':url' => $savedFiles[0],
            ':body' => $body,
            ':R18' => $R18,
            ':public' => $public,
            ':s_url' => $savedFiles[0]
        ];
        $dataList = $stmt->execute($data);

        // illust.id を取得
        $illust_id = intval($dbh->lastInsertId());

        // imagesテーブルへ保存
        $imageInsertQuery = "INSERT INTO images (post_id, url, sort_order) VALUES (:post_id, :url, :sort_order)";
        $imageStmt = $dbh->prepare($imageInsertQuery);

        foreach ($savedFiles as $index => $imgUrl) {
            $imageStmt->execute([
                ':post_id' => $illust_id,
                ':url' => $imgUrl,
                ':sort_order' => $index
            ]);
        }

        // 既存のタグを結びつけ新規のタグを作成し、illust_tagsテーブルに登録する
        include('./TagsInsert.php');

        // 成功レスポンス
        echo json_encode([
            "success" => "データの挿入が完了しました。",
            "タグ名称" => $tagList,
            "タグのID" => $tagIds
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["error" => "POSTリクエストを送信してください。"], JSON_UNESCAPED_UNICODE);
        die();
    }
} catch (PDOException $e) {
    echo json_encode([
        "error" => "データベースエラー: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    die();
}

// 接続を閉じる
$dbh = null;
