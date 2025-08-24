<?php
//DB接続
header('Content-Type: application/json; charset=utf-8');
include('./BlogPDO.php');

// DBへ接続
try {
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // クエリ
    $query = "INSERT INTO illust (p_id, title, tag, url, body, R18, public, s_url) 
                VALUES (:p_id, :title, :tag, :url, :body, :R18, :public, :s_url)";
    $stmt = $dbh->prepare($query);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //入力内容を取得
        include('./PhotoEntry.php');

        //Int型としてDBへ格納
        if ($userid) {
            $p_id = intval($userid);
        } else {
            $p_id = 3;
        }

        $R18 = (int) $adultsOnly;
        $public = (int) $publish;

        // パラメータをバインド
        $data = [
            ':p_id' => $p_id,
            ':title' => $title,
            ':tag' => $tags,
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
            echo json_encode(["error" => "データの挿入に失敗しました。"], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["true" => "データの挿入が完了しました。"], JSON_UNESCAPED_UNICODE);
        }
    } else {
        //POSTリクエスト失敗時
        echo json_encode(["error" => "POSTリクエストを送信してください。"], JSON_UNESCAPED_UNICODE);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "データベースの接続に失敗しました。" . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    die();
}

// 接続を閉じる
$dbh = null;
