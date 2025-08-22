<?php
//DB接続
header('Content-Type: application/json; charset=utf-8');
include('./BlogPDO.php');

// リクエストURIからIDを取得
$requestUri = $_SERVER['REQUEST_URI'];
$id = null;

$imageUrl = isset($_POST['thumbnail_url']) ? $_POST['thumbnail_url'] : '未指定';
$title = isset($_POST['illust_title']) ? htmlspecialchars($_POST['illust_title']) : '未指定';
$tags = isset($_POST['tags']) ? htmlspecialchars($_POST['tags']) : '未指定';
$body = isset($_POST['illust_body']) ? htmlspecialchars($_POST['illust_body']) : '';
$publish = isset($_POST['public']) ? htmlspecialchars($_POST['public']) : '未指定';
$adultsOnly = isset($_POST['R18']) ? htmlspecialchars($_POST['R18']) : '未指定';

// URIが'/api/UpdateArticleAPI.php/id'形式の場合
if (preg_match('/\/(\d+)$/', $requestUri, $matches)) {
    $id = intval($matches[1]);
    $illust_id = $id;
}

// DBへ接続
try{
$dbh = new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// クエリ
    $query = "UPDATE illust
              SET title = :title,
                  url = :url,
                  body = :body,
                  R18 = :R18,
                  public = :public,
                  s_url = :s_url
              WHERE id = :id";
    $stmt = $dbh->prepare($query);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && $imageUrl !== '未指定') {
        $R18 = (int) $adultsOnly;
        $public = (int) $publish;

        // パラメータをバインド
        $data = [
        'id' => $id,
        ':title' => $title,
        ':url' => $imageUrl,
        ':body' => $body,
        ':R18' => $R18,
        ':public' => $public,
        ':s_url' => $imageUrl
        ];

    // クエリを実行
        $result = $stmt->execute($data);

        if (!$result) {
            error_log("データ挿入エラー: " . print_r($stmt->errorInfo(), true));
            echo json_encode(["error" => "データの更新に失敗しました。"], JSON_UNESCAPED_UNICODE);
        } else {
            include('./TagsInsert.php');
            echo json_encode(["true" => "データの更新が完了しました。"], JSON_UNESCAPED_UNICODE);
        }

    } else {
        //POSTリクエスト失敗時
        echo json_encode(["error" => "POSTリクエストを送信してください。"], JSON_UNESCAPED_UNICODE);
    }

}catch(PDOException $e){
    echo json_encode(["error" => "データベースの接続に失敗しました。".$e->getMessage()], JSON_UNESCAPED_UNICODE);
    die();
}

// 接続を閉じる
$dbh = null;
?>

