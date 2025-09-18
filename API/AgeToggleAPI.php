<?php
//DB接続
include('./BlogPDO.php');

// リクエストURIからIDを取得
$requestUri = $_SERVER['REQUEST_URI'];

// URIが'/api/favoriteAPI.php/id'形式の場合
if (preg_match('#([^/]+\.php)/(\d+)$#', $requestUri, $matches)) {
    $id = $matches[1];
} else {
    echo json_encode(["error" => "リクエストURLの形式が間違っています。"], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->beginTransaction(); // トランザクション開始

    // birthDate,certificate18の値を確認
    $conditionQuery = "SELECT birthDate, certificate18 FROM profile WHERE id = :id";
    $conditionStmt = $dbh->prepare($conditionQuery);
    $conditionStmt->bindParam(':id', $id);
    $conditionStmt->execute();

    $conditionResult = $conditionStmt->fetch(PDO::FETCH_ASSOC);
    if ($conditionResult) {
        $CertificateFrag = (int)$conditionResult['certificate18'];
    } else {
        // エラーメッセージ
        echo json_encode(["error" => "結果を取得できませんでした。"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 例: MySQLから取得した1ユーザー分のデータ
    $birthDateStr = $conditionResult['birthDate']; // DATE型で入ってる（例: "2007-09-17"）

    if (!$conditionResult['birthDate']) {
        // エラーメッセージ
        echo json_encode(["error" => "誕生日を設定してください。"], JSON_UNESCAPED_UNICODE);
        exit;
    }
    // DateTimeで処理
    $birthDate = new DateTime($birthDateStr);
    $today = new DateTime('today');

    // 年齢を計算（満年齢）
    $age = $birthDate->diff($today)->y;

    if ($CertificateFrag === 0 && $age >= 18) {
        $newCertificate18 = 1;
        $msg = "コンテンツ制限を解除しました。id: $id, conditionResult: $CertificateFrag";
    } else {
        // エラーメッセージ
        echo json_encode(["error" => "エラー: 不正な入力。id: $id"], JSON_UNESCAPED_UNICODE);
        die();
    }
    $query = "UPDATE favorite SET certificate18 = :certificate18";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':certificate18', $newCertificate18);
    $stmt->execute();

    $dbh->commit(); // コミット（確定）

    // ここで少し待つ（MySQLの反映待ち）
    usleep(300000); // 0.3秒遅延（1000000 = 1秒）

    // 最新の状態を取得
    $checkQuery = "SELECT certificate18 FROM profile WHERE id = :id";
    $checkStmt = $dbh->prepare($checkQuery);
    $checkStmt->bindParam(':id', $id);
    $checkStmt->execute();
    $newConditionResult = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($newConditionResult && $newConditionResult['exists_flag']) {
        $status = "成人済み";
    } else {
        $status = "未成年";
    }

    echo json_encode(["success" => $msg, "status" => $status], JSON_UNESCAPED_UNICODE);
    die();
} catch (PDOException $e) {
    $dbh->rollBack(); // エラー発生時にロールバック
    echo json_encode(["error" => "データベースエラー: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    die();
}

// 接続を閉じる
$dbh = null;
