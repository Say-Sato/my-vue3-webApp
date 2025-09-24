<?php

header("Content-Type: application/json; charset=utf-8");
include('./BlogPDO.php');

// リクエストURIからIDを取得
$requestUri = $_SERVER['REQUEST_URI'];
$id = null;

if (preg_match('/\/(\d+)$/', $requestUri, $matches)) {
    $id = intval($matches[1]);
}
if (is_null($id)) {
    echo json_encode(["error" => "IDが入力されていません。"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 入力値の取得（nullチェックのみ行う）
    $inputData = [
        'notify_comment'          => array_key_exists('comment', $_POST) ? $_POST['comment'] : null,
        'notify_follow'      => array_key_exists('follow', $_POST) ? $_POST['follow'] : null,
        'notify_favorite' => array_key_exists('favorite', $_POST) ? $_POST['favorite'] : null,
        'notify_illust'     => array_key_exists('illust', $_POST) ? $_POST['illust'] : null,
    ];

    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $updates = [];
    $params = [];

    foreach ($inputData as $column => $value) {
        if (!is_null($value)) {
            $updates[] = "$column = :$column";
            $params[$column] = $value;
        }
    }

    if (!empty($updates) && $id !== null && $id > 0) {
        $params['id'] = $id;
        $sql = "UPDATE notification_settings SET " . implode(', ', $updates) . " WHERE user_id = :id";

        // 💡SQLデバッグ用（バインドされた値をSQLに埋め込んで表示）
        $interpolatedSql = $sql;
        foreach ($params as $key => $val) {
            $escapedVal = is_null($val) ? 'NULL' : $dbh->quote($val);
            $interpolatedSql = str_replace(":$key", $escapedVal, $interpolatedSql);
        }

        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute($params);
            echo json_encode([
                "message" => "通知を更新しました。",
                "query" => $interpolatedSql
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } catch (PDOException $e) {
            echo json_encode(["error" => $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        exit;
    } else {
        echo json_encode(["error" => "更新対象がありません。"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
} else {
    echo json_encode(["error" => "POSTリクエストを送信してください。"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
