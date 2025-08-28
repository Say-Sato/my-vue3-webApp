<?php
// DB接続情報
include('./BlogPDO.php');

try {
    $pdo = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    die("DB接続失敗: " . $e->getMessage());
}

// 1. profileテーブルのidをすべて取得
$stmt = $pdo->query("SELECT id FROM profile");
$profileIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

// 2. notification_settingsテーブルに合致するか調べる
// まず既存のuser_idを取得
$stmt = $pdo->query("SELECT user_id FROM notification_settings");
$existingUserIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

// 3. 合致しなかったものを抽出
$toInsertIds = array_diff($profileIds, $existingUserIds);

// 4. 合致しなかったものを順にINSERT
$insertStmt = $pdo->prepare("INSERT INTO notification_settings (user_id) VALUES (:user_id)");

foreach ($toInsertIds as $userId) {
    $insertStmt->execute(['user_id' => $userId]);
}

echo "完了！ " . count($toInsertIds) . " 件のレコードを追加しました。";
