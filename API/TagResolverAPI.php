<?php
header('Content-Type: application/json; charset=utf-8');
include('./BlogPDO.php');

$requestUri = $_SERVER['REQUEST_URI'];

if (preg_match('/\/(\d+(?:,\d+)*)$/', $requestUri, $matches)) {
    $idString = $matches[1];
    $tagList = array_map('intval', explode(',', $idString));
} else {
    echo json_encode(["error" => "タグの値が渡されていません。"], JSON_UNESCAPED_UNICODE);
    die();
}

try {
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // クエリの条件を作成（プレースホルダ使用）
    $placeholders = implode(',', array_fill(0, count($tagList), '?'));
    $query = "SELECT id, name FROM tags WHERE id IN ($placeholders)";
    $stmt = $dbh->prepare($query);

    // バインドパラメータを設定
    $stmt->execute($tagList);

    // 結果を連想配列に格納 (id => name)
    $tagData = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tagData[$row['id']] = $row['name'];
    }

    // `$tagList` の順番を保持したまま、対応するタグ名を取得
    $tagNames = [];
    foreach ($tagList as $id) {
        $tagNames[] = $tagData[$id] ?? ""; // IDが存在しない場合 ""(空) にする
    }

    // 成功レスポンス
    echo json_encode([
        "success" => "タグの取得に成功しました。",
        "tagName" => implode(',', $tagNames), // カンマ区切りのタグ名
        "tagIds" => $tagList
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(["error" => "データベースエラー: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    die();
}

// 接続を閉じる
$dbh = null;
