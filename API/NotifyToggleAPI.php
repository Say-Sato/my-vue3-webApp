<?php
//DB接続
include('./BlogPDO.php');

// リクエストURIからIDを取得
$requestUri = $_SERVER['REQUEST_URI'];

// URIが'/api/favoriteAPI.php/u_id/f_id/action'形式の場合
if (preg_match('#([^/]+\.php)/(\d+)/(\d+)/(delete|insert)$#', $requestUri, $matches)) {
    $user_id = $matches[2];
    $notify_id = $matches[3];          // 数値ID
    $action = $matches[4];      // "order" または "text"

    if ($user_id == $notify_id) {
        // エラーメッセージ
        echo json_encode(["error" => "エラー: フォローとフォロワーが同じです。follower_id: $user_id, followed_id: $notify_id"], JSON_UNESCAPED_UNICODE);
        die();
    }


    try {
        $dbh = new PDO($dsn, $user, $password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->beginTransaction(); // トランザクション開始

        // `EXISTS` を使って「フォロー」の存在チェック
        $conditionQuery = $conditionQuery = "
            SELECT
            EXISTS(
                SELECT 1 FROM follows WHERE follower_id = :u_id AND followed_id = :n_id
            ) AS follow_exists,
            EXISTS(
                SELECT 1 FROM user_follow_notification_blocks WHERE user_id = :u_id AND target_user_id = :n_id
            ) AS notify_blocked
        ";
        $conditionStmt = $dbh->prepare($conditionQuery);
        $conditionStmt->bindParam(':u_id', $user_id);
        $conditionStmt->bindParam(':n_id', $notify_id);
        $conditionStmt->execute();

        $conditionResult = $conditionStmt->fetch(PDO::FETCH_ASSOC);
        if ($conditionResult && !empty($conditionResult['follow_exists'])) {
            $FollowFrag = (int)$conditionResult['follow_exists'];
            $NotifyFrag = (int)$conditionResult['notify_blocked'];
        } else {
            // エラーメッセージ
            echo json_encode(["error" => "エラー: フォローされていないユーザーです。"], JSON_UNESCAPED_UNICODE);
            die();
        }

        if ((int)$NotifyFrag === 1 && $action === 'delete') {
            $query = "DELETE FROM user_follow_notification_blocks WHERE user_id = :u_id AND target_user_id = :n_id";
            $stmt = $dbh->prepare($query);
            $stmt->bindParam(':u_id', $user_id);
            $stmt->bindParam(':n_id', $notify_id);
            $stmt->execute();
            $msg = "通知をオフにしました。user_id: $user_id, target_user_id: $notify_id, conditionResult: $FollowFrag";
        } elseif ((int)$NotifyFrag === 0 && $action == 'insert') {
            $query = "INSERT INTO user_follow_notification_blocks (user_id, target_user_id) VALUES (:u_id, :n_id)";
            $stmt = $dbh->prepare($query);
            $stmt->bindParam(':u_id', $user_id);
            $stmt->bindParam(':n_id', $notify_id);
            $stmt->execute();
            $msg = "通知をオンにしました。user_id: $user_id, target_user_id: $notify_id, conditionResult: $FollowFrag";
        } else {
            // エラーメッセージ
            echo json_encode(["error" => "エラー: 不正な入力。user_id: $user_id, target_user_id: $notify_id"], JSON_UNESCAPED_UNICODE);
            die();
        }

        $dbh->commit(); // コミット（確定）

        // ここで少し待つ（MySQLの反映待ち）
        usleep(300000); // 0.3秒遅延（1000000 = 1秒）

        // 最新の状態を取得
        $checkQuery = "SELECT EXISTS(SELECT 1 FROM user_follow_notification_blocks WHERE user_id = :u_id AND target_user_id = :n_id) AS notify_blocked";
        $checkStmt = $dbh->prepare($checkQuery);
        $checkStmt->bindParam(':u_id', $user_id);
        $checkStmt->bindParam(':n_id', $notify_id);
        $checkStmt->execute();
        $newConditionResult = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($newConditionResult && $newConditionResult['notify_blocked']) {
            $status = "通知オン";
        } else {
            $status = "通知オフ";
        }

        echo json_encode(["success" => $msg, "status" => $status], JSON_UNESCAPED_UNICODE);
        die();
    } catch (PDOException $e) {
        $dbh->rollBack(); // エラー発生時にロールバック
        echo json_encode(["error" => "データベースエラー: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        die();
    }
} elseif (preg_match('#/([^/]+\.php)/(\d+)/(\d+)$#', $requestUri, $matches)) {
    $user_id = $matches[2];
    $notify_id = $matches[3];
    echo json_encode([
        "true" => "idリクエストを受け取りました。user_id: $user_id, target_user_id: $notify_id"
    ], JSON_UNESCAPED_UNICODE);
    die();
} else {
    echo json_encode(["error" => "リクエストURLの形式が間違っています。"], JSON_UNESCAPED_UNICODE);
    die();
}

// 接続を閉じる
$dbh = null;
