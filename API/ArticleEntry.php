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
        $userid = isset($_POST['userid']) ? htmlspecialchars($_POST['userid']) : '未指定';
        $title = isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '未指定';
        $tags = isset($_POST['tags']) ? htmlspecialchars($_POST['tags']) : '未指定';
        $body = isset($_POST['body']) ? htmlspecialchars($_POST['body']) : '';
        $publish = isset($_POST['publish']) ? htmlspecialchars($_POST['publish']) : '未指定';
        $adultsOnly = isset($_POST['adultsOnly']) ? htmlspecialchars($_POST['adultsOnly']) : '未指定';

        //Int型としてDBへ格納
        if($userid){
            $p_id = intval($userid);
        } else {
            echo json_encode(["error" => "投稿するにはログインしてください。"], JSON_UNESCAPED_UNICODE);
            die();
        }

        $R18 = (int) $adultsOnly;
        $public = (int) $publish;

        // パラメータをバインド
        $data = [
        ':p_id' => $p_id,
        ':title' => $title,
        ':url' => $imageUrl,
        ':body' => $body,
        ':R18' => $R18,
        ':public' => $public,
        ':s_url' => $imageUrl
        ];

        // クエリを実行
        $dataList = $stmt->execute($data);

        // 作成した記事のIDを取得する
        $illust_id = intval($dbh->lastInsertId());

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
    echo json_encode(["error" => "データベースエラー: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    die();
}

// 接続を閉じる
$dbh = null;
?>

