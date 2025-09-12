<?php
//DB接続
include('./BlogPDO.php');

// リクエストURIからIDを取得
$requestUri = $_SERVER['REQUEST_URI'];

// URIが'/api/FollowGetAPI.php/user_id/notify_id'形式の場合
if (preg_match('#([^/]+\.php)/(\d+)/(\d+)$#', $requestUri, $matches)) {
    $user_id = $matches[2];
    $notify_id = $matches[3];
} else {
    echo json_encode(["error" => "パラメータの値が不正です。"], JSON_UNESCAPED_UNICODE);
    die();
}

try {
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!empty($user_id) && !empty($notify_id)) {
        $query = "SELECT EXISTS(SELECT 1 FROM user_follow_notification_blocks WHERE user_id = :u_id AND target_user_id = :n_id) AS notify_frag";
        $stmt = $dbh->prepare($query);
        // パラメータをバインド
        $stmt->bindParam(':u_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':n_id', $notify_id, PDO::PARAM_INT);
        $stmt->execute();

        // notify_fragを取得
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $NotifyFrag = ((int)$result['notify_frag'] === 0 ? 0 : 1);

        echo json_encode([
            "success" => true,
            "user_id" => $user_id,
            "notify_id" => $notify_id,
            "NotifyFrag" => $NotifyFrag
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["error" => "必要なパラメータが設定されていません。"], JSON_UNESCAPED_UNICODE);
        die();
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "データベースエラー: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    die();
}
// 接続を閉じる
$dbh = null;
