<?php
$conditions = [];

foreach ($tagIds as $tag) {
    // 各タグに対してOR条件を適用（タグのいずれかが該当することを確認）
    $conditions[] = "(t_id = '$tag')";
}

// i_id = $illust_id の条件を追加（AND 条件に変更）
$conditions[] = "i_id = $illust_id";

// OR条件で結合（タグのいずれかが該当し、i_id が一致するものを検索）
$searchIllustTagsQuery = "SELECT t_id FROM illust_tags
        WHERE i_id = $illust_id AND (" . implode(' OR ', $conditions) . ")";

$searchIllustTagsStmt = $dbh->prepare($searchIllustTagsQuery);
$searchIllustTagsStmt->execute();

// 結果取得（複数のタグIDがあるのでfetchAllを使用）
$tagResult = $searchIllustTagsStmt->fetchAll(PDO::FETCH_COLUMN);

// 該当しないタグを格納する配列
$tagsNotInResult = [];

// 配列に格納する条件
foreach ($tagIds as $tag) {
    // $tagResult に該当しない場合に格納
    if (!in_array($tag, $tagResult)) {
        $tagsNotInResult[] = $tag;
    }
}
?>