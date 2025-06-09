<?php
//ArticleEntry.php

// $tags をカンマ区切りで配列化
$tagList = array_map('trim', explode(',', $tags));

// タグをすべて小文字などに統一しておくと正確な比較ができる（任意）
$tagList = array_filter($tagList, fn($tag) => $tag !== ''); // 空タグ除去

// illust_tags の既存 t_id 一覧を取得（先にやる！）
$existingTagIdsQuery = "SELECT t_id FROM illust_tags WHERE i_id = :i_id";
$existingTagIdsStmt = $dbh->prepare($existingTagIdsQuery);
$existingTagIdsStmt->execute([':i_id' => $illust_id]);

// 既存のタグを検索するクエリ
$query = "SELECT id FROM tags WHERE name = :name";
$stmt = $dbh->prepare($query);

// 新規タグを挿入するクエリ
$insertTagQuery = "INSERT INTO tags (name) VALUES (:name)";
$insertTagStmt = $dbh->prepare($insertTagQuery);

// 挿入クエリなど
$selectTagsQuery = "SELECT t_id FROM illust_tags WHERE i_id = :i_id AND t_id = :t_id";
$selectTagsStmt = $dbh->prepare($selectTagsQuery);
$illustTagsQuery = "INSERT INTO illust_tags (i_id, t_id) VALUES (:i_id, :t_id)";
$illustTagsStmt = $dbh->prepare($illustTagsQuery);

// 編集前のタグID一覧
$existingTagIds = $existingTagIdsStmt->fetchAll(PDO::FETCH_COLUMN);
// 保存するタグID一覧
$tagIds = [];

foreach ($tagList as $tagName) {
    // タグIDの取得
    $stmt->execute([':name' => $tagName]);
    $tag_id = $stmt->fetchColumn();

    if (!$tag_id) {
        // 存在しないならINSERTして取得
        $insertTagStmt->execute([':name' => $tagName]);
        $tag_id = intval($dbh->lastInsertId());
    }

    $tagIds[] = $tag_id;

    // illust_tags テーブルに既に存在するか確認
    $selectTagsStmt->execute([
        ':i_id' => $illust_id,
        ':t_id' => $tag_id
    ]);
    $tagResult = $selectTagsStmt->fetchColumn();

    if (!$tagResult) {
        // 挿入実行
        $result = $illustTagsStmt->execute([
            ':i_id' => $illust_id,
            ':t_id' => $tag_id
        ]);
        if (!$result) {
            echo json_encode([
                "error" => "データの挿入に失敗しました。"
            ], JSON_UNESCAPED_UNICODE);
            die();
        }
    }
}

// ★追加部分：削除処理
//array_diff = 配列の比較をして違うものを結果として抽出する
$tagIdsToDelete = array_diff($existingTagIds, $tagIds);
if (!empty($tagIdsToDelete)) {
    $deleteQuery = "DELETE FROM illust_tags WHERE i_id = :i_id AND t_id = :t_id";
    $deleteStmt = $dbh->prepare($deleteQuery);

    foreach ($tagIdsToDelete as $deleteId) {
        $deleteStmt->execute([
            ':i_id' => $illust_id,
            ':t_id' => $deleteId
        ]);
    }
}
?>
