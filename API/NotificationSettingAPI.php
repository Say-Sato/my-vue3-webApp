<?php
// ここで $lastId が親ファイルから渡される前提
if (!isset($lastId) || !is_int($lastId)) {
    return; // IDが無効なら何もしない
}

// 既存の user_id を取得
$stmt2 = $dbh->query("SELECT user_id FROM notification_settings");
$existingUserIds = $stmt2->fetchAll(PDO::FETCH_COLUMN);

// $lastId が存在しなければINSERT
if (!in_array($lastId, $existingUserIds)) {
    $insertStmt = $dbh->prepare("INSERT INTO notification_settings (user_id) VALUES (:user_id)");
    $insertStmt->execute(['user_id' => $lastId]);
}
