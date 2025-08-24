<?php
//DB接続
include('./BlogPDO.php');

// リクエストURIからIDを取得
$requestUri = $_SERVER['REQUEST_URI'];
$id = null;

// URIが'/api/favoriteAPI.php/i_id/u_id/action'形式の場合
if (preg_match('#([^/]+\.php)/(\d+)/(\d+)/(delete|insert)$#', $requestUri, $matches)) {
    $i_id = $matches[2];
    $u_id = $matches[3];          // 数値ID
    $action = $matches[4];      // "order" または "text"
    //echo json_encode(["true" => "actionリクエストを受け取りました。i_id: $i_id, u_id: $u_id, text: $action"], JSON_UNESCAPED_UNICODE);

    try {
        $dbh = new PDO($dsn, $user, $password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->beginTransaction(); // トランザクション開始

        // `EXISTS` を使って「いいね」の存在チェック
        $conditionQuery = "SELECT EXISTS(SELECT 1 FROM favorite WHERE i_id = :i_id AND u_id = :u_id) AS exists_flag";
        $conditionStmt = $dbh->prepare($conditionQuery);
        $conditionStmt->bindParam(':i_id', $i_id);
        $conditionStmt->bindParam(':u_id', $u_id);
        $conditionStmt->execute();

        $conditionResult = $conditionStmt->fetch(PDO::FETCH_ASSOC);
        if ($conditionResult && isset($conditionResult['exists_flag'])) {
            $deveropFrag = (int)$conditionResult['exists_flag']; // 明示的にintにしときなさい♡
        } else {
            $deveropFrag = 114514;
        }


        if ($action === 'delete') {
            // いいねが存在する場合 → 削除
            $query = "DELETE FROM favorite WHERE i_id = :i_id AND u_id = :u_id";
            $stmt = $dbh->prepare($query);
            $stmt->bindParam(':i_id', $i_id);
            $stmt->bindParam(':u_id', $u_id);
            $stmt->execute();
            $msg = "いいねを削除しました。i_id: $i_id, u_id: $u_id, conditionResult: $deveropFrag";
        } elseif (intval($i_id) > 0 && intval($u_id) > 0 && $action == 'insert') {
            // いいねが存在しない場合 → 登録
            $query = "INSERT IGNORE INTO favorite (i_id, u_id) VALUES (:i_id, :u_id)";
            $stmt = $dbh->prepare($query);
            $stmt->bindParam(':i_id', $i_id);
            $stmt->bindParam(':u_id', $u_id);
            $stmt->execute();
            $msg = "いいねを登録しました。i_id: $i_id, u_id: $u_id, conditionResult: $deveropFrag";
        } else {
            // エラーメッセージ
            echo json_encode(["error" => "エラー: 不正な入力。i_id: $i_id, u_id: $u_id"], JSON_UNESCAPED_UNICODE);
            die();
        }

        $dbh->commit(); // コミット（確定）

        // ここで少し待つ（MySQLの反映待ち）
        usleep(300000); // 0.3秒遅延（1000000 = 1秒）

        // 最新の状態を取得
        $checkQuery = "SELECT EXISTS(SELECT 1 FROM favorite WHERE i_id = :i_id AND u_id = :u_id) AS exists_flag";
        $checkStmt = $dbh->prepare($checkQuery);
        $checkStmt->bindParam(':i_id', $i_id);
        $checkStmt->bindParam(':u_id', $u_id);
        $checkStmt->execute();
        $newConditionResult = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($newConditionResult && $newConditionResult['exists_flag']) {
            $status = "いいね済み";
        } else {
            $status = "未いいね";
        }

        echo json_encode(["success" => $msg, "status" => $status], JSON_UNESCAPED_UNICODE);
        die();
    } catch (PDOException $e) {
        $dbh->rollBack(); // エラー発生時にロールバック
        echo json_encode(["error" => "データベースエラー: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        die();
    }
} elseif (preg_match('#/([^/]+\.php)/(\d+)/(\d+)$#', $requestUri, $matches)) {
    $i_id = $matches[2];
    $u_id = $matches[3];
    echo json_encode([
        "true" => "idリクエストを受け取りました。i_id: $i_id, u_id: $u_id"
    ], JSON_UNESCAPED_UNICODE);
    die();
} else {
    echo json_encode(["error" => "リクエストURLの形式が間違っています。"], JSON_UNESCAPED_UNICODE);
    die();
}

// 接続を閉じる
$dbh = null;
