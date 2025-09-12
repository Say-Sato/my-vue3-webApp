<?php
//DB接続
include('./BlogPDO.php');

// リクエストURIからIDを取得
$requestUri = $_SERVER['REQUEST_URI'];

// URIが'/api/favoriteAPI.php/u_id/f_id/action'形式の場合
if (preg_match('#([^/]+\.php)/(\d+)/(\d+)/(delete|insert)$#', $requestUri, $matches)) {
    $user_id = $matches[2];
    $follow_id = $matches[3];          // 数値ID
    $action = $matches[4];      // "order" または "text"

    if ($user_id == $follow_id) {
        // エラーメッセージ
        echo json_encode(["error" => "エラー: フォローとフォロワーが同じです。follower_id: $user_id, followed_id: $follow_id"], JSON_UNESCAPED_UNICODE);
        die();
    }


    try {
        $dbh = new PDO($dsn, $user, $password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->beginTransaction(); // トランザクション開始

        // `EXISTS` を使って「フォロー」の存在チェック
        $conditionQuery = "SELECT EXISTS(SELECT 1 FROM follows WHERE follower_id = :u_id AND followed_id = :f_id) AS exists_flag";
        $conditionStmt = $dbh->prepare($conditionQuery);
        $conditionStmt->bindParam(':u_id', $user_id);
        $conditionStmt->bindParam(':f_id', $follow_id);
        $conditionStmt->execute();

        $conditionResult = $conditionStmt->fetch(PDO::FETCH_ASSOC);
        if ($conditionResult && !empty($conditionResult['exists_flag'])) {
            $FollowFrag = (int)$conditionResult['exists_flag'];
        } else {
            // エラーメッセージ
            echo json_encode(["error" => "エラー: フォローされていないユーザーです。"], JSON_UNESCAPED_UNICODE);
            die();
        }

        if ($action === 'delete') {
            // フォローが存在する場合 → 削除
            $query = "DELETE FROM follows WHERE follower_id = :u_id AND followed_id = :f_id";
            $stmt = $dbh->prepare($query);
            $stmt->bindParam(':u_id', $user_id);
            $stmt->bindParam(':f_id', $follow_id);
            $stmt->execute();
            $msg = "いいねを削除しました。follower_id: $user_id, followed_id: $follow_id, conditionResult: $FollowFrag";
        } elseif (intval($user_id) > 0 && intval($follow_id) > 0 && $action == 'insert') {
            // フォローが存在しない場合 → 登録
            $query = "INSERT IGNORE INTO follows (follower_id, followed_id) VALUES (:u_id, :f_id)";
            $stmt = $dbh->prepare($query);
            $stmt->bindParam(':u_id', $user_id);
            $stmt->bindParam(':f_id', $follow_id);
            $stmt->execute();
            $msg = "いいねを登録しました。follower_id: $user_id, followed_id: $follow_id, conditionResult: $FollowFrag";
        } else {
            // エラーメッセージ
            echo json_encode(["error" => "エラー: 不正な入力。follower_id: $user_id, followed_id: $follow_id"], JSON_UNESCAPED_UNICODE);
            die();
        }

        $dbh->commit(); // コミット（確定）

        // ここで少し待つ（MySQLの反映待ち）
        usleep(300000); // 0.3秒遅延（1000000 = 1秒）

        // 最新の状態を取得
        $checkQuery = "SELECT EXISTS(SELECT 1 FROM follows WHERE follower_id = :u_id AND followed_id = :f_id) AS exists_flag";
        $checkStmt = $dbh->prepare($checkQuery);
        $checkStmt->bindParam(':u_id', $user_id);
        $checkStmt->bindParam(':f_id', $follow_id);
        $checkStmt->execute();
        $newConditionResult = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($newConditionResult && $newConditionResult['exists_flag']) {
            $status = "フォロー中";
        } else {
            $status = "フォロー";
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
    $follow_id = $matches[3];
    echo json_encode([
        "true" => "idリクエストを受け取りました。follower_id: $user_id, followed_id: $follow_id"
    ], JSON_UNESCAPED_UNICODE);
    die();
} else {
    echo json_encode(["error" => "リクエストURLの形式が間違っています。"], JSON_UNESCAPED_UNICODE);
    die();
}

// 接続を閉じる
$dbh = null;
