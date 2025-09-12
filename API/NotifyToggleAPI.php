<?php
//DB接続
include('./BlogPDO.php');

// リクエストURIからIDを取得
$requestUri = $_SERVER['REQUEST_URI'];

// URIが'/api/NotifyToggleAPI.php/u_id/n_id/action'形式の場合
if (preg_match('#([^/]+\.php)/(\d+)/(\d+)/(off|on)$#', $requestUri, $matches)) {
    $user_id = $matches[2];
    $notify_id = $matches[3];          // 数値ID
    $action = $matches[4];      // "order" または "text"

    if ($user_id == $notify_id) {
        // エラーメッセージ
        echo json_encode(["error" => "エラー: 同じユーザーです。follower_id: $user_id, followed_id: $notify_id"], JSON_UNESCAPED_UNICODE);
        die();
    }


    try {
        $dbh = new PDO($dsn, $user, $password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->beginTransaction(); // トランザクション開始

        // `EXISTS` を使って「フォロー」の存在チェック
        $conditionQuery = "SELECT EXISTS(SELECT 1 FROM follows WHERE follower_id = :u_id AND followed_id = :n_id) AS exists_flag";
        $conditionStmt = $dbh->prepare($conditionQuery);
        $conditionStmt->bindParam(':u_id', $user_id);
        $conditionStmt->bindParam(':n_id', $notify_id);
        $conditionStmt->execute();

        $conditionResult = $conditionStmt->fetch(PDO::FETCH_ASSOC);
        if ($conditionResult && !empty($conditionResult['exists_flag'])) {
            $FollowFrag = (int)$conditionResult['exists_flag'];
        } else {
            // エラーメッセージ
            echo json_encode(["error" => "エラー: フォローされていないユーザーです。"], JSON_UNESCAPED_UNICODE);
            die();
        }

        if ($FollowFrag) {
            if ($action === 'off') {
                $query = "UPDATE notification_settings SET notify_follow = 0 WHERE user_id = :u_id";
                $stmt = $dbh->prepare($query);
                $stmt->bindParam(':u_id', $user_id);
                $stmt->execute();
                $msg = "フォローユーザーの投稿通知をオフにしました。user_id: $user_id, conditionResult: $FollowFrag";
            } elseif ($action == 'on') {
                $query = "UPDATE notification_settings SET notify_follow = 1 WHERE user_id = :u_id";
                $stmt = $dbh->prepare($query);
                $stmt->bindParam(':u_id', $user_id);
                $stmt->execute();
                $msg = "フォローユーザーの投稿通知をオンにしました。user_id: $user_id, conditionResult: $FollowFrag";
            } else {
                // エラーメッセージ
                echo json_encode(["error" => "エラー: 不正な入力。follower_id: $user_id, followed_id: $notify_id"], JSON_UNESCAPED_UNICODE);
                die();
            }
        }

        $dbh->commit(); // コミット（確定）

        // ここで少し待つ（MySQLの反映待ち）
        usleep(300000); // 0.3秒遅延（1000000 = 1秒）

        // 最新の状態を取得
        $checkQuery = "SELECT notify_follow FROM notification_settings WHERE user_id = :u_id";
        $checkStmt = $dbh->prepare($checkQuery);
        $checkStmt->bindParam(':u_id', $user_id);
        $checkStmt->execute();
        $newConditionResult = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($newConditionResult) {
            if ((int)$newConditionResult['notify_follow'] === 1) {
                $status = "通知オン";
            } elseif ((int)$newConditionResult['notify_follow'] === 0) {
                $status = "通知オフ";
            }
        } else {
            // レコードが存在しない場合の扱い
            // エラーメッセージ
            echo json_encode(["error" => "エラー: 値を取得できませんでした。"], JSON_UNESCAPED_UNICODE);
            die();
        }

        echo json_encode(["success" => $msg, "status" => $status], JSON_UNESCAPED_UNICODE);
        die();
    } catch (PDOException $e) {
        $dbh->rollBack(); // エラー発生時にロールバック
        echo json_encode(["error" => "データベースエラー: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        die();
    }
} else {
    echo json_encode(["error" => "リクエストURLの形式が間違っています。"], JSON_UNESCAPED_UNICODE);
    die();
}

// 接続を閉じる
$dbh = null;
