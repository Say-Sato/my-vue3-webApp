<?php
//TagCatchiAPI.php
header('Content-Type: application/json');
include('./BlogPDO.php');

// リクエストURIからタグを取得
$requestUri = $_SERVER['REQUEST_URI'];
$tag = null;

if (preg_match('/php\/(.+)$/', $requestUri, $matches)) {
    $tag = urldecode($matches[1]);
}

$tagList = $tag ? explode(',', $tag) : []; // 空チェック

try {
    // データベース接続
    $dbh = new PDO($dsn, $user, $password);

    // タグの検証とプレースホルダの生成の修正
    if (count($tagList) > 0) {
        $tagConditions = array_fill(0, count($tagList), "(id = ?)");
        $tagQuery = "SELECT id FROM tags WHERE " . implode(" OR ", $tagConditions);
        $tagStmt = $dbh->prepare($tagQuery);
        $tagStmt->execute($tagList);
        $tagIds = $tagStmt->fetchAll(PDO::FETCH_COLUMN); // idカラムのみ取得

        if (empty($tagIds)) {
            echo json_encode(["error" => "該当するタグが見つかりました。"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            die();
        } else {
            echo json_encode(["error" => "該当するタグが見つかりません。"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            die();
        }

        // $sortTagIdsを正しく初期化
        $sortTagIds = [];

        // 検索クエリの処理
        $sortPlaceholders = implode(',', array_fill(0, count($tagList), '?'));
        $sortQuery = "SELECT i_id, GROUP_CONCAT(t_id ORDER BY t_id) AS tag_ids
    FROM illust_tags
    WHERE illust_tags.t_id IN ($sortPlaceholders)
    GROUP BY i_id";
        $sortStmt = $dbh->prepare($sortQuery);
        $sortStmt->execute($tagList);

        $searchIdList = [];
        while ($sortResult = $sortStmt->fetch(PDO::FETCH_ASSOC)) {
            // 正しくタグを分割
            $resultTags = $sortResult['tag_ids'];
            $sortTagIds = $resultTags ? explode(',', $resultTags) : [];

            // 全タグが一致するか確認
            $missingTags = array_diff($tagList, $sortTagIds);
            if (empty($missingTags)) {
                // $searchIdListにi_idを追加
                $searchIdList[] = $sortResult['i_id'];
            }
        }


        // 検索IDが存在する場合の処理
        if (count($searchIdList) > 0) {
            $placeholders = implode(',', array_fill(0, count($searchIdList), '?'));
            $query = "SELECT illust.*, profile.name AS p_name, profile.profile_photo AS p_photo
            FROM illust
            JOIN profile ON illust.p_id = profile.id
            WHERE illust.id IN ($placeholders)
            GROUP BY illust.id
            ORDER BY illust.updated DESC";
            $stmt = $dbh->prepare($query);
            $stmt->execute($searchIdList);
        } else {
            echo json_encode([
                "error" => "該当する記事がありません。",
                "tags" => $searchIdList
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(["error" => "タグが入力されていません。"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} catch (PDOException $e) {
    header('Content-Type: application/json', true, 500);
    echo json_encode(["error" => "データベースの接続に失敗しました: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    die();
}
